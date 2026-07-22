<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
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
    <title>My Profile | SwiftCare</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="../css/patient.css">
    <link rel="stylesheet" href="../css/patient_dashboardd.css">
    <link rel="stylesheet" href="../css/myprofile.css">
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
            <a href="patient_request consultation.php">Request Consultation</a>
            <a href="patient_view_appointment.php">Appointment</a>
            <a href="patient_request medical.php">Request Medical Documents</a>
            <a href="patient_services.php">Services</a>
            <a href="all doctors.php"> All Doctors</a>
        </nav>

        <!-- Profile dropdown (icon only) -->
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
            <h1>My Profile</h1>
        </header>

        <div class="mp-wrap" style="margin: 25px auto;">

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
                                <div class="mp-pw-wrap">
                                    <input type="password" name="new_password" id="mpNewPw" required minlength="6">
                                    <button type="button" class="mp-eye-btn" onclick="mpTogglePw('mpNewPw', this)" title="Show/Hide">&#128065;</button>
                                </div>
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

<script>
function mpTogglePw(id, btn){
    var inp = document.getElementById(id);
    if (inp.type === 'password'){
        inp.type = 'text';
        btn.style.color = '#2c3e50';
    } else {
        inp.type = 'password';
        btn.style.color = '#64748b';
    }
}
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

<script src="../js/input-restrictions.js"></script>
</body>
</html>
