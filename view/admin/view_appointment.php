<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: http://localhost/clinic1/view/login/login.php");
    exit();
}

require_once 'C:\xampp\htdocs\clinic1\config\Database.php';
require_once 'C:\xampp\htdocs\clinic1\model\Appointment.php';
require_once 'C:\xampp\htdocs\clinic1\model\User.php';
require_once 'C:\xampp\htdocs\clinic1\model\Doctor.php';

$database = new Database();
$conn = $database->connect();
$appointment = new Appointment($conn);
$user = new User($conn);
$doctorModel = new Doctor($conn);

$user_info = $user->getUserById($_SESSION['user_id']);

$appointment_id = (int)$_GET['id'];
$appointment_data = $appointment->getAppointmentById($appointment_id);

$doctors = $doctorModel->getAllDoctors();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Appointment</title>
    <link rel="stylesheet" href="../css/admin_dashboard.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>

<body>

<div class="dashboard-container">
    <aside class="sidebar-simple">
        <a href="http://localhost/clinic1/view/admin/admin_appointments.php" class="back-btn-top">
            <div class="icon">←</div>
            <span>Back</span>
        </a>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <div class="topbar-left">
                <div class="clinic-text">
                    <h1>Appointment Details</h1>
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

        <!-- Read-only appointment details, reflecting exactly what was submitted at booking/request time -->
        <section class="table-section section-gap">
            <div class="section-header">Appointment Information</div>
            <div class="info-grid info-grid-pad">
                <div class="info-item"><label>Patient</label><span><?php echo htmlspecialchars($appointment_data['patient_name']); ?></span></div>
                <div class="info-item"><label>Doctor</label><span>Dr. <?php echo htmlspecialchars($appointment_data['doctor_name']); ?></span></div>
                <div class="info-item"><label>Date</label><span><?php echo date('F j, Y', strtotime($appointment_data['appointment_date'])); ?></span></div>
                <div class="info-item"><label>Time</label><span><?php echo htmlspecialchars($appointment_data['appointment_time']); ?></span></div>
                <div class="info-item"><label>Complaint</label><span><?php echo $appointment_data['complaint'] ? htmlspecialchars($appointment_data['complaint']) : '—'; ?></span></div>
                <div class="info-item"><label>Purpose</label><span><?php echo htmlspecialchars($appointment_data['purpose']); ?></span></div>
                <div class="info-item"><label>Type</label><span><?php echo htmlspecialchars($appointment_data['consultation_type'] ?: 'In Person'); ?></span></div>
                <div class="info-item"><label>Status</label><span class="status <?php echo $appointment_data['status']; ?>"><?php echo ucfirst($appointment_data['status']); ?></span></div>
            </div>
        </section>
        <?php if(!in_array($appointment_data['status'], ['completed', 'cancelled'])): ?>
        <div class="btn-row">
            <a href="http://localhost/clinic1/controller/AppointmentController.php?adminCancel=<?php echo $appointment_id; ?>" class="btn-cancel-appointment" onclick="return confirm('Cancel this appointment?')">Cancel Appointment</a>
        </div>
        <?php endif; ?>
    </main>
</div>

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