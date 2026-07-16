<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: http://localhost/clinic1/view/login/login.php");
    exit();
}

require_once 'C:\xampp\htdocs\clinic1\config\Database.php';
require_once 'C:\xampp\htdocs\clinic1\model\Appointment.php';
require_once 'C:\xampp\htdocs\clinic1\model\User.php';

$database = new Database();
$conn = $database->connect();

$appointment_model = new Appointment($conn);
$user_model = new User($conn);

$doctor_info = $user_model->getUserById($_SESSION['user_id']);
$today_appointments = $appointment_model->getDoctorTodayAppointments($_SESSION['user_id']);
$followups_count = $appointment_model->getDoctorFollowUps($_SESSION['user_id'])->rowCount();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Doctor Dashboard</title>
    <link rel="stylesheet" href="../css/doctor.css">
</head>

<body>

<div class="dashboard-container">

    <aside class="sidebar">

        <div class="sidebar-top">
            <img src="http://localhost/clinic1/img/logo.png" class="sidebar-logo">
            <div class="sidebar-clinic-name">
                <h2>SwiftCare</h2>
            </div>
        </div>

        <nav class="sidebar-menu">
            <a href="http://localhost/clinic1/view/doctor/doctor_dashboard.php" class="menu-item active">Dashboard</a>
            <a href="http://localhost/clinic1/view/doctor/doctor_appointments.php" class="menu-item">My Appointments</a>
            <a href="http://localhost/clinic1/view/doctor/doctor_followup.php" class="menu-item">Follow-Up</a>
        </nav>

    </aside>

    <main class="main-content">

        <header class="topbar">
            <div class="topbar-left">
                <div class="clinic-text">
                    <h1>Doctor Portal</h1>
                </div>
            </div>
            <div class="user-menu">
                <button type="button" class="user-menu-toggle" onclick="toggleUserMenu(this)">
                    <?php if(!empty($doctor_info['profile_photo'])): ?>
                    <img src="http://localhost/clinic1/uploads/<?php echo $doctor_info['profile_photo']; ?>" class="user-avatar" style="object-fit: cover;">
                    <?php else: ?>
                    <div class="user-avatar"></div>
                    <?php endif; ?>
                    <span class="user-name">Dr. <?php echo $_SESSION['name']; ?></span>
                    <span class="user-role-badge">Doctor</span>
                    <span class="dropdown-arrow">&#9662;</span>
                </button>
                <div class="user-dropdown">
                    <a href="http://localhost/clinic1/view/doctor/my_profile.php" class="dropdown-item">My Profile</a>
                    <a href="http://localhost/clinic1/controller/logoutController.php" class="dropdown-item signout" onclick="return confirm('Logout?')">Sign Out</a>
                </div>
            </div>
        </header>

        <section class="stats-grid">

            <div class="stat-card">
                <h3>Today's Appointments</h3>
                <p><?php echo $today_appointments->rowCount(); ?></p>
            </div>

            <div class="stat-card">
                <h3>Follow-Ups</h3>
                <p><?php echo $followups_count; ?></p>
            </div>

        </section>

        <section class="table-section">

            <div class="section-header">Today's Appointments</div>

            <table class="appointment-table">
                <thead>
                    <tr>
                        <th>Patient Name</th>
                        <th>Time</th>
                        <th>Purpose</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($today_appointments->rowCount() > 0): ?>
                        <?php while ($row = $today_appointments->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?php echo $row['patient_name']; ?></td>
                            <td><?php echo $row['appointment_time']; ?></td>
                            <td><?php echo $row['purpose']; ?></td>
                            <td><span class="status <?php echo $row['status']; ?>"><?php echo ucfirst($row['status']); ?></span></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center; color: #64748b;">No appointments today</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

        </section>

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