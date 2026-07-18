<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: http://localhost/clinic1/view/login/login.php");
    exit();
}

require_once 'C:\xampp\htdocs\clinic1\config\Database.php';
require_once 'C:\xampp\htdocs\clinic1\model\User.php';

$database = new Database();
$conn = $database->connect();
$user = new User($conn);

$user_info = $user->getUserById($_SESSION['user_id']);
$doctors_result = $user->getAllDoctors();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Doctors</title>
    <link rel="stylesheet" href="../css/admin_dashboard.css">
    <link rel="stylesheet" href="../css/admin.css">
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
            <a href="http://localhost/clinic1/view/admin/admin_dashboard.php" class="menu-item">Dashboard</a>
            <a href="http://localhost/clinic1/view/admin/admin_patientRecord.php" class="menu-item">Patient Records</a>
            <a href="http://localhost/clinic1/view/admin/admin_appointments.php" class="menu-item">Appointments</a>
            <a href="http://localhost/clinic1/view/admin/admin_doctors.php" class="menu-item active">Doctors</a>
            <a href="http://localhost/clinic1/view/admin/admin_staff.php" class="menu-item">Staff</a>
        </nav>
    </aside>

    <main class="main-content">

        <header class="topbar">
            <div class="topbar-left">
                <div class="clinic-text">
                    <h1>Doctors</h1>
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

            <div class="section-header">
                Doctor List
            </div>

            <table class="appointment-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>License Number</th>
                        <th>Schedule Days</th>
                        <th>Schedule Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($doctors_result->rowCount() > 0): ?>
                        <?php while ($row = $doctors_result->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td>Dr. <?php echo $row['first_name'].' '.$row['last_name']; ?></td>
                            <td><?php echo $row['license_number']; ?></td>
                            <td><?php echo $row['schedule_days']; ?></td>
                            <td><?php echo $row['schedule_time_start'].' - '.$row['schedule_time_end']; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center; color: #64748b;">No doctors found</td>
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