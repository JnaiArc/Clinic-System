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

$id = $_GET['id'] ?? 0;
$patient_data = $patient->getPatientById($id);
$edit_mode = isset($_GET['edit']) && $_GET['edit'] == 1;

$history = $patient->getPatientConsultationHistory($id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Patient</title>
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
                    <h1>Patient Information</h1>
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

            <div class="section-header">
                <?php echo $edit_mode ? 'Edit Patient' : 'Patient Information'; ?>
            </div>

            <?php if ($patient_data): ?>

            <!-- edit -->
            <?php if ($edit_mode): ?>
            <form method="POST" action="http://localhost/clinic1/controller/PatientController.php" class="patient-form">
                <input type="hidden" name="id" value="<?php echo $id; ?>">

                <div class="form-group">
                    <label>First Name <span style="color:red">*</span></label>
                    <input type="text" name="first_name" value="<?php echo $patient_data['first_name']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Last Name <span style="color:red">*</span></label>
                    <input type="text" name="last_name" value="<?php echo $patient_data['last_name']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Gender <span style="color:red">*</span></label>
                    <select name="gender" required>
                        <option value="Male" <?php echo $patient_data['gender']=='Male'?'selected':''; ?>>Male</option>
                        <option value="Female" <?php echo $patient_data['gender']=='Female'?'selected':''; ?>>Female</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Birthdate <span style="color:red">*</span></label>
                    <input type="date" name="birthdate" value="<?php echo $patient_data['birthdate']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Contact Number <span style="color:red">*</span></label>
                    <input type="text" name="phone" value="<?php echo $patient_data['phone']; ?>" required maxlength="11" pattern="[0-9]{11}">
                </div>
                <div class="form-group">
                    <label>Email <span style="color:red">*</span></label>
                    <input type="email" name="email" value="<?php echo $patient_data['email']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Address <span style="color:red">*</span></label>
                    <input type="text" name="address" value="<?php echo $patient_data['address']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Emergency Contact</label>
                    <input type="text" name="emergency_contact" value="<?php echo $patient_data['emergency_contact']; ?>" maxlength="11" pattern="[0-9]{11}">
                </div>
                <div class="form-group">
                    <label>Allergies</label>
                    <input type="text" name="allergies" value="<?php echo $patient_data['allergies']; ?>">
                </div>
                <div class="form-group" style="grid-column: span 2;">
                    <label>Medical History</label>
                    <textarea name="medical_history"><?php echo $patient_data['medical_history']; ?></textarea>
                </div>

                <div class="button-group">
                    <button type="submit" name="updatePatient" class="save-btn">Update</button>
                    <a href="http://localhost/clinic1/view/admin/view_patient.php?id=<?php echo $id; ?>" class="clear-btn">Cancel</a>
                </div>
            </form>

            <!-- view -->
            <?php else: ?>
            <div class="patient-info-grid">
                <div class="info-card">
                    <label>Full Name</label>
                    <span><?php echo $patient_data['first_name'].' '.$patient_data['last_name']; ?></span>
                </div>
                <div class="info-card">
                    <label>Gender</label>
                    <span><?php echo $patient_data['gender']; ?></span>
                </div>
                <div class="info-card">
                    <label>Age</label>
                    <span><?php echo date('Y') - date('Y', strtotime($patient_data['birthdate'])); ?> years old</span>
                </div>
                <div class="info-card">
                    <label>Birthdate</label>
                    <span><?php echo date('F j, Y', strtotime($patient_data['birthdate'])); ?></span>
                </div>
                <div class="info-card">
                    <label>Contact Number</label>
                    <span><?php echo $patient_data['phone']; ?></span>
                </div>
                <div class="info-card">
                    <label>Email</label>
                    <span><?php echo $patient_data['email']; ?></span>
                </div>
                <div class="info-card-full">
                    <label>Address</label>
                    <span><?php echo $patient_data['address']; ?></span>
                </div>
                <div class="info-card">
                    <label>Emergency Contact</label>
                    <span><?php echo $patient_data['emergency_contact'] ?: '—'; ?></span>
                </div>
                <div class="info-card">
                    <label>Allergies</label>
                    <span><?php echo $patient_data['allergies'] ?: '—'; ?></span>
                </div>
                <div class="info-card-full">
                    <label>Medical History</label>
                    <span><?php echo $patient_data['medical_history'] ?: '—'; ?></span>
                </div>
            </div>

            <div class="view-buttons">
                <a href="http://localhost/clinic1/view/admin/view_patient.php?id=<?php echo $id; ?>&edit=1" class="save-btn">Edit</a>
                <form method="POST" action="http://localhost/clinic1/controller/PatientController.php" style="display:inline;">
                    <input type="hidden" name="id" value="<?php echo $id; ?>">
                    <button type="submit" name="deletePatient" class="delete-btn" onclick="return confirm('Delete?')">Delete</button>
                </form>
            </div>
            <?php endif; ?>

            <?php else: ?>
            <p>Patient not found.</p>
            <?php endif; ?>

        </section>

        <!-- cons hisory table -->
        <?php if(!$edit_mode): ?>
        <div class="history-section">
            <div class="history-header">Consultation History (Completed)</div>
            <?php if ($history && $history->rowCount() > 0): ?>
                <?php while ($h = $history->fetch(PDO::FETCH_ASSOC)):
                    $meds = $patient->getConsultationMedicines($h['id']);
                    $recs = $patient->getConsultationRecommendations($h['id']);
                ?>
                <div class="history-item">
                    <div class="history-item-header">
                        <span class="history-date"><?php echo date('F j, Y', strtotime($h['appointment_date'])); ?> &mdash; <?php echo $h['appointment_time']; ?></span>
                        <span class="history-doctor">Dr. <?php echo $h['doctor_name']; ?></span>
                    </div>
                    <div class="history-body">
                        <div class="history-col">
                            <label>Findings</label>
                            <p><?php echo $h['findings'] ?: '—'; ?></p>
                        </div>
                        <div class="history-col">
                            <label>Medicines</label>
                            <?php $med_rows = $meds->fetchAll(PDO::FETCH_ASSOC); ?>
                            <?php if($med_rows): ?>
                            <ul>
                                <?php foreach($med_rows as $m): ?>
                                <li><?php echo $m['medicine_name'].' ('.$m['dosage'].') '.$m['frequency'].' &times; '.$m['duration']; ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <?php else: ?><p>—</p><?php endif; ?>
                        </div>
                        <div class="history-col">
                            <label>Recommendations</label>
                            <?php $rec_rows = $recs->fetchAll(PDO::FETCH_ASSOC); ?>
                            <?php if($rec_rows): ?>
                            <ul>
                                <?php foreach($rec_rows as $r): ?>
                                <li><?php echo $r['recommendation']; ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <?php else: ?><p>—</p><?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-history">No completed consultations on record.</div>
            <?php endif; ?>
        </div>
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
