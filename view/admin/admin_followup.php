<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: http://localhost/clinic1/view/login/login.php");
    exit();
}

require_once 'C:\xampp\htdocs\clinic1\config\Database.php';
require_once 'C:\xampp\htdocs\clinic1\model\Appointment.php';
require_once 'C:\xampp\htdocs\clinic1\model\User.php';

$database = new Database();
$conn = $database->connect();
$user = new User($conn);
$appointment = new Appointment($conn);

$user_info = $user->getUserById($_SESSION['user_id']);

$followups = $appointment->getFollowUpList();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Follow-Up Checkup</title>
    <link rel="stylesheet" href="../css/admin_dashboard.css">
    <link rel="stylesheet" href="../css/admin_tables.css">
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
            <a href="http://localhost/clinic1/view/admin/admin_followup.php" class="menu-item active">Follow-Up Checkup</a>
            <a href="http://localhost/clinic1/view/admin/admin_doctors.php" class="menu-item">Doctors</a>
            <a href="http://localhost/clinic1/view/admin/admin_staff.php" class="menu-item">Staff</a>
            <a href="http://localhost/clinic1/controller/logoutController.php" class="menu-item logout-btn" onclick="return confirm('Logout?')">Logout</a>
        </nav>
    </aside>

    <main class="main-content">

        <header class="topbar">
            <div class="topbar-left">
                <img src="http://localhost/clinic1/logo.jpg" class="clinic-logo">
                <div class="clinic-text"><h1>SwiftCare Clinic</h1><p>Follow-Up Checkup</p></div>
            </div>
            <div class="admin-box">Admin</div>
        </header>

        <section class="table-section">
            <div class="section-header">Follow-Up List</div>
            <table class="appointment-table">
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($followups->rowCount() > 0): ?>
                        <?php while ($row = $followups->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?php echo $row['patient_name']; ?></td>
                            <td>Dr. <?php echo $row['doctor_name']; ?></td>
                            <td><?php echo $row['appointment_date']; ?></td>
                            <td><?php echo $row['appointment_time']; ?></td>
                            <td><span class="status <?php echo $row['status']; ?>"><?php echo ucfirst($row['status']); ?></span></td>
                            <td><a href="http://localhost/clinic1/view/admin/view_followup_edit.php?id=<?php echo $row['id']; ?>" class="action-btn view-btn">Edit</a></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; color: #64748b;">No follow-ups</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>

    </main>

</div>

</body>

</html>