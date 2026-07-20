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

// Only patients this doctor has actually consulted with — not every booked patient.
$my_patients_result = $patient_model->getPatientsByDoctor($doc_id);
$my_patients = $my_patients_result->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Patients</title>
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
                    <h1>My Patients</h1>
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
                Patients You've Consulted
            </div>

            <table class="appointment-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Gender</th>
                        <th>Age</th>
                        <th>Contact</th>
                        <th>Allergies</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($my_patients) > 0): ?>
                        <?php foreach ($my_patients as $row):
                            $age = '—';
                            if (!empty($row['birthdate'])) {
                                $birthdate = new DateTime($row['birthdate']);
                                $today = new DateTime('today');
                                $age = $birthdate->diff($today)->y;
                            }
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['first_name'].' '.$row['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['gender'] ?: '—'); ?></td>
                            <td><?php echo $age; ?></td>
                            <td><?php echo htmlspecialchars($row['phone'] ?: '—'); ?></td>
                            <td><?php echo htmlspecialchars($row['allergies'] ?: '—'); ?></td>
                            <td>
                                <a href="http://localhost/clinic1/view/doctor/doctor_view_patient.php?id=<?php echo $row['id']; ?>" class="action-btn view-btn">View</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; color: #64748b;">You haven't consulted with any patients yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

        </section>

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
