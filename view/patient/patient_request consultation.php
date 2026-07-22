<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: http://localhost/clinic1/view/login/login.php");
    exit();
}

require_once 'C:\xampp\htdocs\clinic1\config\Database.php';
require_once 'C:\xampp\htdocs\clinic1\config\DropdownOptions.php';
require_once 'C:\xampp\htdocs\clinic1\model\Patient.php';
require_once 'C:\xampp\htdocs\clinic1\model\Appointment.php';
require_once 'C:\xampp\htdocs\clinic1\model\User.php';
require_once 'C:\xampp\htdocs\clinic1\model\Doctor.php';

$database = new Database();
$conn = $database->connect();
$patient = new Patient($conn);
$appointment = new Appointment($conn);
$userModel = new User($conn);
$doctorModel = new Doctor($conn);

$patient_data = $patient->getPatientByUserId($_SESSION['user_id']);
$profile_complete = $patient->isProfileComplete($patient_data);

$pending_requests = $profile_complete ? $appointment->getPatientCurrentAppointments($patient_data['id']) : null;
$has_active_appointment = $profile_complete ? $appointment->hasActiveAppointment($patient_data['id']) : false;

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Consultation | SwiftCare</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="../css/patient.css">
    <link rel="stylesheet" href="../css/patient_dashboardd.css">
    <link rel="stylesheet" href="../css/booking_calendar.css">
    <?php include_once '../chatbot_widget.php'; ?>
</head>

<body>

    <!-- TOP NAVBAR-->
    <header class="topbar">

        <!-- Logo -->
        <div class="logo">
            <a href="patient_dashboard.php">
                <img src="../../img/logo.png" alt="SwiftCare Logo">
            </a>
            <span>SwiftCare</span>
        </div>

        <!-- Navigation -->
          <nav class="top-nav">
            <a href="patient_dashboard.php">Home</a>
            <a href="patient_request consultation.php"class="active">Request Consultation</a>
            <a href="patient_view_appointment.php">Appointment</a>
            <a href="patient_request medical.php">Request Medical Documents</a>
            <a href="patient_services.php">Services</a>
            <a href="all doctors.php"> All Doctors</a>
        </nav>

        <!-- Profile -->
        <div class="profile-menu">
            <button type="button" class="profile-menu-toggle" onclick="togglePatientMenu(this)">
                <img src="../../img/user.png" class="profile-avatar" alt="Profile">
                <span class="dropdown-arrow">&#9662;</span>
            </button>
            <div class="profile-dropdown">
                <a href="my_profile.php" class="profile-dropdown-item">My Profile</a>
                <a href="http://localhost/clinic1/controller/logoutController.php" class="profile-dropdown-item signout" onclick="return confirm('Logout?')">Sign Out</a>
            </div>
        </div>

    </header>

<main class="main-content">

    <header class="topbar">
        <h1>Schedule Appointment</h1>
    </header>

    <div class="profile-wrap">

    <?php if (!$profile_complete): ?>

        <div class="profile-card">
            <p class="required-note" style="font-size:15px;">
                <span style="color:red">*</span> Please fill up your Patient Profile first before requesting a consultation.
            </p>
            <div class="button-group">
                <a href="patient_profile.php" class="btn-save">Fill Up Patient Profile</a>
            </div>
        </div>

    <?php else: ?>

        <?php if ($pending_requests && $pending_requests->rowCount() > 0): ?>
        <div class="profile-card">
            <h2>Your Pending Requests</h2>
            <p class="subtitle">Here's what you already have scheduled — no need to book again if you see it here.</p>
            <table class="appointments-table">
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
                    <?php while ($row = $pending_requests->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td>Dr. <?php echo htmlspecialchars($row['doctor_name']); ?></td>
                        <td><?php echo $row['complaint'] ? htmlspecialchars($row['complaint']) : '—'; ?></td>
                        <td><?php echo htmlspecialchars($row['purpose']); ?></td>
                        <td><?php echo date('F j, Y', strtotime($row['appointment_date'])); ?></td>
                        <td><?php echo htmlspecialchars($row['consultation_type'] ?: 'In Person'); ?></td>
                        <td><span class="status-badge <?php echo strtolower($row['status']); ?>"><?php echo ucfirst($row['status']); ?></span></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <?php if ($has_active_appointment): ?>

        <div class="profile-card">
            <p class="required-note" style="font-size:15px;">
                <span style="color:red">*</span> You already have an upcoming appointment. Please wait until it's completed or cancelled before booking another one.
            </p>
        </div>

        <?php else: ?>

        <form method="POST" action="http://localhost/clinic1/controller/AppointmentController.php">

            <div class="profile-card">
                <h2>Schedule Appointment</h2>
                <p class="subtitle">Book a consultation with one of our doctors</p>
                <p class="required-note"><span style="color:red">*</span> Required Fields</p>

                <?php if ($booking_error): ?>
                <div class="booking-error-box"><?php echo htmlspecialchars($booking_error); ?></div>
                <?php endif; ?>

                <div class="form-grid">

                    <!-- Patient Name (auto, from profile) -->
                    <div class="field">
                        <label for="patient-name">Patient Name <span style="color:red">*</span></label>
                        <input type="text" id="patient-name" value="<?php echo htmlspecialchars($patient_data['first_name'].' '.$patient_data['last_name']); ?>" readonly>
                    </div>

                    <!-- Complaint (free text) -->
                    <div class="field">
                        <label for="complaint">Complaint <span style="color:red">*</span></label>
                        <input type="text" id="complaint" name="complaint" placeholder="e.g. Headache, Shortness of Breathe" required>
                    </div>

                    <!-- Purpose -->
                    <div class="field">
                        <label for="purpose">Purpose <span style="color:red">*</span></label>
                        <select id="purpose" name="purpose" required>
                            <option value="" disabled selected>Select Purpose</option>
                            <?php foreach (DropdownOptions::PURPOSES as $p): ?>
                            <option value="<?php echo htmlspecialchars($p); ?>"><?php echo htmlspecialchars($p); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Consultation Type -->
                    <div class="field">
                        <label for="consultation_type">Consultation Type <span style="color:red">*</span></label>
                        <select id="consultation_type" name="consultation_type" required>
                            <option value="" disabled selected>Select Consultation Type</option>
                            <?php foreach (DropdownOptions::CONSULTATION_TYPES as $t): ?>
                            <option value="<?php echo htmlspecialchars($t); ?>"><?php echo htmlspecialchars($t); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                </div>

                <hr style="border:none; border-top:1px solid var(--border-color); margin:22px 0;">

                <!-- STEP 1: SPECIALIZATION -->
                <div class="booking-step">
                    <label class="booking-step-label" for="specializationSelect">1. Choose a Specialization <span style="color:red">*</span></label>
                    <select id="specializationSelect" class="booking-select">
                        <option value="">Select Specialization</option>
                        <?php foreach ($specializations as $spec):
                            $specDesc = DropdownOptions::specializationDescription($spec);
                            $optLabel = $specDesc ? ($spec . ' — ' . $specDesc) : $spec;
                        ?>
                        <option value="<?php echo htmlspecialchars($spec); ?>"><?php echo htmlspecialchars($optLabel); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- STEP 2: DOCTOR -->
                <div class="booking-step">
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
                <div class="booking-step">
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
                <div class="booking-step time-slots-wrap" id="timeSlotsWrap">
                    <label class="booking-step-label">4. Choose a Time <span style="color:red">*</span> — <span id="selectedDateLabel"></span></label>
                    <div class="time-slots-grid" id="timeSlotsGrid"></div>
                </div>

                <!-- Hidden fields actually submitted with the form -->
                <input type="hidden" id="appointmentDate" name="appointment_date" required>
                <input type="hidden" id="appointmentTime" name="appointment_time" required>
                <input type="hidden" name="doctor_id" id="doctorIdHidden">

                <br>
                <div class="button-group">
                    <button type="submit" name="bookAppointment" class="btn-save" id="confirmBookingBtn" disabled>Confirm Appointment</button>
                    <a href="patient_dashboard.php" class="btn-cancel">Cancel</a>
                </div>
            </div>

        </form>

        <?php endif; ?>

    <?php endif; ?>

    </div>

</main>

<?php if ($profile_complete && !$has_active_appointment): ?>
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
function togglePatientMenu(btn){
    var menu = btn.closest('.profile-menu');
    var isOpen = menu.classList.contains('open');
    document.querySelectorAll('.profile-menu.open').forEach(function(m){ m.classList.remove('open'); });
    if(!isOpen){ menu.classList.add('open'); }
}
document.addEventListener('click', function(e){
    if(!e.target.closest('.profile-menu')){
        document.querySelectorAll('.profile-menu.open').forEach(function(m){ m.classList.remove('open'); });
    }
});
</script>
</body>
</html>