<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: http://localhost/clinic1/view/login/login.php");
    exit();
}

require_once 'C:\xampp\htdocs\clinic1\config\Database.php';
require_once 'C:\xampp\htdocs\clinic1\model\Appointment.php';
require_once 'C:\xampp\htdocs\clinic1\model\User.php';

$database = new Database();
$conn = $database->connect();
$appointment_model = new Appointment($conn);
$user_model = new User($conn);

$doctor_info = $user_model->getUserById($_SESSION['user_id']);
$doc_id = $_SESSION['user_id'];

$appointment_model->markMissedAppointments();

$today_appointments    = $appointment_model->getDoctorTodayAppointments($doc_id);
$upcoming_appointments = $appointment_model->getDoctorUpcomingOnly($doc_id);
$followup_appointments = $appointment_model->getDoctorFollowUps($doc_id);
$all_appointments      = $appointment_model->getDoctorAllTodayAndFuture($doc_id);
$completed_appointments = $appointment_model->getDoctorCompletedAppointments($doc_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Appointments</title>
    <link rel="stylesheet" href="../css/doctor.css">
</head>
<body>
<div class="dashboard-container">

    <aside class="sidebar">
        <div class="sidebar-top">
            <img src="http://localhost/clinic1/logo.jpg" class="sidebar-logo">
            <div class="sidebar-clinic-name">
                <h2>SwiftCare</h2>
            </div>
        </div>

        <nav class="sidebar-menu">
            <a href="http://localhost/clinic1/view/doctor/doctor_dashboard.php" class="menu-item">Dashboard</a>
            <a href="http://localhost/clinic1/view/doctor/doctor_appointments.php" class="menu-item active">My Appointments</a>
            <a href="http://localhost/clinic1/view/doctor/doctor_followup.php" class="menu-item">Follow-Up</a>
        </nav>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <div class="topbar-left">
                <div class="clinic-text">
                    <h1>My Appointments</h1>
                </div>
            </div>
            <div class="user-menu">
                <button type="button" class="user-menu-toggle" onclick="toggleUserMenu(this)">
                    <?php if(!empty($doctor_info['profile_photo'])): ?>
                    <img src="http://localhost/clinic1/uploads/<?php echo $doctor_info['profile_photo']; ?>" class="user-avatar" style="object-fit: cover;">
                    <?php else: ?>
                    <div class="user-avatar"></div>
                    <?php endif; ?>
                    <span class="user-name">Dr. <?php echo $_SESSION['name']; ?></span>
                    <span class="user-role-badge">Doctor</span>
                    <span class="dropdown-arrow">&#9662;</span>
                </button>
                <div class="user-dropdown">
                    <a href="http://localhost/clinic1/view/doctor/my_profile.php" class="dropdown-item">My Profile</a>
                    <a href="http://localhost/clinic1/controller/logoutController.php" class="dropdown-item signout" onclick="return confirm('Logout?')">Sign Out</a>
                </div>
            </div>
        </header>

        <!-- TABS -->
        <div class="tabs-container">
            <button class="tab-btn active" onclick="showDocTab('today')" id="tab-today">
                Today <span class="tab-count"><?php echo $today_appointments->rowCount(); ?></span>
            </button>
            <button class="tab-btn" onclick="showDocTab('upcoming')" id="tab-upcoming">
                Upcoming <span class="tab-count"><?php echo $upcoming_appointments->rowCount(); ?></span>
            </button>
            <button class="tab-btn" onclick="showDocTab('followup')" id="tab-followup">
                Follow-Up <span class="tab-count"><?php echo $followup_appointments->rowCount(); ?></span>
            </button>
            <button class="tab-btn" onclick="showDocTab('all')" id="tab-all">
                All <span class="tab-count"><?php echo $all_appointments->rowCount(); ?></span>
            </button>
            <button class="tab-btn" onclick="showDocTab('completed')" id="tab-completed">
                Completed <span class="tab-count"><?php echo $completed_appointments->rowCount(); ?></span>
            </button>
        </div>

        <!-- TODAY -->
        <div id="today-section" class="tab-content" style="display:block;">
            <section class="table-section">
                <div class="section-header">Today's Appointments</div>
                <table class="appointment-table">
                    <thead><tr><th>Patient</th><th>Time</th><th>Purpose</th><th>Status</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php if ($today_appointments->rowCount() > 0): ?>
                            <?php while ($row = $today_appointments->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?php echo $row['patient_name']; ?></td>
                                <td><?php echo $row['appointment_time']; ?></td>
                                <td><?php echo $row['purpose']; ?></td>
                                <td><span class="status <?php echo $row['status']; ?>"><?php echo ucfirst($row['status']); ?></span></td>
                                <td>
                                    <?php if($row['status'] == 'completed'): ?>
                                    <span style="color:#64748b;">Completed</span>
                                    <?php elseif($row['status'] == 'pending' || $row['status'] == 'confirmed'): ?>
                                    <a href="http://localhost/clinic1/view/doctor/doctor_consultation.php?id=<?php echo $row['id']; ?>" class="action-btn consult-btn">Consult</a>
                                    <?php else: ?>
                                    <span style="color:#64748b;"><?php echo ucfirst($row['status']); ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5" style="text-align:center;color:#64748b;">No appointments today</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </div>

        <!-- UPCOMING -->
        <div id="upcoming-section" class="tab-content" style="display:none;">
            <section class="table-section">
                <div class="section-header">Upcoming Appointments</div>
                <table class="appointment-table">
                    <thead><tr><th>Patient</th><th>Date</th><th>Time</th><th>Purpose</th><th>Status</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php if ($upcoming_appointments->rowCount() > 0): ?>
                            <?php while ($row = $upcoming_appointments->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?php echo $row['patient_name']; ?></td>
                                <td><?php echo $row['appointment_date']; ?></td>
                                <td><?php echo $row['appointment_time']; ?></td>
                                <td><?php echo $row['purpose']; ?></td>
                                <td><span class="status <?php echo $row['status']; ?>"><?php echo ucfirst($row['status']); ?></span></td>
                                <td>
                                    <a href="http://localhost/clinic1/view/doctor/doctor_consultation.php?id=<?php echo $row['id']; ?>" class="action-btn consult-btn">Consult</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" style="text-align:center;color:#64748b;">No upcoming appointments</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </div>

        <!-- FOLLOW-UP -->
        <div id="followup-section" class="tab-content" style="display:none;">
            <section class="table-section">
                <div class="section-header">Follow-Up Appointments</div>
                <table class="appointment-table">
                    <thead><tr><th>Patient</th><th>Date</th><th>Time</th><th>Purpose</th><th>Status</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php if ($followup_appointments->rowCount() > 0): ?>
                            <?php while ($row = $followup_appointments->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?php echo $row['patient_name']; ?></td>
                                <td><?php echo $row['appointment_date']; ?></td>
                                <td><?php echo $row['appointment_time']; ?></td>
                                <td><?php echo $row['purpose']; ?></td>
                                <td><span class="status <?php echo $row['status']; ?>"><?php echo ucfirst($row['status']); ?></span></td>
                                <td>
                                    <a href="http://localhost/clinic1/view/doctor/doctor_consultation.php?id=<?php echo $row['id']; ?>" class="action-btn consult-btn">Consult</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" style="text-align:center;color:#64748b;">No follow-up appointments</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </div>

        <!-- ALL -->
        <div id="all-section" class="tab-content" style="display:none;">
            <section class="table-section">
                <div class="section-header">All Appointments</div>
                <table class="appointment-table">
                    <thead><tr><th>Patient</th><th>Date</th><th>Time</th><th>Purpose</th><th>Status</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php if ($all_appointments->rowCount() > 0): ?>
                            <?php while ($row = $all_appointments->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?php echo $row['patient_name']; ?></td>
                                <td><?php echo $row['appointment_date']; ?></td>
                                <td><?php echo $row['appointment_time']; ?></td>
                                <td><?php echo $row['purpose']; ?></td>
                                <td><span class="status <?php echo $row['status']; ?>"><?php echo ucfirst($row['status']); ?></span></td>
                                <td>
                                    <?php if($row['status'] == 'missed'): ?>
                                    <a href="http://localhost/clinic1/view/doctor/doctor_consultation.php?id=<?php echo $row['id']; ?>" class="action-btn view-btn">View</a>
                                    <?php else: ?>
                                    <a href="http://localhost/clinic1/view/doctor/doctor_consultation.php?id=<?php echo $row['id']; ?>" class="action-btn consult-btn">Consult</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" style="text-align:center;color:#64748b;">No appointments</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </div>

        <!-- COMPLETED -->
        <div id="completed-section" class="tab-content" style="display:none;">
            <section class="table-section">
                <div class="section-header">Completed Appointments</div>
                <table class="appointment-table">
                    <thead><tr><th>Patient</th><th>Date</th><th>Time</th><th>Purpose</th><th>Status</th></tr></thead>
                    <tbody>
                        <?php if ($completed_appointments->rowCount() > 0): ?>
                            <?php while ($row = $completed_appointments->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?php echo $row['patient_name']; ?></td>
                                <td><?php echo $row['appointment_date']; ?></td>
                                <td><?php echo $row['appointment_time']; ?></td>
                                <td><?php echo $row['purpose']; ?></td>
                                <td><span class="status completed">Completed</span></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5" style="text-align:center;color:#64748b;">No completed appointments</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </div>

    </main>
</div>
<script src="../js/doctor.js"></script>

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
