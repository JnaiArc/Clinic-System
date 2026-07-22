<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: http://localhost/clinic1/view/login/login.php");
    exit();
}

require_once 'C:\xampp\htdocs\clinic1\config\Database.php';
require_once 'C:\xampp\htdocs\clinic1\model\User.php';
require_once 'C:\xampp\htdocs\clinic1\model\Patient.php';

$database = new Database();
$conn = $database->connect();
$user = new User($conn);
$patient = new Patient($conn);

$user_info = $user->getUserById($_SESSION['user_id']);
$patient_data = $patient->getPatientByUserId($_SESSION['user_id']);
$profile_complete = $patient->isProfileComplete($patient_data);

// Show the form if there's no record yet (first-time fill-up) or ?edit=1 was requested.
$edit_mode = !$patient_data || (isset($_GET['edit']) && $_GET['edit'] == 1);

$error = $_SESSION['error'] ?? "";
unset($_SESSION['error']);
$success = $_SESSION['success'] ?? "";
unset($_SESSION['success']);

// Auto-fill Name/Email from the account created at registration when there's no saved profile yet.
$display_first_name = $patient_data['first_name'] ?? $user_info['first_name'];
$display_last_name  = $patient_data['last_name']  ?? $user_info['last_name'];
$display_email      = $patient_data['email']      ?? $user_info['email'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Profile | SwiftCare</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="../css/patient.css">
    <link rel="stylesheet" href="../css/patient_dashboardd.css">
    <?php include_once '../chatbot_widget.php'; ?>
</head>

<body>

    <!-- TOP NAVBAR-->
    <header class="topbar">

        <!-- Logo -->
        <div class="logo">
            <a href="patient_dashboard.php">
                <img src="../../img/logo.png" alt="SwiftCare Logo">
            </a>
            <span>SwiftCare</span>
        </div>

        <!-- Navigation -->
        <nav class="top-nav">
            <a href="patient_dashboard.php">Home</a>
            <a href="patient_request consultation.php">Request Consultation</a>
            <a href="patient_view_appointment.php">Appointment</a>
            <a href="patient_request medical.php">Request Medical Documents</a>
            <a href="patient_services.php">Services</a>
            <a href="all doctors.php"> All Doctors</a>
        </nav>

        <!-- Profile -->
        <div class="profile-menu">
            <button type="button" class="profile-menu-toggle" onclick="togglePatientMenu(this)">
                <img src="../../img/user.png" class="profile-avatar" alt="Profile">
                <span class="dropdown-arrow">&#9662;</span>
            </button>
            <div class="profile-dropdown">
                <a href="my_profile.php" class="profile-dropdown-item">My Profile</a>
                <a href="http://localhost/clinic1/controller/logoutController.php" class="profile-dropdown-item signout" onclick="return confirm('Logout?')">Sign Out</a>
            </div>
        </div>

    </header>

    <main class="main-content">

        <header class="topbar">
            <h1>Patient Profile</h1>
        </header>

        <div class="profile-wrap">

            <?php if ($success): ?>
            <div id="successAlert" class="alert-success" style="display:block;"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
            <div id="errorAlert" class="alert-success" style="display:block; background:#fee2e2; color:#991b1b; border-color:#fecaca;"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if (!$patient_data): ?>
            <p class="required-note" style="margin-bottom:10px;">
                <span style="color:red">*</span> New patient? Please fill this up first before you can request a consultation or booking.
            </p>
            <?php endif; ?>

            <?php if ($edit_mode): ?>

            <form id="profileForm" autocomplete="off" method="POST" action="http://localhost/clinic1/controller/PatientController.php">

                <div class="profile-card">
                    <h2>Update Information</h2>
                    <p class="subtitle">Manage your clinic record details</p>
                    <p class="required-note"><span style="color:red">*</span> Required Fields</p>

                    <div class="form-grid">
                        <!-- First Name (auto-filled from account) -->
                        <div class="field">
                            <label for="first_name">First Name <span style="color:red">*</span></label>
                            <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($display_first_name); ?>" required>
                        </div>

                        <!-- Last Name (auto-filled from account) -->
                        <div class="field">
                            <label for="last_name">Last Name <span style="color:red">*</span></label>
                            <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($display_last_name); ?>" required>
                        </div>

                        <!-- Gender -->
                        <div class="field">
                            <label for="gender">Gender <span style="color:red">*</span></label>
                            <select id="gender" name="gender" required>
                                <option value="">Select</option>
                                <option value="Male" <?php echo (($patient_data['gender'] ?? '') == 'Male') ? 'selected' : ''; ?>>Male</option>
                                <option value="Female" <?php echo (($patient_data['gender'] ?? '') == 'Female') ? 'selected' : ''; ?>>Female</option>
                            </select>
                        </div>

                        <!-- Birthdate -->
                        <div class="field">
                            <label for="birthdate">Birthdate <span style="color:red">*</span></label>
                            <input type="date" id="birthdate" name="birthdate" value="<?php echo htmlspecialchars($patient_data['birthdate'] ?? ''); ?>" required max="<?php echo date('Y-m-d'); ?>">
                        </div>

                        <!-- Contact Number -->
                        <div class="field">
                            <label for="phone">Contact Number <span style="color:red">*</span></label>
                            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($patient_data['phone'] ?? ''); ?>" required maxlength="11" pattern="[0-9]{11}" placeholder="e.g. 09123456789">
                        </div>

                        <!-- Email (auto-filled from account) -->
                        <div class="field">
                            <label for="email">Email <span style="color:red">*</span></label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($display_email); ?>" required>
                        </div>

                        <!-- Address -->
                        <div class="field full">
                            <label for="address">Address <span style="color:red">*</span></label>
                            <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($patient_data['address'] ?? ''); ?>" required>
                        </div>

                        <!-- Emergency Contact -->
                        <div class="field full">
                            <label for="emergency_contact">Emergency Contact Number</label>
                            <input type="text" id="emergency_contact" name="emergency_contact" value="<?php echo htmlspecialchars($patient_data['emergency_contact'] ?? ''); ?>" maxlength="11" pattern="[0-9]{11}" placeholder="e.g. 09123456789">
                        </div>

                        <!-- Allergies -->
                        <div class="field full">
                            <label for="allergies">Allergies</label>
                            <input type="text" id="allergies" name="allergies" value="<?php echo htmlspecialchars($patient_data['allergies'] ?? ''); ?>" placeholder="e.g. Penicillin, Peanuts (Leave blank if none)">
                        </div>

                        <!-- Medical History -->
                        <div class="field full">
                            <label for="medical_history">Medical History</label>
                            <textarea id="medical_history" name="medical_history" placeholder="Previous conditions, surgeries, or ongoing treatments..."><?php echo htmlspecialchars($patient_data['medical_history'] ?? ''); ?></textarea>
                        </div>
                    </div>

                    <br>
                    <div class="button-group">
                        <button type="submit" name="savePatientProfile" class="btn-save">Save Changes</button>
                        <?php if ($patient_data): ?>
                        <a href="patient_profile.php" class="btn-cancel">Cancel</a>
                        <?php else: ?>
                        <a href="patient_dashboard.php" class="btn-cancel">Cancel</a>
                        <?php endif; ?>
                    </div>
                </div>

            </form>

            <?php else: ?>

            <!-- SAVED VIEW -->
            <div class="profile-card">
                <h2>Your Information</h2>
                <p class="subtitle">Manage your clinic record details</p>

                <div class="patient-info-grid">
                    <div class="info-card">
                        <label>Full Name</label>
                        <span><?php echo htmlspecialchars($patient_data['first_name'].' '.$patient_data['last_name']); ?></span>
                    </div>
                    <div class="info-card">
                        <label>Gender</label>
                        <span><?php echo htmlspecialchars($patient_data['gender']); ?></span>
                    </div>
                    <div class="info-card">
                        <label>Birthdate</label>
                        <span><?php echo date('F j, Y', strtotime($patient_data['birthdate'])); ?></span>
                    </div>
                    <div class="info-card">
                        <label>Contact Number</label>
                        <span><?php echo htmlspecialchars($patient_data['phone']); ?></span>
                    </div>
                    <div class="info-card">
                        <label>Email</label>
                        <span><?php echo htmlspecialchars($patient_data['email']); ?></span>
                    </div>
                    <div class="info-card">
                        <label>Emergency Contact</label>
                        <span><?php echo $patient_data['emergency_contact'] ? htmlspecialchars($patient_data['emergency_contact']) : '—'; ?></span>
                    </div>
                    <div class="info-card-full">
                        <label>Address</label>
                        <span><?php echo htmlspecialchars($patient_data['address']); ?></span>
                    </div>
                    <div class="info-card-full">
                        <label>Allergies</label>
                        <span><?php echo $patient_data['allergies'] ? htmlspecialchars($patient_data['allergies']) : '—'; ?></span>
                    </div>
                    <div class="info-card-full">
                        <label>Medical History</label>
                        <span><?php echo $patient_data['medical_history'] ? htmlspecialchars($patient_data['medical_history']) : '—'; ?></span>
                    </div>
                </div>

                <br>
                <div class="button-group">
                    <a href="patient_profile.php?edit=1" class="btn-save">Edit</a>
                    <a href="patient_dashboard.php" class="btn-cancel">Back to Home</a>
                </div>
            </div>

            <?php endif; ?>

        </div>

    </main>

<script>
function togglePatientMenu(btn){
    var menu = btn.closest('.profile-menu');
    var isOpen = menu.classList.contains('open');
    document.querySelectorAll('.profile-menu.open').forEach(function(m){ m.classList.remove('open'); });
    if(!isOpen){ menu.classList.add('open'); }
}
document.addEventListener('click', function(e){
    if(!e.target.closest('.profile-menu')){
        document.querySelectorAll('.profile-menu.open').forEach(function(m){ m.classList.remove('open'); });
    }
});
</script>
<script src="../js/input-restrictions.js"></script>
</body>
</html>
