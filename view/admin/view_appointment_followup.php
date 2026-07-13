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
$appointment = new Appointment($conn);
$user = new User($conn);

$user_info = $user->getUserById($_SESSION['user_id']);

$appointment_id = (int)$_GET['id'];
$appointment_data = $appointment->getAppointmentById($appointment_id);

$consultation = null;
$medicines = null;
$recommendations = null;

if ($appointment_data) {
    $consultation = $appointment->getConsultationForAppointment($appointment_id, $appointment_data['patient_id']);
    
    if ($consultation) {
        $medicines = $appointment->getMedicinesByConsultationId($consultation['id']);
        $recommendations = $appointment->getRecommendationsByConsultationId($consultation['id']);
    }
}

$is_followup = ($appointment_data['purpose'] === 'Follow-up');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Follow-Up Appointment</title>
    <link rel="stylesheet" href="../css/admin_dashboard.css">
    <link rel="stylesheet" href="../css/admin_info.css">
    <style>
        .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        .edit-rx-btn { padding: 8px 16px; background-color: #3b82f6; color: white; border-radius: 6px; text-decoration: none; font-size: 14px; font-weight: 600; }
    </style>
</head>

<body>
<div class="dashboard-container">
    <aside class="sidebar-simple">
        <a href="http://localhost/clinic1/view/admin/admin_appointments.php" class="back-btn-top">
            <div class="icon">←</div>
            <span>Back</span>
        </a>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <div class="topbar-left">
                <div class="clinic-text">
                    <h1>Follow-Up Details</h1>
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

        <section class="table-section section-gap">
            <div class="section-header">Appointment Details</div>
            <div class="info-grid info-grid-pad">
                <div class="info-item"><label>Patient</label><span><?php echo $appointment_data['patient_name']; ?></span></div>
                <div class="info-item"><label>Doctor</label><span>Dr. <?php echo $appointment_data['doctor_name']; ?></span></div>
                <div class="info-item"><label>Date</label><span><?php echo $appointment_data['appointment_date']; ?></span></div>
                <div class="info-item"><label>Time</label><span><?php echo $appointment_data['appointment_time']; ?></span></div>
                <div class="info-item"><label>Purpose</label><span><?php echo $appointment_data['purpose']; ?></span></div>
                <div class="info-item"><label>Status</label><span class="status <?php echo $appointment_data['status']; ?>"><?php echo ucfirst($appointment_data['status']); ?></span></div>
            </div>
        </section>

        <!-- view only perscription -->
        <section class="table-section rx-section">
            <div class="section-header">
                <span>Prescription (View Only)</span>
                <?php if($medicines || $recommendations): ?>
                <a href="http://localhost/clinic1/view/admin/view_followup_edit.php?id=<?php echo $appointment_id; ?>" class="edit-rx-btn">Edit Prescription</a>
                <?php endif; ?>
            </div>
            
            <?php if($medicines && $medicines->rowCount() > 0): ?>
            <div class="info-grid-pad">
                <label class="rx-label">Medicines</label>
                <?php while($med = $medicines->fetch(PDO::FETCH_ASSOC)): 
                    $is_done = isset($med['is_done']) ? $med['is_done'] : 0;
                    $item_class = $is_done ? 'rx-item-checked' : 'rx-item';
                ?>
                <div class="<?php echo $item_class; ?>">
                    <input type="checkbox" <?php echo $is_done ? 'checked' : ''; ?> disabled class="rx-checkbox">
                    <span class="rx-flex"><?php echo $med['medicine_name']; ?> - <?php echo $med['dosage']; ?> x <?php echo $med['frequency']; ?> for <?php echo $med['duration']; ?></span>
                </div>
                <?php endwhile; ?>
            </div>
            <?php endif; ?>
            
            <?php if($recommendations && $recommendations->rowCount() > 0): ?>
            <div class="info-grid-pad">
                <label class="rx-label">Recommendations</label>
                <?php while($rec = $recommendations->fetch(PDO::FETCH_ASSOC)): 
                    $is_done = isset($rec['is_done']) ? $rec['is_done'] : 0;
                    $item_class = $is_done ? 'rx-item-checked' : 'rx-item';
                ?>
                <div class="<?php echo $item_class; ?>">
                    <input type="checkbox" <?php echo $is_done ? 'checked' : ''; ?> disabled class="rx-checkbox">
                    <span class="rx-flex"><?php echo $rec['recommendation']; ?></span>
                </div>
                <?php endwhile; ?>
            </div>
            <?php endif; ?>
        </section>
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