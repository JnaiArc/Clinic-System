<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: http://localhost/clinic1/view/login/login.php");
    exit();
}

require_once 'C:\xampp\htdocs\clinic1\config\Database.php';
require_once 'C:\xampp\htdocs\clinic1\model\Patient.php';
require_once 'C:\xampp\htdocs\clinic1\model\User.php';

$database = new Database();
$conn = $database->connect();
$patient = new Patient($conn);
$user = new User($conn);

$user_info = $user->getUserById($_SESSION['user_id']);
$patient_id = $_GET['patient_id'] ?? 0;
$selected_patient = $patient_id ? $patient->getPatientById($patient_id) : null;

// get date today
$today = date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Consultation</title>
    <link rel="stylesheet" href="../css/admin_dashboard.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>

<body>

<div class="dashboard-container">
    <aside class="sidebar-simple">
        <a href="http://localhost/clinic1/view/admin/admin_patientRecord.php" class="back-btn-top">
            <div class="icon">←</div>
            <span>Back</span>
        </a>
    </aside>

    <main class="main-content">

        <header class="topbar">
            <div class="topbar-left">
                <div class="clinic-text">
                    <h1>Schedule Appointment</h1>
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

        <section class="table-section">

            <div class="section-header">Schedule Appointment</div>

            <form method="POST" action="http://localhost/clinic1/controller/AppointmentController.php" class="appointment-body">
                
                <div class="form-group">
                    <label>Patient Name</label>
                    <input type="text" value="<?php echo $selected_patient ? $selected_patient['first_name'].' '.$selected_patient['last_name'] : ''; ?>" readonly>
                    <input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
                </div>

                <div class="form-group">
                    <label>Complaint</label>
                    <input type="text" name="complaint" placeholder="e.g. Fever, Headache" required>
                </div>

                <div class="form-group">
                    <label>Date</label>
                    <input type="date" name="appointment_date" id="appointmentDate" min="<?php echo $today; ?>" required>
                </div>

                <div class="form-group">
                    <label>Doctor</label>
                    <select id="doctorSelect" name="doctor_id" required>
                        <option value="">Select a date first</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Time</label>
                    <select id="timeSelect" name="appointment_time" required>
                        <option value="">Select a doctor first</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Purpose</label>
                    <select name="purpose" required>
                        <option value="Check-up" selected>Check-up</option>
                        <option value="Consultation">Consultation</option>
                        <option value="Follow-up">Follow-up</option>
                        <option value="Vaccination">Vaccination</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Consultation Type</label>
                    <select name="consultation_type" required>
                        <option value="In Person" selected>In Person</option>
                        <option value="Online">Online</option>
                    </select>
                </div>

                <div class="view-buttons span-2">
                    <button type="submit" name="bookAppointment" class="save-btn">Confirm Appointment</button>
                    <a href="http://localhost/clinic1/view/admin/admin_patientRecord.php" class="back-link">Cancel</a>
                </div>

            </form>

        </section>

    </main>

</div>

<script src="../js/admin.js"></script>
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