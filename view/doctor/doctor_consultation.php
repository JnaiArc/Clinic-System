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

$appointment_id = $_GET['id'] ?? 0;
$doctor_id = $_SESSION['user_id'];

$appointment = $appointment_model->getAppointmentById($appointment_id);
$doctor_info = $user_model->getUserById($doctor_id);

$is_followup = ($appointment['purpose'] == 'Follow-up');
$previous_consultation = null;
$previous_medicines = null;
$previous_recommendations = null;

if ($is_followup) {
    $previous_consultation = $appointment_model->getPreviousConsultation($appointment['patient_id'], $doctor_id, $appointment['appointment_date']);
    if ($previous_consultation) {
        $previous_medicines = $appointment_model->getMedicinesByConsultationId($previous_consultation['id']);
        $previous_recommendations = $appointment_model->getRecommendationsByConsultationId($previous_consultation['id']);
    }
}

$age = $appointment['patient_birthdate'] ? date('Y') - date('Y', strtotime($appointment['patient_birthdate'])) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Consultation</title>
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
                    <h1><?php echo $is_followup ? 'Follow-Up Consultation' : 'New Consultation'; ?></h1>
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

        <section class="table-section" style="margin-bottom: 30px;">
            <div class="section-header">Patient Information</div>
            <div class="info-grid">
                <div class="info-item"><label>Name</label><span><?php echo $appointment['patient_name']; ?></span></div>
                <div class="info-item"><label>Age</label><span><?php echo $age; ?> years old</span></div>
                <div class="info-item"><label>Gender</label><span><?php echo $appointment['patient_gender']; ?></span></div>
                <div class="info-item"><label>Phone</label><span><?php echo $appointment['patient_phone']; ?></span></div>
                <div class="info-item"><label>Purpose</label><span><?php echo $appointment['purpose']; ?></span></div>
            </div>
        </section>

        <?php if($is_followup && $previous_consultation): ?>
        <section class="table-section" style="margin-bottom: 30px; background: #f0f9ff; border: 2px solid #0ea5e9;">
            <div class="section-header">Previous Consultation (Read Only)</div>
            <div class="info-grid" style="padding: 25px;">
                <div class="info-item" style="background: white;"><label>Findings</label><span><?php echo nl2br($previous_consultation['findings']); ?></span></div>
            </div>
            <?php if($previous_medicines && $previous_medicines->rowCount() > 0): ?>
            <div style="padding: 0 25px 25px;">
                <label style="display:block; margin-bottom:10px; font-weight:600; color:#17324d;">Previous Medicines</label>
                <?php while($med = $previous_medicines->fetch(PDO::FETCH_ASSOC)): 
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
            <?php if($previous_recommendations && $previous_recommendations->rowCount() > 0): ?>
            <div style="padding: 0 25px 25px;">
                <label style="display:block; margin-bottom:10px; font-weight:600; color:#17324d;">Previous Recommendations</label>
                <?php while($rec = $previous_recommendations->fetch(PDO::FETCH_ASSOC)): 
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
        </section>
        <?php endif; ?>

        <form method="POST" action="http://localhost/clinic1/controller/ConsultationController.php">
            <input type="hidden" name="appointment_id" value="<?php echo $appointment_id; ?>">
            <input type="hidden" name="patient_id" value="<?php echo $appointment['patient_id']; ?>">

            <section class="table-section" style="margin-bottom: 30px;">
                <div class="section-header">Current Findings</div>
                <textarea name="findings" class="findings-input" placeholder="Enter your findings..."></textarea>
            </section>

            <section class="table-section" style="margin-bottom: 30px;">
                <div class="section-header">Medicines <button type="button" onclick="addMedicine()" class="add-btn">+ Add</button></div>
                <div id="medicines-container">
                    <div class="medicine-row">
                        <input type="text" name="medicine_name[]" placeholder="Medicine Name">
                        <input type="text" name="dosage[]" placeholder="Dosage">
                        <input type="text" name="frequency[]" placeholder="Frequency">
                        <input type="text" name="duration[]" placeholder="Duration">
                        <button type="button" onclick="removeMedicine(this)" class="remove-btn">×</button>
                    </div>
                </div>
            </section>

            <section class="table-section" style="margin-bottom: 30px;">
                <div class="section-header">Recommendations <button type="button" onclick="addRecommendation()" class="add-btn">+ Add</button></div>
                <div id="recommendations-container">
                    <div class="recommendation-row">
                        <input type="text" placeholder="Enter recommendation" name="recommendation[]">
                        <button type="button" onclick="removeRecommendation(this)" class="remove-btn">×</button>
                    </div>
                </div>
            </section>

            <section class="table-section" style="margin-bottom: 30px;">
                <div class="section-header">Follow-Up</div>
                <div class="followup-options">
                    <label><input type="radio" name="followup_needed" value="no" checked onchange="toggleFollowupDate()"> No Follow-up (Complete)</label>
                    <label><input type="radio" name="followup_needed" value="yes" onchange="toggleFollowupDate()"> Yes, schedule follow-up</label>
                </div>
                <div id="followup-date-container" style="display:none; margin-top:15px;">
                    <div style="margin-bottom:12px;">
                        <label style="display:block; font-weight:600; margin-bottom:6px; color:#17324d;">Follow-Up Date</label>
                        <input type="date" name="followup_date" id="followupDate" min="<?php echo date('Y-m-d'); ?>" onchange="generateFollowupTimes()" style="padding:10px; border:1px solid #cbd5e1; border-radius:8px; font-size:14px;">
                    </div>
                    <div id="followup-time-container" style="display:none;">
                        <label style="display:block; font-weight:600; margin-bottom:6px; color:#17324d;">Follow-Up Time</label>
                        <select name="followup_time" id="followupTime" style="padding:10px; border:1px solid #cbd5e1; border-radius:8px; font-size:14px; min-width:200px;">
                            <option value="">Select Time</option>
                        </select>
                    </div>
                </div>
            </section>

            <div class="save-btn-container">
                <button type="submit" name="saveConsultation" class="save-btn">Complete Consultation</button>
                <a href="http://localhost/clinic1/view/doctor/doctor_appointments.php" class="cancel-btn">Cancel</a>
            </div>
        </form>

    </main>

</div>
<script>
    var doctorScheduleDays = "<?php echo $doctor_info['schedule_days']; ?>".split(',').map(function(d){ return d.trim(); });
    var doctorStartTime = "<?php echo $doctor_info['schedule_time_start']; ?>";
    var doctorEndTime = "<?php echo $doctor_info['schedule_time_end']; ?>";
</script>
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