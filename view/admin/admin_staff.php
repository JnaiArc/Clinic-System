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

$users_result = $user->getAllUsers();

$edit_mode = isset($_GET['edit']) && $_GET['edit'] == 1;
$edit_id = $_GET['edit_id'] ?? 0;
$edit_user = $edit_id ? $user->getUserById($edit_id) : null;

$add_mode = isset($_GET['add']) && $_GET['add'] == 1;
$add_error = $_SESSION['error'] ?? "";
unset($_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff Management</title>
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
            <a href="http://localhost/clinic1/view/admin/admin_staff.php" class="menu-item active">Staff</a>
        </nav>
    </aside>

    <main class="main-content">

        <header class="topbar">
            <div class="topbar-left">
                <div class="clinic-text">
                    <h1><?php echo $edit_mode ? 'Edit User' : ($add_mode ? 'Add Staff' : 'Staff Management'); ?></h1>
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

            <div class="section-header" style="display:flex; align-items:center; justify-content:space-between;">
                <span><?php echo $edit_mode ? 'Edit User' : ($add_mode ? 'Create Admin / Doctor Account' : 'Staff List'); ?></span>
                <?php if (!$edit_mode && !$add_mode): ?>
                <a href="http://localhost/clinic1/view/admin/admin_staff.php?add=1" class="add-new-btn">+ Add Staff</a>
                <?php endif; ?>
            </div>

            <?php if ($add_mode): ?>
            <!-- ADD STAFF FORM -->
            <div class="staff-form-box">

                <?php if($add_error): ?>
                <div class="error-box"><?php echo nl2br(htmlspecialchars($add_error)); ?></div>
                <?php endif; ?>

                <form action="http://localhost/clinic1/controller/StaffController.php" method="POST" enctype="multipart/form-data">

                    <div class="form-grid">

                        <!-- role -->
                        <div class="form-group span-2">
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
                        <div class="form-group">
                            <label>Email <span class="req">*</span></label>
                            <input type="email" name="email" required placeholder="example@email.com">
                        </div>

                        <!-- admin role-->
                        <div id="adminFields" style="display: none;">
                            <div class="form-group">
                                <label>Username <span class="req">*</span></label>
                                <input type="text" name="username" id="usernameInput">
                            </div>
                        </div>

                        <!-- doctor role -->
                        <div id="doctorFields" style="display: none;">

                            <div class="form-group">
                                <label>License Number <span class="req">*</span></label>
                                <input type="text" name="license_number" id="licenseInput">
                            </div>

                            <div class="form-group span-2">
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

                        <div class="profile-photo span-2">
                            <label>Profile Photo (Optional)</label>
                            <input type="file" name="profile_photo" accept="image/*">
                        </div>

                        <button class="add-new-btn" type="submit" name="addStaffBtn">CREATE STAFF ACCOUNT</button>

                    </div>

                </form>

            </div>

            <?php elseif ($edit_mode && $edit_user): ?>
            <!-- EDIT FORM -->
            <form method="POST" action="http://localhost/clinic1/controller/UserController.php" class="appointment-body">
                <input type="hidden" name="id" value="<?php echo $edit_id; ?>">
                <input type="hidden" name="role" value="<?php echo $edit_user['role']; ?>">

                <div class="form-group">
                    <label>Role</label>
                    <input type="text" value="<?php echo ucfirst($edit_user['role']); ?>" disabled style="background:#f1f5f9;color:#64748b;cursor:not-allowed;border:1px solid #cbd5e1;">
                </div>

                <div class="form-group">
                    <label>First Name <span style="color:red">*</span></label>
                    <input type="text" name="first_name" value="<?php echo $edit_user['first_name']; ?>" required>
                </div>

                <div class="form-group">
                    <label>Last Name <span style="color:red">*</span></label>
                    <input type="text" name="last_name" value="<?php echo $edit_user['last_name']; ?>" required>
                </div>

                <div class="form-group">
                    <label>Email <span style="color:red">*</span></label>
                    <input type="email" name="email" value="<?php echo $edit_user['email']; ?>" required>
                </div>

                <?php if($edit_user['role'] == 'admin'): ?>
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" value="<?php echo $edit_user['username']; ?>">
                </div>
                <?php else: ?>
                <div class="form-group">
                    <label>License Number</label>
                    <input type="text" name="license_number" value="<?php echo $edit_user['license_number']; ?>">
                </div>
                <div class="form-group">
                    <label>Schedule Days</label>
                    <input type="text" name="schedule_days" value="<?php echo $edit_user['schedule_days']; ?>">
                </div>
                <div class="form-group">
                    <label>Start Time</label>
                    <select name="schedule_time_start">
                        <option value="">Select Start Time</option>
                        <?php $times = ['8:00 AM','9:00 AM','10:00 AM','11:00 AM','12:00 PM','1:00 PM','2:00 PM','3:00 PM']; foreach($times as $t): ?>
                        <option value="<?php echo $t; ?>" <?php echo $edit_user['schedule_time_start']==$t?'selected':''; ?>><?php echo $t; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>End Time</label>
                    <select name="schedule_time_end">
                        <option value="">Select End Time</option>
                        <?php $times_end = ['9:00 AM','10:00 AM','11:00 AM','12:00 PM','1:00 PM','2:00 PM','3:00 PM','4:00 PM','5:00 PM','6:00 PM']; foreach($times_end as $t): ?>
                        <option value="<?php echo $t; ?>" <?php echo $edit_user['schedule_time_end']==$t?'selected':''; ?>><?php echo $t; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <div class="view-buttons" style="grid-column: span 2;">
                    <button type="submit" name="updateUser" class="save-btn">Update</button>
                    <a href="http://localhost/clinic1/view/admin/admin_staff.php" class="back-link">Cancel</a>
                </div>
            </form>

            <?php else: ?>
            <!-- LIST TABLE -->
            <table class="appointment-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Email</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($users_result->rowCount() > 0): ?>
                        <?php while ($row = $users_result->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?php echo $row['first_name'].' '.$row['last_name']; ?></td>
                            <td><span class="status <?php echo $row['role']; ?>"><?php echo ucfirst($row['role']); ?></span></td>
                            <td><?php echo $row['email']; ?></td>
                            <td>
                                <a href="http://localhost/clinic1/view/admin/admin_staff.php?edit=1&edit_id=<?php echo $row['id']; ?>" class="action-btn view-btn">Edit</a>
                                
                                <form method="POST" action="http://localhost/clinic1/controller/UserController.php" style="display:inline;">
                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="deleteUser" class="action-btn delete-btn" onclick="return confirm('Delete user?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center; color: #64748b;">No users found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <?php endif; ?>

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