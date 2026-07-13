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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Patient</title>
    <link rel="stylesheet" href="../css/admin_dashboard.css">
    <link rel="stylesheet" href="../css/admin_add_view.css">
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
                    <h1>Patient Registration</h1>
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

        <!-- FORM -->
        <section class="table-section">

            <div class="section-header">
                Patient Registration Form
            </div>

            <form method="POST" action="http://localhost/clinic1/controller/PatientController.php" class="patient-form">
                <div class="form-group">
                    <label>First Name <span style="color:red">*</span></label>
                    <input type="text" name="first_name" required>
                </div>
                <div class="form-group">
                    <label>Last Name <span style="color:red">*</span></label>
                    <input type="text" name="last_name" required>
                </div>
                <div class="form-group">
                    <label>Gender <span style="color:red">*</span></label>
                    <select name="gender" required>
                        <option value="">Select</option>
                        <option>Male</option>
                        <option>Female</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Birthdate <span style="color:red">*</span></label>
                    <input type="date" name="birthdate" required max="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="form-group">
                    <label>Contact Number <span style="color:red">*</span></label>
                    <input type="text" name="phone" required maxlength="11" pattern="[0-9]{11}">
                </div>
                <div class="form-group">
                    <label>Email <span style="color:red">*</span></label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Address <span style="color:red">*</span></label>
                    <input type="text" name="address" required>
                </div>
                <div class="form-group">
                    <label>Emergency Contact</label>
                    <input type="text" name="emergency_contact" maxlength="11" pattern="[0-9]{11}">
                </div>
                <div class="form-group">
                    <label>Allergies</label>
                    <input type="text" name="allergies">
                </div>
                <div class="form-group" style="grid-column: span 2;">
                    <label>Medical History</label>
                    <textarea name="medical_history"></textarea>
                </div>

                <div class="button-group">
                    <button type="submit" name="addPatient" class="save-btn">Save Patient</button>
                    <a href="http://localhost/clinic1/view/admin/admin_patientRecord.php" class="clear-btn">Cancel</a>
                </div>
            </form>

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