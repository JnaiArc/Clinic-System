<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: http://localhost/clinic1/view/login/login.php");
    exit();
}

require_once 'C:\xampp\htdocs\clinic1\config\Database.php';
require_once 'C:\xampp\htdocs\clinic1\config\DropdownOptions.php';
require_once 'C:\xampp\htdocs\clinic1\model\Patient.php';
require_once 'C:\xampp\htdocs\clinic1\model\User.php';
require_once 'C:\xampp\htdocs\clinic1\model\Doctor.php';
require_once 'C:\xampp\htdocs\clinic1\model\Appointment.php';

$database = new Database();
$conn = $database->connect();
$patient = new Patient($conn);
$user = new User($conn);
$doctorModel = new Doctor($conn);
$appointment = new Appointment($conn);

$user_info = $user->getUserById($_SESSION['user_id']);
$patient_id = $_GET['patient_id'] ?? 0;
$selected_patient = $patient_id ? $patient->getPatientById($patient_id) : null;

// Prevent double-booking: check if this patient already has a pending/confirmed appointment
$has_active_appointment = $patient_id ? $appointment->hasActiveAppointment($patient_id) : false;
$existing_appointments = $patient_id ? $appointment->getPatientCurrentAppointments($patient_id) : null;

// Specializations that currently have at least one doctor assigned -> feeds the first booking step
$specializations = $doctorModel->getAllSpecializations();

$booking_error = $_SESSION['booking_error'] ?? "";
unset($_SESSION['booking_error']);

// get date today
$today = date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Consultation</title>
    <link rel="stylesheet" href="../css/admin_dashboard.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/booking_calendar.css">
</head>

<body>

<div class="dashboard-container">
    <aside class="sidebar-simple">
        <a href="http://localhost/clinic1/view/admin/admin_patientRecord.php" class="back-btn-top">
            <div class="icon">←</div>
            <span>Back</span>
        </a>
    </aside>

    <main class="main-content">

        <header class="topbar">
            <div class="topbar-left">
                <div class="clinic-text">
                    <h1>Schedule Appointment</h1>
                </div>
            </div>
            
            <div class="user-menu">
                <button type="button" class="user-menu-toggle" onclick="toggleUserMenu(this)">
                    <?php if(!empty($user_info['profile_photo'])): ?>
                    <img src="../../uploads/<?php echo $user_info['profile_photo']; ?>" class="user-avatar" style="object-fit: cover;">
                    <?php else: ?>
                    <div class="user-avatar"></div>
                    <?php endif; ?>
                    <span class="user-name"><?php echo $_SESSION['name']; ?></span>
                    <span class="user-role-badge">Admin</span>
                    <span class="dropdown-arrow">&#9662;</span>
                </button>
                <div class="user-dropdown">
                    <a href="http://localhost/clinic1/view/admin/my_profile.php" class="dropdown-item">My Profile</a>
                    <a href="http://localhost/clinic1/controller/logoutController.php" class="dropdown-item signout" onclick="return confirm('Logout?')">Sign Out</a>
                </div>
            </div>
        </header>

        <section class="table-section">

            <div class="section-header">Schedule Appointment</div>

            <?php if ($has_active_appointment): ?>

            <div class="booking-error-box" style="margin:20px 25px;">
                <span style="color:red">*</span> This patient already has an upcoming appointment. To avoid double-booking, a new appointment can't be scheduled until it's completed or cancelled.
            </div>

            <?php if ($existing_appointments && $existing_appointments->rowCount() > 0): ?>
            <div style="margin:0 25px 25px;">
                <table class="appointment-table">
                    <thead>
                        <tr>
                            <th>Doctor</th>
                            <th>Complaint</th>
                            <th>Purpose</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $existing_appointments->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td>Dr. <?php echo htmlspecialchars($row['doctor_name']); ?></td>
                            <td><?php echo $row['complaint'] ? htmlspecialchars($row['complaint']) : '—'; ?></td>
                            <td><?php echo htmlspecialchars($row['purpose']); ?></td>
                            <td><?php echo date('F j, Y', strtotime($row['appointment_date'])); ?></td>
                            <td><?php echo htmlspecialchars($row['consultation_type'] ?: 'In Person'); ?></td>
                            <td><span class="status <?php echo strtolower($row['status']); ?>"><?php echo ucfirst($row['status']); ?></span></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

            <div class="view-buttons" style="margin:0 25px 25px;">
                <a href="http://localhost/clinic1/view/admin/admin_patientRecord.php" class="back-link">Back</a>
            </div>

            <?php else: ?>

            <form method="POST" action="http://localhost/clinic1/controller/AppointmentController.php" class="appointment-body">
                
                <div class="form-group">
                    <label>Patient Name</label>
                    <input type="text" value="<?php echo $selected_patient ? $selected_patient['first_name'].' '.$selected_patient['last_name'] : ''; ?>" readonly>
                    <input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
                </div>

                <div class="form-group">
                    <label>Complaint <span style="color:red">*</span></label>
                    <input type="text" name="complaint" placeholder="e.g. Fever, Shortness fo Breathe" required>
                </div>

                <div class="form-group">
                    <label>Purpose <span style="color:red">*</span></label>
                    <select name="purpose" required>
                        <option value="" disabled selected>Select Purpose</option>
                        <?php foreach (DropdownOptions::PURPOSES as $p): ?>
                        <option value="<?php echo htmlspecialchars($p); ?>"><?php echo htmlspecialchars($p); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Consultation Type <span style="color:red">*</span></label>
                    <select name="consultation_type" required>
                        <option value="" disabled selected>Select Consultation Type</option>
                        <?php foreach (DropdownOptions::CONSULTATION_TYPES as $t): ?>
                        <option value="<?php echo htmlspecialchars($t); ?>"><?php echo htmlspecialchars($t); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <?php if ($booking_error): ?>
                <div class="booking-error-box span-2"><?php echo htmlspecialchars($booking_error); ?></div>
                <?php endif; ?>

                <!-- STEP 1: SPECIALIZATION -->
                <div class="form-group span-2 booking-step">
                    <label class="booking-step-label" for="specializationSelect">1. Choose a Specialization <span style="color:red">*</span></label>
                    <select id="specializationSelect" class="booking-select">
                        <option value="" disabled selected>Select Specialization</option>
                        <?php foreach ($specializations as $spec):
                            $specDesc = DropdownOptions::specializationDescription($spec);
                            $optLabel = $specDesc ? ($spec . ' — ' . $specDesc) : $spec;
                        ?>
                        <option value="<?php echo htmlspecialchars($spec); ?>"><?php echo htmlspecialchars($optLabel); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- STEP 2: DOCTOR -->
                <div class="form-group span-2 booking-step">
                    <label class="booking-step-label" for="doctorSelect">2. Choose a Doctor <span style="color:red">*</span></label>
                    <select id="doctorSelect" class="booking-select" disabled>
                        <option value="">Select a specialization first</option>
                    </select>

                    <div class="doctor-summary-card" id="doctorSummary">
                        <img src="../../img/user.png" alt="Doctor" class="doctor-summary-photo" id="doctorSummaryPhoto">
                        <div class="doctor-summary-info">
                            <h4 id="doctorSummaryName">—</h4>
                            <p id="doctorSummarySpec">—</p>
                        </div>
                    </div>
                </div>

                <!-- STEP 3: DATE (CALENDAR) -->
                <div class="form-group span-2 booking-step">
                    <label class="booking-step-label">3. Choose a Date <span style="color:red">*</span></label>
                    <p class="booking-step-hint">Greyed-out dates are either fully booked or outside the doctor's schedule.</p>

                    <div class="booking-calendar" id="bookingCalendar">
                        <div class="calendar-nav">
                            <button type="button" class="calendar-nav-btn" id="calendarPrevBtn">&#8249;</button>
                            <span class="calendar-month-label" id="calendarMonthLabel">—</span>
                            <button type="button" class="calendar-nav-btn" id="calendarNextBtn">&#8250;</button>
                        </div>
                        <div class="calendar-weekdays">
                            <span>Sun</span><span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span>
                        </div>
                        <div class="calendar-grid" id="calendarGrid"></div>
                    </div>
                </div>

                <!-- STEP 4: TIME -->
                <div class="form-group span-2 booking-step time-slots-wrap" id="timeSlotsWrap">
                    <label class="booking-step-label">4. Choose a Time <span style="color:red">*</span> — <span id="selectedDateLabel"></span></label>
                    <div class="time-slots-grid" id="timeSlotsGrid"></div>
                </div>

                <!-- Hidden fields actually submitted with the form -->
                <input type="hidden" id="appointmentDate" name="appointment_date" required>
                <input type="hidden" id="appointmentTime" name="appointment_time" required>
                <input type="hidden" name="doctor_id" id="doctorIdHidden">

                <div class="view-buttons span-2">
                    <button type="submit" name="bookAppointment" class="save-btn" id="confirmBookingBtn" disabled>Confirm Appointment</button>
                    <a href="http://localhost/clinic1/view/admin/admin_patientRecord.php" class="back-link">Cancel</a>
                </div>

            </form>

            <?php endif; ?>

        </section>

    </main>

</div>

<?php if (!$has_active_appointment): ?>
<script>
    window.BookingConfig = {
        getDoctorsUrl: 'http://localhost/clinic1/controller/GetDoctors.php',
        getAvailabilityUrl: 'http://localhost/clinic1/controller/GetAvailability.php',
        uploadsPath: '../../uploads/',
        defaultAvatarPath: '../../img/user.png',
        dateInputId: 'appointmentDate',
        timeInputId: 'appointmentTime'
    };
</script>
<script src="../js/booking.js"></script>
<script>
    // Keep the hidden doctor_id input in sync with the doctor dropdown
    document.getElementById('doctorSelect').addEventListener('change', function () {
        document.getElementById('doctorIdHidden').value = this.value;
    });
</script>
<?php endif; ?>
<script>
function toggleUserMenu(btn){
    var menu = btn.closest('.user-menu');
    var isOpen = menu.classList.contains('open');
    document.querySelectorAll('.user-menu.open').forEach(function(m){ m.classList.remove('open'); });
    if(!isOpen){ menu.classList.add('open'); }
}
document.addEventListener('click', function(e){
    if(!e.target.closest('.user-menu')){
        document.querySelectorAll('.user-menu.open').forEach(function(m){ m.classList.remove('open'); });
    }
});
</script>
</body>
</html>