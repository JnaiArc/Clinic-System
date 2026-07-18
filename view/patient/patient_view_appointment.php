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

$current_appointments   = $patient_data ? $appointment->getPatientCurrentAppointments($patient_data['id'])   : null;
$past_appointments      = $patient_data ? $appointment->getPatientPastAppointments($patient_data['id'])      : null;
$cancelled_appointments = $patient_data ? $appointment->getPatientCancelledAppointments($patient_data['id']) : null;

function renderAppointmentRows($stmt, $showView = true) {
    if (!$stmt || $stmt->rowCount() === 0) {
        $colspan = $showView ? 7 : 6;
        echo '<tr class="empty-row"><td colspan="' . $colspan . '">No appointments found.</td></tr>';
        return;
    }
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $statusClass = strtolower($row['status']);
        echo '<tr>';
        echo '<td>Dr. ' . htmlspecialchars($row['doctor_name']) . '</td>';
        echo '<td>' . ($row['complaint'] ? htmlspecialchars($row['complaint']) : '—') . '</td>';
        echo '<td>' . htmlspecialchars($row['purpose']) . '</td>';
        echo '<td>' . date('F j, Y', strtotime($row['appointment_date'])) . '</td>';
        echo '<td>' . htmlspecialchars($row['consultation_type'] ?: 'In Person') . '</td>';
        echo '<td><span class="status-badge ' . $statusClass . '">' . ucfirst($row['status']) . '</span></td>';
        if ($showView) {
            echo '<td style="text-align:center;"><a href="patient_view_appointment.php?id=' . $row['id'] . '" class="btn-view">View</a></td>';
        }
        echo '</tr>';
    }
}

// Optional single-appointment detail view (?id=)
$view_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$view_appointment = ($view_id && $patient_data) ? $appointment->getPatientAppointmentById($view_id, $patient_data['id']) : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment | SwiftCare</title>

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
            <a href="patient_request consultation.php">Request Consultation</a>
            <a href="patient_view_appointment.php"class="active">Appointment</a>
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
            <h1>Appointment Info</h1>
        </header>

        <div class="appointments-wrap">

            <?php if ($view_appointment): ?>

            <!-- SINGLE APPOINTMENT DETAIL -->
            <div class="profile-card appointment-detail-card">

                <div class="appointment-detail-head">
                    <div>
                        <p class="appointment-detail-eyebrow">Appointment with</p>
                        <h2>Dr. <?php echo htmlspecialchars($view_appointment['doctor_name']); ?></h2>
                    </div>
                    <span class="status-badge <?php echo strtolower($view_appointment['status']); ?> appointment-detail-status">
                        <?php echo ucfirst($view_appointment['status']); ?>
                    </span>
                </div>

                <div class="patient-info-grid">
                    <div class="info-card"><label>Date</label><span><?php echo date('F j, Y', strtotime($view_appointment['appointment_date'])); ?></span></div>
                    <div class="info-card"><label>Time</label><span><?php echo htmlspecialchars($view_appointment['appointment_time']); ?></span></div>
                    <div class="info-card"><label>Complaint</label><span><?php echo $view_appointment['complaint'] ? htmlspecialchars($view_appointment['complaint']) : '—'; ?></span></div>
                    <div class="info-card"><label>Purpose</label><span><?php echo htmlspecialchars($view_appointment['purpose']); ?></span></div>
                    <div class="info-card-full"><label>Consultation Type</label><span><?php echo htmlspecialchars($view_appointment['consultation_type'] ?: 'In Person'); ?></span></div>
                </div>
                <br>
                <div class="button-group">
                    <a href="patient_view_appointment.php" class="btn-cancel">Back to Appointments</a>
                    <?php if (in_array($view_appointment['status'], ['pending', 'confirmed'])): ?>
                    <form method="POST" action="http://localhost/clinic1/controller/AppointmentController.php" style="display:inline;" onsubmit="return confirm('Cancel this appointment?');">
                        <input type="hidden" name="appointment_id" value="<?php echo $view_appointment['id']; ?>">
                        <button type="submit" name="cancelAppointment" class="btn-cancel-appointment">Cancel Appointment</button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>

            <?php else: ?>

            <div class="info-banner">
                <p>Select an appointment that you want to view.</p>
            </div>

            <!-- Tabs Navigation -->
            <div class="tabs-container">
                <button class="tab-btn active" onclick="switchTab(event, 'current')">Current Appointment</button>
                <button class="tab-btn" onclick="switchTab(event, 'past')">Past Appointments</button>
                <button class="tab-btn" onclick="switchTab(event, 'cancelled')">Cancelled Appointment</button>
            </div>

            <!-- Current Appointments -->
            <div id="current" class="tab-content active">
                <table class="appointments-table">
                    <thead>
                        <tr>
                            <th>Doctor</th>
                            <th>Complaint</th>
                            <th>Purpose</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th style="text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php renderAppointmentRows($current_appointments); ?>
                    </tbody>
                </table>
            </div>

            <!-- Past Appointments -->
            <div id="past" class="tab-content">
                <table class="appointments-table">
                    <thead>
                        <tr>
                            <th>Doctor</th>
                            <th>Complaint</th>
                            <th>Purpose</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th style="text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php renderAppointmentRows($past_appointments); ?>
                    </tbody>
                </table>
            </div>

            <!-- Cancelled Appointments -->
            <div id="cancelled" class="tab-content">
                <table class="appointments-table">
                    <thead>
                        <tr>
                            <th>Doctor</th>
                            <th>Complaint</th>
                            <th>Purpose</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th style="text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php renderAppointmentRows($cancelled_appointments); ?>
                    </tbody>
                </table>
            </div>

            <?php endif; ?>

        </div>

    </main>

<script>
function switchTab(event, tabId) {
    const contents = document.querySelectorAll('.tab-content');
    contents.forEach(content => content.classList.remove('active'));

    const buttons = document.querySelectorAll('.tab-btn');
    buttons.forEach(btn => btn.classList.remove('active'));

    document.getElementById(tabId).classList.add('active');
    event.currentTarget.classList.add('active');
}
</script>

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
