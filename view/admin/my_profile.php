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

$profile_error   = $_SESSION['profile_error']   ?? "";
$profile_success = $_SESSION['profile_success'] ?? "";
$password_error   = $_SESSION['password_error']   ?? "";
$password_success = $_SESSION['password_success'] ?? "";
unset($_SESSION['profile_error'], $_SESSION['profile_success'], $_SESSION['password_error'], $_SESSION['password_success']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link rel="stylesheet" href="../css/admin_dashboard.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/myprofile.css">
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

        <div class="mp-wrap">

            <!-- ACCOUNT INFORMATION -->
            <div class="mp-card">
                <div class="mp-card-header">
                    <h2>Account Information</h2>
                    <p>Update your name, username, and email address.</p>
                </div>
                <div class="mp-card-body">

                    <?php if ($profile_success): ?><div class="mp-alert mp-alert-success"><?php echo htmlspecialchars($profile_success); ?></div><?php endif; ?>
                    <?php if ($profile_error): ?><div class="mp-alert mp-alert-error"><?php echo htmlspecialchars($profile_error); ?></div><?php endif; ?>

                    <form method="POST" action="http://localhost/clinic1/controller/UserController.php">
                        <div class="mp-grid">
                            <div class="mp-field">
                                <label>First Name</label>
                                <input type="text" name="first_name" value="<?php echo htmlspecialchars($user_info['first_name']); ?>" required>
                            </div>
                            <div class="mp-field">
                                <label>Last Name</label>
                                <input type="text" name="last_name" value="<?php echo htmlspecialchars($user_info['last_name']); ?>" required>
                            </div>
                            <div class="mp-field">
                                <label>Username</label>
                                <input type="text" name="username" value="<?php echo htmlspecialchars($user_info['username']); ?>" required>
                            </div>
                            <div class="mp-field">
                                <label>Email</label>
                                <input type="email" name="email" value="<?php echo htmlspecialchars($user_info['email']); ?>" required>
                            </div>
                        </div>
                        <div class="mp-actions">
                            <button type="submit" name="updateMyProfile" class="mp-btn-save">Save Changes</button>
                        </div>
                    </form>

                </div>
            </div>

            <!-- CHANGE PASSWORD -->
            <div class="mp-card">
                <div class="mp-card-header">
                    <h2>Change Password</h2>
                    <p>Enter your current password to set a new one.</p>
                </div>
                <div class="mp-card-body">

                    <?php if ($password_success): ?><div class="mp-alert mp-alert-success"><?php echo htmlspecialchars($password_success); ?></div><?php endif; ?>
                    <?php if ($password_error): ?><div class="mp-alert mp-alert-error"><?php echo htmlspecialchars($password_error); ?></div><?php endif; ?>

                    <form method="POST" action="http://localhost/clinic1/controller/UserController.php">
                        <div class="mp-grid">
                            <div class="mp-field mp-full">
                                <label>Current Password</label>
                                <input type="password" name="current_password" required>
                            </div>
                            <div class="mp-field">
                                <label>New Password</label>
                                <input type="password" name="new_password" required minlength="6">
                                <span class="mp-hint">Must be 6 or more characters</span>
                            </div>
                            <div class="mp-field">
                                <label>Confirm New Password</label>
                                <input type="password" name="confirm_password" required minlength="6">
                            </div>
                        </div>
                        <div class="mp-actions">
                            <button type="submit" name="changeMyPassword" class="mp-btn-save">Update Password</button>
                        </div>
                    </form>

                </div>
            </div>

        </div>

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
