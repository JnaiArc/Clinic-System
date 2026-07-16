<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
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

// SEARCH
$search = isset($_POST['search']) ? $_POST['search'] : '';
if (!empty($search)) {
    $patients_result = $patient->searchPatients($search);
} else {
    $patients_result = $patient->getAllPatients();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Records</title>
    <link rel="stylesheet" href="../css/admin_dashboard.css">
    <link rel="stylesheet" href="../css/admin.css">
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
            <a href="http://localhost/clinic1/view/admin/admin_dashboard.php" class="menu-item">Dashboard</a>
            <a href="http://localhost/clinic1/view/admin/admin_patientRecord.php" class="menu-item active">Patient Records</a>
            <a href="http://localhost/clinic1/view/admin/admin_appointments.php" class="menu-item">Appointments</a>
            <a href="http://localhost/clinic1/view/admin/admin_followup.php" class="menu-item">Follow-Up Checkup</a>
            <a href="http://localhost/clinic1/view/admin/admin_doctors.php" class="menu-item">Doctors</a>
            <a href="http://localhost/clinic1/view/admin/admin_staff.php" class="menu-item">Staff</a>
        </nav>

    </aside>
    <main class="main-content">
        <header class="topbar">
            <div class="topbar-left">
                <div class="clinic-text">
                    <h1>Patient Records</h1>
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

        <!-- SEARCH AND ADD -->
        <div class="search-add-bar">
            <form method="POST" action="http://localhost/clinic1/view/admin/admin_patientRecord.php" class="search-form">
                <input type="text" name="search" placeholder="Search Patient Name" value="<?php echo $search; ?>">
                <button type="submit" name="searchBtn">Search</button>
            </form>
            <a href="http://localhost/clinic1/view/admin/add_patient.php" class="add-new-btn">+ Add New Patient</a>
            <a href="http://localhost/clinic1/xml/export_patients.php" class="add-new-btn">⬇ Export XML</a>
            <a href="http://localhost/clinic1/xml/import_patients.php" class="add-new-btn">⬆ Import XML</a>
        </div>

        <!-- PATIENT LIST -->   
        <section class="table-section">

            <div class="section-header">
                Patient List
            </div>

            <table class="appointment-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Gender</th>
                        <th>Age</th>
                        <th>Contact</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($patients_result->rowCount() > 0): ?>
                        <?php while ($row = $patients_result->fetch(PDO::FETCH_ASSOC)): 
                            $birthdate = new DateTime($row['birthdate']);
                            $today = new DateTime('today');
                            $age = $birthdate->diff($today)->y;
                        ?>
                        <tr>
                            <td><?php echo $row['first_name'] . ' ' . $row['last_name']; ?></td>
                            <td><?php echo $row['gender']; ?></td>
                            <td><?php echo $age; ?></td>
                            <td><?php echo $row['phone']; ?></td>
                            <td>
                                <a href="http://localhost/clinic1/view/admin/view_patient.php?id=<?php echo $row['id']; ?>" class="action-btn view-btn">View</a>
                                <a href="http://localhost/clinic1/view/admin/book_consultation.php?patient_id=<?php echo $row['id']; ?>" class="action-btn book-btn">Book</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: #64748b;">No patients found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

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