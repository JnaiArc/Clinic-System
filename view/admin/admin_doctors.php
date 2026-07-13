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
    <link rel="stylesheet" href="../css/admin_patients.css">
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
            <a href="http://localhost/clinic1/view/admin/admin_doctors.php" class="menu-item active">Doctors</a>
            <a href="http://localhost/clinic1/view/admin/admin_staff.php" class="menu-item">Staff</a>
            <a href="http://localhost/clinic1/controller/logoutController.php" class="menu-item logout-btn" onclick="return confirm('Logout?')">Logout</a>
        </nav>
    </aside>

    <main class="main-content">

        <header class="topbar">
            <div class="topbar-left">
                <img src="http://localhost/clinic1/logo.jpg" class="clinic-logo">
                <div class="clinic-text">
                    <h1>SwiftCare Clinic</h1>
                    <p>Doctors</p>
                </div>
            </div>
            <div class="admin-box">Admin</div>
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

</body>
</html>