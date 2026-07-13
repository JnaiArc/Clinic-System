<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: http://localhost/clinic1/view/login/login.php");
    exit();
}

require_once 'C:\xampp\htdocs\clinic1\config\Database.php';
require_once 'C:\xampp\htdocs\clinic1\model\User.php';
require_once 'C:\xampp\htdocs\clinic1\model\Appointment.php';

$database = new Database();
$conn = $database->connect();
$user = new User($conn);
$appointment = new Appointment($conn);

$user_info = $user->getUserById($_SESSION['user_id']);

$appointment->markMissedAppointments();

// FOR TABS
$today_appointments    = $appointment->getTodayAppointments();
$upcoming_appointments = $appointment->getUpcomingOnlyAppointments();
$followup_appointments = $appointment->getFollowUpAppointments();
$all_appointments      = $appointment->getAllTodayAndFuture();
$completed_appointments = $appointment->getCompletedAppointments();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Appointments</title>
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
            <div class="profile-name"><h2><?php echo $_SESSION['name']; ?></h2></div>
        </div>
        <nav class="sidebar-menu">
            <a href="http://localhost/clinic1/view/admin/admin_dashboard.php" class="menu-item">Dashboard</a>
            <a href="http://localhost/clinic1/view/admin/admin_patientRecord.php" class="menu-item">Patient Records</a>
            <a href="http://localhost/clinic1/view/admin/admin_appointments.php" class="menu-item active">Appointments</a>
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
                <div class="clinic-text"><h1>SwiftCare Clinic</h1><p>Appointments</p></div>
            </div>
            <div class="admin-box">Admin</div>
        </header>

        <!-- TABS -->
        <div class="tabs-container">
            <button class="tab-btn active" onclick="showTab('today')" id="tab-today">
                Today <span class="tab-count"><?php echo $today_appointments->rowCount(); ?></span>
            </button>
            <button class="tab-btn" onclick="showTab('upcoming')" id="tab-upcoming">
                Upcoming <span class="tab-count"><?php echo $upcoming_appointments->rowCount(); ?></span>
            </button>
            <button class="tab-btn" onclick="showTab('followup')" id="tab-followup">
                Follow-Up <span class="tab-count"><?php echo $followup_appointments->rowCount(); ?></span>
            </button>
            <button class="tab-btn" onclick="showTab('all')" id="tab-all">
                All <span class="tab-count"><?php echo $all_appointments->rowCount(); ?></span>
            </button>
            <button class="tab-btn" onclick="showTab('completed')" id="tab-completed">
                Completed <span class="tab-count"><?php echo $completed_appointments->rowCount(); ?></span>
            </button>
        </div>

        <!-- TODAY -->
        <div id="today-section" class="tab-content show">
            <section class="table-section">
                <div class="section-header">Today's Appointments</div>
                <table class="appointment-table">
                    <thead><tr><th>Patient</th><th>Doctor</th><th>Time</th><th>Purpose</th><th>Status</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php if ($today_appointments->rowCount() > 0): ?>
                            <?php while ($row = $today_appointments->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?php echo $row['patient_name']; ?></td>
                                <td>Dr. <?php echo $row['doctor_name']; ?></td>
                                <td><?php echo $row['appointment_time']; ?></td>
                                <td><?php echo $row['purpose']; ?></td>
                                <td><span class="status <?php echo $row['status']; ?>"><?php echo ucfirst($row['status']); ?></span></td>
                                <td>
                                    <?php if($row['status'] == 'completed'): ?>
                                        <span style="color:#64748b;">Completed</span>
                                    <?php elseif($row['purpose'] == 'Follow-up'): ?>
                                        <a href="http://localhost/clinic1/view/admin/view_appointment_followup.php?id=<?php echo $row['id']; ?>" class="action-btn view-btn">View</a>
                                    <?php else: ?>
                                        <a href="http://localhost/clinic1/view/admin/view_appointment.php?id=<?php echo $row['id']; ?>" class="action-btn view-btn">View</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="empty-row">No appointments today</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </div>

        <!-- UPCOMING (future only, pending) -->
        <div id="upcoming-section" class="tab-content">
            <section class="table-section">
                <div class="section-header">Upcoming Appointments</div>
                <table class="appointment-table">
                    <thead><tr><th>Patient</th><th>Doctor</th><th>Date</th><th>Time</th><th>Purpose</th><th>Status</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php if ($upcoming_appointments->rowCount() > 0): ?>
                            <?php while ($row = $upcoming_appointments->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?php echo $row['patient_name']; ?></td>
                                <td>Dr. <?php echo $row['doctor_name']; ?></td>
                                <td><?php echo $row['appointment_date']; ?></td>
                                <td><?php echo $row['appointment_time']; ?></td>
                                <td><?php echo $row['purpose']; ?></td>
                                <td><span class="status <?php echo $row['status']; ?>"><?php echo ucfirst($row['status']); ?></span></td>
                                <td>
                                    <?php if($row['purpose'] == 'Follow-up'): ?>
                                        <a href="http://localhost/clinic1/view/admin/view_appointment_followup.php?id=<?php echo $row['id']; ?>" class="action-btn view-btn">View</a>
                                    <?php else: ?>
                                        <a href="http://localhost/clinic1/view/admin/view_appointment.php?id=<?php echo $row['id']; ?>" class="action-btn view-btn">View</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="7" class="empty-row">No upcoming appointments</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </div>

        <!-- FOLLOW-UP (Follow-up purpose, pending, today + future) -->
        <div id="followup-section" class="tab-content">
            <section class="table-section">
                <div class="section-header">Follow-Up Appointments</div>
                <table class="appointment-table">
                    <thead><tr><th>Patient</th><th>Doctor</th><th>Date</th><th>Time</th><th>Status</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php if ($followup_appointments->rowCount() > 0): ?>
                            <?php while ($row = $followup_appointments->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?php echo $row['patient_name']; ?></td>
                                <td>Dr. <?php echo $row['doctor_name']; ?></td>
                                <td><?php echo $row['appointment_date']; ?></td>
                                <td><?php echo $row['appointment_time']; ?></td>
                                <td><span class="status <?php echo $row['status']; ?>"><?php echo ucfirst($row['status']); ?></span></td>
                                <td><a href="http://localhost/clinic1/view/admin/view_appointment_followup.php?id=<?php echo $row['id']; ?>" class="action-btn view-btn">View</a></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="empty-row">No follow-up appointments</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </div>

        <!-- ALL (pending + missed, any purpose) -->
        <div id="all-section" class="tab-content">
            <section class="table-section">
                <div class="section-header">All Appointments</div>
                <table class="appointment-table">
                    <thead><tr><th>Patient</th><th>Doctor</th><th>Date</th><th>Time</th><th>Purpose</th><th>Status</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php if ($all_appointments->rowCount() > 0): ?>
                            <?php while ($row = $all_appointments->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?php echo $row['patient_name']; ?></td>
                                <td>Dr. <?php echo $row['doctor_name']; ?></td>
                                <td><?php echo $row['appointment_date']; ?></td>
                                <td><?php echo $row['appointment_time']; ?></td>
                                <td><?php echo $row['purpose']; ?></td>
                                <td><span class="status <?php echo $row['status']; ?>"><?php echo ucfirst($row['status']); ?></span></td>
                                <td>
                                    <?php if($row['status'] == 'missed'): ?>
                                        <a href="http://localhost/clinic1/view/admin/view_appointment_followup.php?id=<?php echo $row['id']; ?>" class="action-btn view-btn">View</a>
                                    <?php elseif($row['purpose'] == 'Follow-up'): ?>
                                        <a href="http://localhost/clinic1/view/admin/view_appointment_followup.php?id=<?php echo $row['id']; ?>" class="action-btn view-btn">View</a>
                                    <?php else: ?>
                                        <a href="http://localhost/clinic1/view/admin/view_appointment.php?id=<?php echo $row['id']; ?>" class="action-btn view-btn">View</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="7" class="empty-row">No appointments</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </div>

        <!-- COMPLETED (no action) -->
        <div id="completed-section" class="tab-content">
            <section class="table-section">
                <div class="section-header">Completed Appointments</div>
                <table class="appointment-table">
                    <thead><tr><th>Patient</th><th>Doctor</th><th>Date</th><th>Time</th><th>Purpose</th><th>Status</th></tr></thead>
                    <tbody>
                        <?php if ($completed_appointments->rowCount() > 0): ?>
                            <?php while ($row = $completed_appointments->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?php echo $row['patient_name']; ?></td>
                                <td>Dr. <?php echo $row['doctor_name']; ?></td>
                                <td><?php echo $row['appointment_date']; ?></td>
                                <td><?php echo $row['appointment_time']; ?></td>
                                <td><?php echo $row['purpose']; ?></td>
                                <td><span class="status completed">Completed</span></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="empty-row">No completed appointments</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </div>

    </main>
</div>
<script src="../js/admin.js"></script>
</body>
</html>
