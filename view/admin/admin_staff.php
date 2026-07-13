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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff Management</title>
    <link rel="stylesheet" href="../css/admin_dashboard.css">
    <link rel="stylesheet" href="../css/admin_patients.css">
    <link rel="stylesheet" href="../css/admin_add_view.css">
    <link rel="stylesheet" href="../css/admin_appointment.css">
</head>

<body>

<div class="dashboard-container">

    <aside class="sidebar">
        
        <div class="sidebar-top">
            <?php if(!empty($user_info['profile_photo'])): ?>
            <img src="../../uploads/<?php echo $user_info['profile_photo']; ?>" class="profile-circle" style="object-fit: cover;">
            <?php else: ?>
            <div class="profile-circle"></div>
            <?php endif; ?>
            <div class="profile-name">
                <h2><?php echo $_SESSION['name']; ?></h2>
            </div>
        </div>

        <nav class="sidebar-menu">
            <a href="http://localhost/clinic1/view/admin/admin_dashboard.php" class="menu-item">Dashboard</a>
            <a href="http://localhost/clinic1/view/admin/admin_patientRecord.php" class="menu-item">Patient Records</a>
            <a href="http://localhost/clinic1/view/admin/admin_appointments.php" class="menu-item">Appointments</a>
            <a href="http://localhost/clinic1/view/admin/admin_followup.php" class="menu-item">Follow-Up Checkup</a>
            <a href="http://localhost/clinic1/view/admin/admin_doctors.php" class="menu-item">Doctors</a>
            <a href="http://localhost/clinic1/view/admin/admin_staff.php" class="menu-item active">Staff</a>
            <a href="http://localhost/clinic1/controller/logoutController.php" class="menu-item logout-btn" onclick="return confirm('Logout?')">Logout</a>
        </nav>
    </aside>

    <main class="main-content">

        <header class="topbar">
            <div class="topbar-left">
                <img src="http://localhost/clinic1/logo.jpg" class="clinic-logo">
                <div class="clinic-text">
                    <h1>SwiftCare Clinic</h1>
                    <p><?php echo $edit_mode ? 'Edit User' : 'Staff Management'; ?></p>
                </div>
            </div>
            <div class="admin-box">Admin</div>
        </header>

        <section class="table-section">

            <div class="section-header">
                <?php echo $edit_mode ? 'Edit User' : 'Staff List'; ?>
            </div>

            <?php if ($edit_mode && $edit_user): ?>
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
                        <th>Details</th>
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
                                <?php if($row['role'] == 'admin'): ?>
                                User: <?php echo $row['username']; ?>
                                <?php else: ?>
                                License: <?php echo $row['license_number']; ?>
                                <?php endif; ?>
                            </td>
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
                            <td colspan="5" style="text-align: center; color: #64748b;">No users found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <?php endif; ?>

        </section>

    </main>

</div>

</body>
</html>