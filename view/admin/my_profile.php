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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
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
            <a href="http://localhost/clinic1/view/admin/admin_followup.php" class="menu-item">Follow-Up Checkup</a>
            <a href="http://localhost/clinic1/view/admin/admin_doctors.php" class="menu-item">Doctors</a>
            <a href="http://localhost/clinic1/view/admin/admin_staff.php" class="menu-item">Staff</a>
        </nav>

    </aside>

    <main class="main-content">

        <header class="topbar">
            <div class="topbar-left">
                <div class="clinic-text">
                    <h1>My Profile</h1>
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
                Account Information
            </div>

            <div class="info-grid info-grid-pad">

                <div class="info-item" style="grid-column: 1 / -1; text-align:center;">
                    <?php if(!empty($user_info['profile_photo'])): ?>
                    <img src="../../uploads/<?php echo $user_info['profile_photo']; ?>" style="width:100px;height:100px;border-radius:50%;object-fit:cover;">
                    <?php else: ?>
                    <div style="width:100px;height:100px;border-radius:50%;background:#e2e8f0;margin:0 auto;"></div>
                    <?php endif; ?>
                </div>

                <div class="info-item">
                    <label>First Name</label>
                    <span><?php echo htmlspecialchars($user_info['first_name']); ?></span>
                </div>

                <div class="info-item">
                    <label>Last Name</label>
                    <span><?php echo htmlspecialchars($user_info['last_name']); ?></span>
                </div>

                <div class="info-item">
                    <label>Username</label>
                    <span><?php echo htmlspecialchars($user_info['username']); ?></span>
                </div>

                <div class="info-item">
                    <label>Email</label>
                    <span><?php echo htmlspecialchars($user_info['email']); ?></span>
                </div>

                <div class="info-item">
                    <label>Role</label>
                    <span><?php echo ucfirst($user_info['role']); ?></span>
                </div>

            </div>

            <div class="btn-row">
                <p style="color:#64748b; font-size:13px;">Editing profile photo, name, and password will be available soon.</p>
            </div>

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
