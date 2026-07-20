<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: http://localhost/clinic1/view/login/login.php");
    exit();
}

require_once 'C:\xampp\htdocs\clinic1\config\Database.php';
require_once 'C:\xampp\htdocs\clinic1\model\User.php';
require_once 'C:\xampp\htdocs\clinic1\model\Patient.php';

$database = new Database();
$conn = $database->connect();
$user_model = new User($conn);
$patient_model = new Patient($conn);

$doctor_info = $user_model->getUserById($_SESSION['user_id']);
$doc_id = $_SESSION['user_id'];

$id = $_GET['id'] ?? 0;

// A doctor may only view a patient they've actually consulted with — this also
// makes sure the history shown further down never leaks another doctor's notes.
$has_consulted = $patient_model->doctorHasConsultedPatient($doc_id, $id);
$patient_data = $has_consulted ? $patient_model->getPatientById($id) : null;

// Only this doctor's own completed consultations with this patient — even if the
// same patient has also been seen by other doctors, those records stay hidden here.
$history = $has_consulted ? $patient_model->getPatientConsultationHistoryByDoctor($id, $doc_id) : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Patient</title>
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
            <a href="http://localhost/clinic1/view/doctor/doctor_followup.php" class="menu-item">Follow-Up Checkup</a>
            <a href="http://localhost/clinic1/view/doctor/doctor_patients.php" class="menu-item active">My Patients</a>
        </nav>
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

        <section class="table-section">

            <div class="section-header">
                Patient Information
            </div>

            <?php if ($patient_data): ?>

            <div class="patient-info-grid">
                <div class="info-card">
                    <label>Full Name</label>
                    <span><?php echo htmlspecialchars($patient_data['first_name'].' '.$patient_data['last_name']); ?></span>
                </div>
                <div class="info-card">
                    <label>Gender</label>
                    <span><?php echo htmlspecialchars($patient_data['gender'] ?: '—'); ?></span>
                </div>
                <div class="info-card">
                    <label>Age</label>
                    <span><?php echo !empty($patient_data['birthdate']) ? (new DateTime($patient_data['birthdate']))->diff(new DateTime())->y . ' years old' : '—'; ?></span>
                </div>
                <div class="info-card">
                    <label>Birthdate</label>
                    <span><?php echo !empty($patient_data['birthdate']) ? date('F j, Y', strtotime($patient_data['birthdate'])) : '—'; ?></span>
                </div>
                <div class="info-card">
                    <label>Contact Number</label>
                    <span><?php echo htmlspecialchars($patient_data['phone'] ?: '—'); ?></span>
                </div>
                <div class="info-card">
                    <label>Email</label>
                    <span><?php echo htmlspecialchars($patient_data['email'] ?: '—'); ?></span>
                </div>
                <div class="info-card-full">
                    <label>Address</label>
                    <span><?php echo htmlspecialchars($patient_data['address'] ?: '—'); ?></span>
                </div>
                <div class="info-card">
                    <label>Emergency Contact</label>
                    <span><?php echo htmlspecialchars($patient_data['emergency_contact'] ?: '—'); ?></span>
                </div>
                <div class="info-card">
                    <label>Allergies</label>
                    <span><?php echo htmlspecialchars($patient_data['allergies'] ?: '—'); ?></span>
                </div>
                <div class="info-card-full">
                    <label>Medical History</label>
                    <span><?php echo htmlspecialchars($patient_data['medical_history'] ?: '—'); ?></span>
                </div>
            </div>

            <div class="view-buttons">
                <a href="http://localhost/clinic1/view/doctor/doctor_patients.php" class="back-link">Back to My Patients</a>
            </div>

            <?php else: ?>
            <p style="padding: 20px; color:#64748b;">Patient not found, or you haven't consulted with this patient.</p>
            <?php endif; ?>

        </section>

        <!-- consultation history, this doctor's own records only -->
        <?php if ($patient_data): ?>
        <div class="history-section">
            <div class="history-header">Consultation History With You (Completed)</div>
            <?php if ($history && $history->rowCount() > 0): ?>
                <?php while ($h = $history->fetch(PDO::FETCH_ASSOC)):
                    $meds = $patient_model->getConsultationMedicines($h['id']);
                    $recs = $patient_model->getConsultationRecommendations($h['id']);
                ?>
                <div class="history-item">
                    <div class="history-item-header">
                        <span class="history-date"><?php echo date('F j, Y', strtotime($h['appointment_date'])); ?> &mdash; <?php echo htmlspecialchars($h['appointment_time']); ?></span>
                        <span class="history-doctor">Dr. <?php echo htmlspecialchars($h['doctor_name']); ?></span>
                    </div>
                    <div class="history-body">
                        <div class="history-col">
                            <label>Findings</label>
                            <p><?php echo $h['findings'] ? htmlspecialchars($h['findings']) : '—'; ?></p>
                        </div>
                        <div class="history-col">
                            <label>Medicines</label>
                            <?php $med_rows = $meds->fetchAll(PDO::FETCH_ASSOC); ?>
                            <?php if($med_rows): ?>
                            <ul>
                                <?php foreach($med_rows as $m): ?>
                                <li><?php echo htmlspecialchars($m['medicine_name'].' ('.$m['dosage'].') '.$m['frequency'].' x '.$m['duration']); ?></li>
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
                                <li><?php echo htmlspecialchars($r['recommendation']); ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <?php else: ?><p>—</p><?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-history">No completed consultations with you on record.</div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

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
