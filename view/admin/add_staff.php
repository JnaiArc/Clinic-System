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

$error = $_SESSION['error'] ?? "";
unset($_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Staff</title>
    <link rel="stylesheet" href="../css/admin_dashboard.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/add_staff.css">

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
            <a href="http://localhost/clinic1/view/admin/admin_staff.php" class="menu-item active">Staff</a>
        </nav>
    </aside>

    <main class="main-content">

        <header class="topbar">
            <div class="topbar-left">
                <div class="clinic-text">
                    <h1>Add Staff</h1>
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

        <section class="table-section staff-page">

            <div class="section-header">Create Admin / Doctor Account</div>

            <form action="http://localhost/clinic1/controller/StaffController.php" method="POST" enctype="multipart/form-data" class="staff-form staff-form-v2">

                <?php if($error): ?>
                <div class="error-box"><?php echo nl2br(htmlspecialchars($error)); ?></div>
                <?php endif; ?>

                <!-- role -->
                <div class="form-group full">
                    <label>Role <span class="req">*</span></label>
                    <select name="role" id="role" required onchange="showFields()">
                        <option value="" disabled selected>Select Role</option>
                        <option value="admin">Admin</option>
                        <option value="doctor">Doctor</option>
                    </select>
                </div>

                <!-- name -->
                <div class="form-group">
                    <label>First Name <span class="req">*</span></label>
                    <input type="text" name="first_name" required>
                </div>

                <div class="form-group">
                    <label>Last Name <span class="req">*</span></label>
                    <input type="text" name="last_name" required>
                </div>

                <!-- email -->
                <div class="form-group full">
                    <label>Email <span class="req">*</span></label>
                    <input type="email" name="email" required placeholder="example@email.com">
                </div>

                <!-- admin role-->
                <div id="adminFields" class="full" style="display: none;">
                    <div class="form-group">
                        <label>Username <span class="req">*</span></label>
                        <input type="text" name="username" id="usernameInput">
                    </div>
                </div>

                <!-- doctor role -->
                <div id="doctorFields" class="full" style="display: none;">

                    <div class="form-group full">
                        <label>License Number <span class="req">*</span></label>
                        <input type="text" name="license_number" id="licenseInput">
                    </div>

                    <div class="form-group full">
                        <label>Schedule Days <span class="req">*</span></label>
                        <small style="color: #666; font-size: 11px;">Select available days</small>
                        <div class="checkbox-group">
                            <label><input type="checkbox" name="schedule_days[]" value="Monday"> Monday</label>
                            <label><input type="checkbox" name="schedule_days[]" value="Tuesday"> Tuesday</label>
                            <label><input type="checkbox" name="schedule_days[]" value="Wednesday"> Wednesday</label>
                            <label><input type="checkbox" name="schedule_days[]" value="Thursday"> Thursday</label>
                            <label><input type="checkbox" name="schedule_days[]" value="Friday"> Friday</label>
                            <label><input type="checkbox" name="schedule_days[]" value="Saturday"> Saturday</label>
                            <label><input type="checkbox" name="schedule_days[]" value="Sunday"> Sunday</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Start Time <span class="req">*</span></label>
                        <select name="schedule_time_start" id="timeStart">
                            <option value="">Select Start Time</option>
                            <option value="8:00 AM">8:00 AM</option>
                            <option value="9:00 AM">9:00 AM</option>
                            <option value="10:00 AM">10:00 AM</option>
                            <option value="11:00 AM">11:00 AM</option>
                            <option value="12:00 PM">12:00 PM</option>
                            <option value="1:00 PM">1:00 PM</option>
                            <option value="2:00 PM">2:00 PM</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>End Time <span class="req">*</span></label>
                        <select name="schedule_time_end" id="timeEnd">
                            <option value="">Select End Time</option>
                            <option value="12:00 PM">12:00 PM</option>
                            <option value="1:00 PM">1:00 PM</option>
                            <option value="2:00 PM">2:00 PM</option>
                            <option value="3:00 PM">3:00 PM</option>
                            <option value="4:00 PM">4:00 PM</option>
                            <option value="5:00 PM">5:00 PM</option>
                            <option value="6:00 PM">6:00 PM</option>
                        </select>
                    </div>

                </div>

                <!-- pass -->
                <div class="form-group">
                    <label>Password <span class="req">*</span></label>
                    <div class="pw-wrap">
                        <input type="password" name="password" id="regPw" required>
                        <button type="button" class="eye-btn" onclick="togglePw('regPw',this)" title="Show/Hide">&#128065;</button>
                    </div>
                    <span class="pw-hint">Must be 6 or more characters</span>
                </div>

                <div class="form-group">
                    <label>Confirm Password <span class="req">*</span></label>
                    <div class="pw-wrap">
                        <input type="password" name="confirm_password" id="regPw2" required>
                        <button type="button" class="eye-btn" onclick="togglePw('regPw2',this)" title="Show/Hide">&#128065;</button>
                    </div>
                </div>

                <div class="form-group full">
                    <label>Profile Photo (Optional)</label>
                    <input type="file" name="profile_photo" accept="image/*" style="padding:10px; background:#f1f5f9; border:1px dashed #cbd5e1; border-radius:10px;">
                </div>

                <button class="add-new-btn" type="submit" name="addStaffBtn">CREATE STAFF ACCOUNT</button>

            </form>

        </section>

    </main>

</div>

<script src="../js/login.js"></script>


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
