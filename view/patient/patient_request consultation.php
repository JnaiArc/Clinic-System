<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: http://localhost/clinic1/view/login/login.php");
    exit();
}

require_once 'C:\xampp\htdocs\clinic1\config\Database.php';
require_once 'C:\xampp\htdocs\clinic1\model\Patient.php';
require_once 'C:\xampp\htdocs\clinic1\model\Appointment.php';

$database = new Database();
$conn = $database->connect();
$patient = new Patient($conn);
$appointment = new Appointment($conn);

$patient_data = $patient->getPatientByUserId($_SESSION['user_id']);
$profile_complete = $patient->isProfileComplete($patient_data);

$pending_requests = $profile_complete ? $appointment->getPatientCurrentAppointments($patient_data['id']) : null;
$has_active_appointment = $profile_complete ? $appointment->hasActiveAppointment($patient_data['id']) : false;

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
                <img src="../../img/Bayani.png" class="profile-avatar" alt="Profile">
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

                <div class="form-grid">

                    <!-- Patient Name (auto, from profile) -->
                    <div class="field">
                        <label for="patient-name">Patient Name <span style="color:red">*</span></label>
                        <input type="text" id="patient-name" value="<?php echo htmlspecialchars($patient_data['first_name'].' '.$patient_data['last_name']); ?>" readonly>
                    </div>

                    <!-- Complaint (free text) -->
                    <div class="field">
                        <label for="complaint">Complaint <span style="color:red">*</span></label>
                        <input type="text" id="complaint" name="complaint" placeholder="e.g. Fever, Headache" required>
                    </div>

                    <!-- Date -->
                    <div class="field">
                        <label for="appointmentDate">Date <span style="color:red">*</span></label>
                        <input type="date" id="appointmentDate" name="appointment_date" min="<?php echo $today; ?>" required>
                    </div>

                    <!-- Doctor -->
                    <div class="field">
                        <label for="doctorSelect">Doctor <span style="color:red">*</span></label>
                        <select id="doctorSelect" name="doctor_id" required>
                            <option value="">Select a date first</option>
                        </select>
                    </div>

                    <!-- Time -->
                    <div class="field">
                        <label for="timeSelect">Time <span style="color:red">*</span></label>
                        <select id="timeSelect" name="appointment_time" required>
                            <option value="">Select a doctor first</option>
                        </select>
                    </div>

                    <!-- Purpose -->
                    <div class="field">
                        <label for="purpose">Purpose <span style="color:red">*</span></label>
                        <select id="purpose" name="purpose" required>
                            <option value="Check-up" selected>Check-up</option>
                            <option value="Consultation">Consultation</option>
                            <option value="Follow-up">Follow-up</option>
                            <option value="Vaccination">Vaccination</option>
                        </select>
                    </div>

                    <!-- Consultation Type -->
                    <div class="field">
                        <label for="consultation_type">Consultation Type <span style="color:red">*</span></label>
                        <select id="consultation_type" name="consultation_type" required>
                            <option value="In Person" selected>In Person</option>
                            <option value="Online">Online</option>
                        </select>
                    </div>

                </div>

                <br>
                <div class="button-group">
                    <button type="submit" name="bookAppointment" class="btn-save">Confirm Appointment</button>
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
function getDayName(dateStr) {
    const date = new Date(dateStr + 'T00:00:00');
    const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    return days[date.getDay()];
}

function timeToNumber(time) {
    const match = time.match(/(\d+):(\d+)\s+(AM|PM)/i);
    if (!match) return 0;
    let hour = parseInt(match[1]);
    const ampm = match[3].toUpperCase();
    if (ampm === 'PM' && hour !== 12) hour += 12;
    if (ampm === 'AM' && hour === 12) hour = 0;
    return hour;
}

function generateTimeSlots(startTime, endTime) {
    const slots = [];
    const start = parseInt(startTime);
    const end = parseInt(endTime);
    for (let i = start; i < end; i++) {
        const hour = i > 12 ? i - 12 : i;
        const ampm = i >= 12 ? 'PM' : 'AM';
        slots.push(hour + ':00 ' + ampm);
        slots.push(hour + ':30 ' + ampm);
    }
    return slots;
}

document.getElementById('appointmentDate').addEventListener('change', function() {
    const day = getDayName(this.value);
    const doctorSelect = document.getElementById('doctorSelect');
    const timeSelect = document.getElementById('timeSelect');

    doctorSelect.innerHTML = '<option value="">Loading...</option>';
    timeSelect.innerHTML = '<option value="">Select a doctor first</option>';

    fetch('http://localhost/clinic1/controller/GetDoctors.php?day=' + encodeURIComponent(day))
        .then(response => response.json())
        .then(doctors => {
            doctorSelect.innerHTML = '<option value="">Select Doctor</option>';
            if (doctors.length === 0) {
                doctorSelect.innerHTML = '<option value="">No doctors available</option>';
            } else {
                doctors.forEach(doc => {
                    const option = document.createElement('option');
                    option.value = doc.id;
                    option.text = 'Dr. ' + doc.first_name + ' ' + doc.last_name;
                    option.dataset.start = doc.schedule_time_start;
                    option.dataset.end = doc.schedule_time_end;
                    doctorSelect.appendChild(option);
                });
            }
        });
});

document.getElementById('doctorSelect').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const timeSelect = document.getElementById('timeSelect');

    if (!selectedOption.value) {
        timeSelect.innerHTML = '<option value="">Select a doctor first</option>';
        return;
    }

    const startTime = selectedOption.dataset.start;
    const endTime = selectedOption.dataset.end;
    const slots = generateTimeSlots(timeToNumber(startTime), timeToNumber(endTime));

    timeSelect.innerHTML = '<option value="">Select Time</option>';
    slots.forEach(slot => {
        const option = document.createElement('option');
        option.value = slot;
        option.text = slot;
        timeSelect.appendChild(option);
    });
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
