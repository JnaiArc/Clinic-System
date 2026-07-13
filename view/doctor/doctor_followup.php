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

$doctor_id = $_SESSION['user_id'];

$doctor_info = $user_model->getUserById($doctor_id);
$followups = $appointment_model->getDoctorFollowUps($doctor_id);

$view_id = $_GET['view'] ?? 0;
$view_appointment = null;
$view_consultation = null;
$view_medicines = null;
$view_recommendations = null;

if($view_id > 0) {
    $view_appointment = $appointment_model->getAppointmentById($view_id);
    if($view_appointment) {
        $view_consultation = $appointment_model->getPreviousConsultation($view_appointment['patient_id'], $doctor_id, $view_appointment['appointment_date']);
        if($view_consultation) {
            $view_medicines = $appointment_model->getMedicinesByConsultationId($view_consultation['id']);
            $view_recommendations = $appointment_model->getRecommendationsByConsultationId($view_consultation['id']);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Follow-Up</title>
    <link rel="stylesheet" href="../css/doctor.css">
</head>

<body>

<div class="dashboard-container">

    <aside class="sidebar">
        <div class="sidebar-top">
            <?php if(!empty($doctor_info['profile_photo'])): ?>
            <img src="http://localhost/clinic1/uploads/<?php echo $doctor_info['profile_photo']; ?>" class="profile-circle" style="object-fit: cover;">
            <?php else: ?>
            <div class="profile-circle"></div>
            <?php endif; ?>
            <div class="profile-name"><h2>Dr. <?php echo $_SESSION['name']; ?></h2></div>
        </div>
        <nav class="sidebar-menu">
            <a href="http://localhost/clinic1/view/doctor/doctor_dashboard.php" class="menu-item">Dashboard</a>
            <a href="http://localhost/clinic1/view/doctor/doctor_appointments.php" class="menu-item">My Appointments</a>
            <a href="http://localhost/clinic1/view/doctor/doctor_followup.php" class="menu-item active">Follow-Up</a>
            <a href="http://localhost/clinic1/controller/logoutController.php" class="menu-item logout-btn" onclick="return confirm('Logout?')">Logout</a>
        </nav>
    </aside>

    <main class="main-content">

        <header class="topbar">
            <div class="topbar-left">
                <img src="http://localhost/clinic1/logo.jpg" class="clinic-logo">
                <div class="clinic-text"><h1>SwiftCare Clinic</h1><p>Follow-Up Appointments</p></div>
            </div>
            <div class="admin-box">Doctor</div>
        </header>

        <?php if($view_id > 0 && $view_appointment): ?>
        <section class="table-section" style="margin-bottom: 30px; background: #f0f9ff; border: 2px solid #0ea5e9;">
            <div class="section-header">
                Previous Consultation (Read Only)
                <a href="http://localhost/clinic1/view/doctor/doctor_followup.php" style="float: right; font-size: 14px; color: #17324d;">← Back</a>
            </div>
            <div class="info-grid" style="padding: 25px;">
                <div class="info-item"><label>Patient Name</label><span><?php echo $view_appointment['patient_name']; ?></span></div>
                <div class="info-item"><label>Follow-Up Date</label><span><?php echo $view_appointment['appointment_date']; ?></span></div>
                <div class="info-item"><label>Time</label><span><?php echo $view_appointment['appointment_time']; ?></span></div>
            </div>
            <?php if($view_consultation): ?>
            <div style="padding: 0 25px 25px;">
                <div class="info-item" style="background: white;"><label>Findings</label><span><?php echo nl2br($view_consultation['findings']); ?></span></div>
            </div>
            <?php if($view_medicines && $view_medicines->rowCount() > 0): ?>
            <div style="padding: 0 25px 25px;">
                <label style="display:block; margin-bottom:10px; font-weight:600; color:#17324d;">Previous Medicines</label>
                <?php while($med = $view_medicines->fetch(PDO::FETCH_ASSOC)): 
                    $is_done = isset($med['is_done']) ? $med['is_done'] : 0;
                    $bg=$is_done?'#f0fdf4':'white'; $bd=$is_done?'1px solid #86efac':'none'; $tc=$is_done?'#15803d':'inherit'; $tw=$is_done?'600':'normal'; 
                ?>
                <div style="margin-bottom:8px; padding:10px 15px; background:<?php echo $bg;?>; border:<?php echo $bd;?>; border-radius:6px; display:flex; align-items:center; gap:12px;">
                    <input type="checkbox" <?php echo $is_done?'checked':''; ?> disabled style="width:18px; height:18px; accent-color:#16a34a;">
                    <span style="flex:1; color:<?php echo $tc;?>; font-weight:<?php echo $tw;?>;"><?php echo $med['medicine_name'].' - '.$med['dosage'].' x '.$med['frequency'].' for '.$med['duration']; ?></span>
                    <?php if($is_done): ?><span style="color:#16a34a;font-weight:bold;">✓</span><?php endif; ?>
                </div>
                <?php endwhile; ?>
            </div>
            <?php endif; ?>
            <?php if($view_recommendations && $view_recommendations->rowCount() > 0): ?>
            <div style="padding: 0 25px 25px;">
                <label style="display:block; margin-bottom:10px; font-weight:600; color:#17324d;">Previous Recommendations</label>
                <?php while($rec = $view_recommendations->fetch(PDO::FETCH_ASSOC)): 
                    $is_done = isset($rec['is_done']) ? $rec['is_done'] : 0;
                    $bg=$is_done?'#f0fdf4':'white'; $bd=$is_done?'1px solid #86efac':'none'; $tc=$is_done?'#15803d':'inherit'; $tw=$is_done?'600':'normal'; 
                ?>
                <div style="margin-bottom:8px; padding:10px 15px; background:<?php echo $bg;?>; border:<?php echo $bd;?>; border-radius:6px; display:flex; align-items:center; gap:12px;">
                    <input type="checkbox" <?php echo $is_done?'checked':''; ?> disabled style="width:18px; height:18px; accent-color:#16a34a;">
                    <span style="flex:1; color:<?php echo $tc;?>; font-weight:<?php echo $tw;?>;"><?php echo $rec['recommendation']; ?></span>
                    <?php if($is_done): ?><span style="color:#16a34a;font-weight:bold;">✓</span><?php endif; ?>
                </div>
                <?php endwhile; ?>
            </div>
            <?php endif; ?>
            <?php else: ?>
            <p style="padding: 25px; color: #64748b;">No previous consultation found.</p>
            <?php endif; ?>
        </section>
        <div style="padding: 0 25px 25px;">
            <a href="http://localhost/clinic1/view/doctor/doctor_consultation.php?id=<?php echo $view_id; ?>" style="display:inline-block; padding:14px 28px; background:#17324d; color:white; text-decoration:none; border-radius:8px; font-weight:600;">Start Follow-Up Consultation</a>
        </div>
        <?php else: ?>
        <section class="table-section">
            <div class="section-header">Follow-Up List</div>
            <table class="appointment-table">
                <thead><tr><th>Patient Name</th><th>Date</th><th>Time</th><th>Purpose</th><th>Status</th><th>Action</th></tr></thead>
                <tbody>
                    <?php if ($followups->rowCount() > 0): ?>
                        <?php while ($row = $followups->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?php echo $row['patient_name']; ?></td>
                            <td><?php echo $row['appointment_date']; ?></td>
                            <td><?php echo $row['appointment_time']; ?></td>
                            <td><?php echo $row['purpose']; ?></td>
                            <td><span class="status <?php echo $row['status']; ?>"><?php echo ucfirst($row['status']); ?></span></td>
                            <td><a href="http://localhost/clinic1/view/doctor/doctor_followup.php?view=<?php echo $row['id']; ?>" class="action-btn view-btn">View</a></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" style="text-align: center; color: #64748b;">No follow-up appointments</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
        <?php endif; ?>

    </main>

</div>

</body>

</html>