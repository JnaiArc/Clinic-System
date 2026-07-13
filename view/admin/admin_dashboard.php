<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: http://localhost/clinic1/view/login/login.php");
    exit();
}

require_once 'C:\xampp\htdocs\clinic1\config\Database.php';
require_once 'C:\xampp\htdocs\clinic1\model\User.php';
require_once 'C:\xampp\htdocs\clinic1\model\Patient.php';
require_once 'C:\xampp\htdocs\clinic1\model\Appointment.php';

$database = new Database();
$conn = $database->connect();
$user = new User($conn);
$patient = new Patient($conn);
$appointment = new Appointment($conn);

$user_info = $user->getUserById($_SESSION['user_id']);
$total_patients = $patient->countTotal();
$today_appointments = $appointment->countToday();
$followups_count = $appointment->countFollowUps();
$doctors_count = $user->countDoctors();
$recent_appointments = $appointment->getTodayAppointments();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/admin_dashboard.css">
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
            <a href="http://localhost/clinic1/view/admin/admin_dashboard.php" class="menu-item active">Dashboard</a>
            <a href="http://localhost/clinic1/view/admin/admin_patientRecord.php" class="menu-item">Patient Records</a>
            <a href="http://localhost/clinic1/view/admin/admin_appointments.php" class="menu-item">Appointments</a>
            <a href="http://localhost/clinic1/view/admin/admin_followup.php" class="menu-item">Follow-Up Checkup</a>
            <a href="http://localhost/clinic1/view/admin/admin_doctors.php" class="menu-item">Doctors</a>
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
                    <p>Clinic Appointment System</p>
                </div>
            </div>

            <div class="admin-box">
                Admin
            </div>

        </header>

        <section class="stats-grid">

            <div class="stat-card">
                <h3>Today's Appointments</h3>
                <p><?php echo $today_appointments; ?></p>
            </div>

            <div class="stat-card">
                <h3>Follow Ups</h3>
                <p><?php echo $followups_count; ?></p>
            </div>

            <div class="stat-card">
                <h3>Total Patients</h3>
                <p><?php echo $total_patients; ?></p>
            </div>

            <div class="stat-card">
                <h3>Doctors</h3>
                <p><?php echo $doctors_count; ?></p>
            </div>

        </section>

        <section class="table-section">

            <div class="section-header">
                Today's Appointments
            </div>

            <table class="appointment-table">

                <thead>
                    <tr>
                        <th>Patient Name</th>
                        <th>Doctor</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if ($recent_appointments->rowCount() > 0): ?>
                        <?php while ($row = $recent_appointments->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?php echo $row['patient_name']; ?></td>
                            <td>Dr. <?php echo $row['doctor_name']; ?></td>
                            <td><?php echo $row['appointment_date']; ?></td>
                            <td><span class="status <?php echo $row['status']; ?>"><?php echo ucfirst($row['status']); ?></span></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center; color: #64748b;">No appointments yet</td>
                        </tr>
                    <?php endif; ?>
                </tbody>

            </table>

        </section>

    </main>

</div>

</body>

</html>