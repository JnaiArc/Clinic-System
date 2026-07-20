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

$edit_id = $_GET['edit'] ?? 0;
$edit_appointment = null;
$edit_consultation = null;
$edit_medicines = null;
$edit_recommendations = null;

if ($edit_id > 0) {
    $edit_appointment = $appointment_model->getAppointmentById($edit_id);
    if ($edit_appointment && (int)$edit_appointment['doctor_id'] !== (int)$doctor_id) {
        $edit_appointment = null;
    }
    if ($edit_appointment) {
        $edit_consultation = $appointment_model->getConsultationForAppointment($edit_id, $edit_appointment['patient_id']);
        if ($edit_consultation) {
            $edit_medicines = $appointment_model->getMedicinesByConsultationId($edit_consultation['id']);
            $edit_recommendations = $appointment_model->getRecommendationsByConsultationId($edit_consultation['id']);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Follow-Up Checkup</title>
    <link rel="stylesheet" href="../css/doctor.css">
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
            <a href="http://localhost/clinic1/view/doctor/doctor_dashboard.php" class="menu-item">Dashboard</a>
            <a href="http://localhost/clinic1/view/doctor/doctor_appointments.php" class="menu-item">My Appointments</a>
            <a href="http://localhost/clinic1/view/doctor/doctor_followup.php" class="menu-item active">Follow-Up Checkup</a>
            <a href="http://localhost/clinic1/view/doctor/doctor_patients.php" class="menu-item">My Patients</a>
        </nav>
    </aside>

    <main class="main-content">

        <header class="topbar">
            <div class="topbar-left">
                <div class="clinic-text">
                    <h1>Follow-Up Checkup</h1>
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

        <?php if($edit_id > 0 && $edit_appointment): ?>

        <section class="table-section" style="margin-bottom: 30px;">
            <div class="section-header">
                Appointment Information
            </div>
            <div class="info-grid" style="padding: 25px;">
                <div class="info-item"><label>Patient Name</label><span><?php echo $edit_appointment['patient_name']; ?></span></div>
                <div class="info-item"><label>Follow-Up Date</label><span><?php echo $edit_appointment['appointment_date']; ?></span></div>
                <div class="info-item"><label>Time</label><span><?php echo $edit_appointment['appointment_time']; ?></span></div>
                <div class="info-item"><label>Status</label><span class="status <?php echo $edit_appointment['status']; ?>"><?php echo ucfirst($edit_appointment['status']); ?></span></div>
            </div>
        </section>

        <?php if($edit_consultation): ?>
        <form method="POST" action="http://localhost/clinic1/controller/FollowUpController.php">
            <input type="hidden" name="appointment_id" value="<?php echo $edit_id; ?>">

            <section class="table-section" style="margin-bottom: 30px; background: #f8fafc; border: 1px solid #e2e8f0;">
                <div class="section-header">Prescription (Check when done)</div>

                <?php if($edit_medicines && $edit_medicines->rowCount() > 0): ?>
                <div style="padding: 0 25px 25px;">
                    <label style="display:block; margin-bottom:10px; font-weight:600; color:#17324d;">Medicines</label>
                    <?php while($med = $edit_medicines->fetch(PDO::FETCH_ASSOC)):
                        $is_done = isset($med['is_done']) ? $med['is_done'] : 0;
                        $bg=$is_done?'#f0fdf4':'white'; $bd=$is_done?'1px solid #86efac':'none'; $tc=$is_done?'#15803d':'inherit'; $tw=$is_done?'600':'normal';
                    ?>
                    <div style="margin-bottom:8px; padding:10px 15px; background:<?php echo $bg;?>; border:<?php echo $bd;?>; border-radius:6px; display:flex; align-items:center; gap:12px;">
                        <input type="checkbox" name="medicines_done[]" value="<?php echo $med['id']; ?>" <?php echo $is_done ? 'checked' : ''; ?> style="width:18px; height:18px; accent-color:#16a34a;">
                        <span style="flex:1; color:<?php echo $tc;?>; font-weight:<?php echo $tw;?>;"><?php echo $med['medicine_name']; ?> - <?php echo $med['dosage']; ?> x <?php echo $med['frequency']; ?> for <?php echo $med['duration']; ?></span>
                        <?php if($is_done): ?><span style="color:#16a34a;font-weight:bold;">✓</span><?php endif; ?>
                    </div>
                    <?php endwhile; ?>
                </div>
                <?php endif; ?>

                <?php if($edit_recommendations && $edit_recommendations->rowCount() > 0): ?>
                <div style="padding: 0 25px 25px;">
                    <label style="display:block; margin-bottom:10px; font-weight:600; color:#17324d;">Recommendations</label>
                    <?php while($rec = $edit_recommendations->fetch(PDO::FETCH_ASSOC)):
                        $is_done = isset($rec['is_done']) ? $rec['is_done'] : 0;
                        $bg=$is_done?'#f0fdf4':'white'; $bd=$is_done?'1px solid #86efac':'none'; $tc=$is_done?'#15803d':'inherit'; $tw=$is_done?'600':'normal';
                    ?>
                    <div style="margin-bottom:8px; padding:10px 15px; background:<?php echo $bg;?>; border:<?php echo $bd;?>; border-radius:6px; display:flex; align-items:center; gap:12px;">
                        <input type="checkbox" name="recommendations_done[]" value="<?php echo $rec['id']; ?>" <?php echo $is_done ? 'checked' : ''; ?> style="width:18px; height:18px; accent-color:#16a34a;">
                        <span style="flex:1; color:<?php echo $tc;?>; font-weight:<?php echo $tw;?>;"><?php echo $rec['recommendation']; ?></span>
                        <?php if($is_done): ?><span style="color:#16a34a;font-weight:bold;">✓</span><?php endif; ?>
                    </div>
                    <?php endwhile; ?>
                </div>
                <?php endif; ?>

                <?php if((!$edit_medicines || $edit_medicines->rowCount() == 0) && (!$edit_recommendations || $edit_recommendations->rowCount() == 0)): ?>
                <p style="padding: 0 25px 25px; color: #64748b;">No prescription items on record for this patient yet.</p>
                <?php endif; ?>
            </section>

            <div class="btn-row" style="padding: 0 0 25px;">
                <button type="submit" name="savePrescription" class="save-btn">Save</button>
            </div>
        </form>
        <?php else: ?>
        <section class="table-section">
            <div class="section-header">Prescription</div>
            <p style="padding: 25px; color: #64748b;">No previous consultation found for this patient.</p>
        </section>
        <?php endif; ?>

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
                            <td><a href="http://localhost/clinic1/view/doctor/doctor_followup.php?edit=<?php echo $row['id']; ?>" class="action-btn view-btn">Edit</a></td>
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