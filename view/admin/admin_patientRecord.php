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
    <link rel="stylesheet" href="../css/admin_patients.css">
</head>

<body>

<div class="dashboard-container">
    <aside class="sidebar">

        <div class="sidebar-top">
            <?php if(!empty($user_info['profile_photo'])): ?>
            <img src="../../uploads/<?php echo $user_info['profile_photo']; ?>" class="profile-circle" style="object-fit: cover;">
            <?php else: ?>
            <div class="profile-circle"></div>
            <?php endif; ?>
            <div class="profile-name">
                <h2><?php echo $_SESSION['name']; ?></h2>
            </div>
        </div>

        <nav class="sidebar-menu">
            <a href="http://localhost/clinic1/view/admin/admin_dashboard.php" class="menu-item">Dashboard</a>
            <a href="http://localhost/clinic1/view/admin/admin_patientRecord.php" class="menu-item active">Patient Records</a>
            <a href="http://localhost/clinic1/view/admin/admin_appointments.php" class="menu-item">Appointments</a>
            <a href="http://localhost/clinic1/view/admin/admin_followup.php" class="menu-item">Follow-Up Checkup</a>
            <a href="http://localhost/clinic1/view/admin/admin_doctors.php" class="menu-item">Doctors</a>
            <a href="http://localhost/clinic1/view/admin/admin_staff.php" class="menu-item">Staff</a>
            <a href="http://localhost/clinic1/controller/logoutController.php" class="menu-item logout-btn" onclick="return confirm('Logout?')">Logout</a>
        </nav>

    </aside>
    <main class="main-content">
        <header class="topbar">
            <div class="topbar-left">
                <img src="http://localhost/clinic1/logo.jpg" class="clinic-logo">
                <div class="clinic-text">
                    <h1>SwiftCare Clinic</h1>
                    <p>Patient Records</p>
                </div>
            </div>
            <div class="admin-box">Admin</div>
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

</body>
</html>