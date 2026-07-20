<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: http://localhost/clinic1/view/login/login.php");
    exit();
}

require_once 'C:\xampp\htdocs\clinic1\config\Database.php';
require_once 'C:\xampp\htdocs\clinic1\model\User.php';
require_once 'C:\xampp\htdocs\clinic1\model\Doctor.php';

$database = new Database();
$conn = $database->connect();
$user = new User($conn);
$doctorModel = new Doctor($conn);

$user_info = $user->getUserById($_SESSION['user_id']);
$doctors_result = $doctorModel->getAllDoctors();
$doctors = $doctors_result->fetchAll(PDO::FETCH_ASSOC);
$specializations = $doctorModel->getAllSpecializations();

// Turn a specialization label into a safe data-filter/class token, e.g. "Obstetrics & Gynecology" -> "obstetrics-gynecology"
function specialtySlug($label) {
    $slug = strtolower(trim($label));
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    return trim($slug, '-');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Doctors</title>
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
            <a href="http://localhost/clinic1/view/admin/admin_patientRecord.php" class="menu-item">Patient Records</a>
            <a href="http://localhost/clinic1/view/admin/admin_appointments.php" class="menu-item">Appointments</a>
            <a href="http://localhost/clinic1/view/admin/admin_doctors.php" class="menu-item active">Doctors</a>
            <a href="http://localhost/clinic1/view/admin/admin_staff.php" class="menu-item">Staff</a>
        </nav>
    </aside>

    <main class="main-content">

        <header class="topbar">
            <div class="topbar-left">
                <div class="clinic-text">
                    <h1>Doctors</h1>
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
                Doctor List
            </div>

            <div class="doctor-specialist">

                <div class="doctor-container">

                    <!-- RIGHT SIDE: doctor cards, keeping the original table's info -->
                    <div class="doctor-grid">

                        <?php if (empty($doctors)): ?>
                        <p style="color:#708098;">No doctors are registered yet.</p>
                        <?php endif; ?>

                        <?php foreach ($doctors as $row): ?>
                        <?php
                            $photo = !empty($row['profile_photo']) ? '../../uploads/'.$row['profile_photo'] : '../../img/user.png';
                            $specLabel = !empty($row['specialization']) ? $row['specialization'] : 'General';
                        ?>
                        <div class="doctor-card" data-specialty="<?php echo specialtySlug($specLabel); ?>">

                            <img src="<?php echo htmlspecialchars($photo); ?>" alt="Dr. <?php echo htmlspecialchars($row['first_name'].' '.$row['last_name']); ?>" style="object-fit:cover;">

                            <div class="doctor-info">

                                <h3>Dr. <?php echo htmlspecialchars($row['first_name'].' '.$row['last_name']); ?></h3>

                                <p class="doc-spec"><?php echo htmlspecialchars($specLabel); ?></p>

                                <div class="doc-meta">
                                    <span><strong>License #:</strong> <?php echo htmlspecialchars($row['license_number']); ?></span>
                                    <span><strong>Schedule Days:</strong> <?php echo htmlspecialchars($row['schedule_days']); ?></span>
                                    <span><strong>Schedule Time:</strong> <?php echo htmlspecialchars($row['schedule_time_start'].' - '.$row['schedule_time_end']); ?></span>
                                </div>

                            </div>

                        </div>
                        <?php endforeach; ?>

                    </div>

                </div>

            </div>

        </section>

    </main>

</div>

<script>
const doctorSpecialtyButtons = document.querySelectorAll(".doctor-specialist .specialty-btn");
const doctorSpecialtyCards = document.querySelectorAll(".doctor-specialist .doctor-card");

doctorSpecialtyButtons.forEach(button => {
    button.addEventListener("click", () => {
        doctorSpecialtyButtons.forEach(btn => btn.classList.remove("active"));
        button.classList.add("active");

        const filter = button.dataset.filter;

        doctorSpecialtyCards.forEach(card => {
            if (filter === "all" || card.dataset.specialty === filter) {
                card.style.display = "block";
            } else {
                card.style.display = "none";
            }
        });
    });
});
</script>

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