<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: http://localhost/clinic1/view/login/login.php");
    exit();
}

require_once 'C:\xampp\htdocs\clinic1\config\Database.php';
require_once 'C:\xampp\htdocs\clinic1\model\User.php';
require_once 'C:\xampp\htdocs\clinic1\model\Patient.php';
require_once 'C:\xampp\htdocs\clinic1\model\Appointment.php';

$database = new Database();
$conn = $database->connect();
$user = new User($conn);
$patient = new Patient($conn);
$appointment = new Appointment($conn);

$user_info = $user->getUserById($_SESSION['user_id']);
$patient_data = $patient->getPatientByUserId($_SESSION['user_id']);
$profile_complete = $patient->isProfileComplete($patient_data);

$next_appointment = $patient_data ? $appointment->getNextAppointmentForPatient($patient_data['id']) : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard | SwiftCare</title>

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
            <a href="patient_dashboard.php"class="active">Home</a>
            <a href="patient_request consultation.php">Request Consultation</a>
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

        <!-- WELCOME BANNER -->
        <section class="welcome-banner">
            <div class="welcome-text">
                <h2>Welcome back, <?php echo htmlspecialchars($user_info['first_name']); ?>!</h2>
                <p>Here's what's happening with your care today.</p>
            </div>
            <a href="patient_request consultation.php" class="btn-book">Book a Consultation</a>
        </section>

        <!-- DASHBOARD CONTENT -->
        <section class="dashboard-grid">

            <!-- YOUR NEXT APPOINTMENT -->
            <div class="appointment-card">

                <div class="card-title">
                    <h2>Your Next Appointment</h2>
                </div>

                <?php if ($next_appointment): ?>
                <div class="appointment-details">

                    <div class="detail-row">
                        <span>Appointment Status</span>
                        <strong><?php echo ucfirst($next_appointment['status']); ?></strong>
                    </div>

                    <div class="detail-row">
                        <span>Consultation Date</span>
                        <strong><?php echo date('F j, Y', strtotime($next_appointment['appointment_date'])); ?></strong>
                    </div>

                    <div class="detail-row">
                        <span>Doctor</span>
                        <strong>Dr. <?php echo htmlspecialchars($next_appointment['doctor_name']); ?></strong>
                    </div>

                    <div class="detail-row">
                        <span>Main Complaint</span>
                        <strong><?php echo htmlspecialchars($next_appointment['purpose']); ?></strong>
                    </div>

                    <div class="detail-row">
                        <span>Type</span>
                        <strong><?php echo htmlspecialchars($next_appointment['consultation_type'] ?: 'In Person'); ?></strong>
                    </div>

                    <div class="detail-row">
                        <span>Consultation Time Slot</span>
                        <strong><?php echo htmlspecialchars($next_appointment['appointment_time']); ?></strong>
                    </div>

                </div>
                <?php else: ?>
                <div class="appointment-details">
                    <div class="detail-row">
                        <span>Status</span>
                        <strong>N/A</strong>
                    </div>
                    <p style="color:#64748b; margin-top:10px;">You have no upcoming appointments. Book a consultation to get started.</p>
                </div>
                <?php endif; ?>

            </div>

            <!-- PATIENT PROFILE -->
            <div class="slot-card">

                <p class="pp-eyebrow">Patient Profile</p>

                <?php if ($profile_complete): ?>
                    <h2 class="pp-name"><?php echo htmlspecialchars($patient_data['first_name'].' '.$patient_data['last_name']); ?></h2>
                    <a href="patient_profile.php" class="btn-book" style="margin-top:16px; display:inline-block;">View Profile</a>
                <?php else: ?>
                    <h2 class="pp-name"><?php echo htmlspecialchars($user_info['first_name'].' '.$user_info['last_name']); ?></h2>
                    <p style="color:#dc2626; margin-top:10px;"><span style="color:red">*</span> Please complete your patient profile first before booking a consultation.</p>
                    <a href="patient_profile.php" class="btn-book" style="margin-top:10px; display:inline-block;">Complete Profile</a>
                <?php endif; ?>

            </div>
        </section>
    </main>

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
