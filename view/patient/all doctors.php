<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: http://localhost/clinic1/view/login/login.php");
    exit();
}

require_once 'C:\xampp\htdocs\clinic1\config\Database.php';
require_once 'C:\xampp\htdocs\clinic1\model\User.php';
require_once 'C:\xampp\htdocs\clinic1\model\Doctor.php';

$database = new Database();
$conn = $database->connect();
$userModel = new User($conn);
$doctorModel = new Doctor($conn);

// Doctors are now pulled from `users` joined with `doctors`, so a newly-added doctor
// (via Admin > Staff > Add Staff) automatically shows up here with their photo,
// name, and specialization — no manual edits to this page needed.
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard | SwiftCare</title>

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
            <a href="all doctors.php"class="active"> All Doctors</a>
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

   <!-- DOCTOR SPECIALIST SECTION -->
    <section class="doctor-specialist">

        <h2>Browse through the doctors specialist.</h2>

        <div class="doctor-container">

            <!-- LEFT SIDE -->
            <div class="specialty-list">

                <button class="specialty-btn active" data-filter="all">
                    All
                </button>

                <?php foreach ($specializations as $spec): ?>
                <button class="specialty-btn" data-filter="<?php echo specialtySlug($spec); ?>">
                    <?php echo htmlspecialchars($spec); ?>
                </button>
                <?php endforeach; ?>

            </div>

            <!-- RIGHT SIDE -->
            <div class="doctor-grid">

                <?php if (empty($doctors)): ?>
                <p style="color:#708098;">No doctors are registered yet.</p>
                <?php endif; ?>

                <?php foreach ($doctors as $doc): ?>
                <?php
                    $photo = !empty($doc['profile_photo']) ? '../../uploads/'.$doc['profile_photo'] : '../../img/user.png';
                    $specLabel = !empty($doc['specialization']) ? $doc['specialization'] : 'General';
                ?>
                <div class="doctor-card" data-specialty="<?php echo specialtySlug($specLabel); ?>">

                    <img src="<?php echo htmlspecialchars($photo); ?>" alt="Dr. <?php echo htmlspecialchars($doc['first_name'].' '.$doc['last_name']); ?>" style="object-fit:cover;">

                    <div class="doctor-info">

                        <h3>Dr. <?php echo htmlspecialchars($doc['last_name'].', '.$doc['first_name']); ?></h3>

                        <p><?php echo htmlspecialchars($specLabel); ?></p>

                    </div>

                </div>
                <?php endforeach; ?>

            </div>

        </div>

    </section>

    <script>

        const buttons = document.querySelectorAll(".specialty-btn");
        const cards = document.querySelectorAll(".doctor-card");

        buttons.forEach(button => {

            button.addEventListener("click", () => {

                buttons.forEach(btn => btn.classList.remove("active"));
                button.classList.add("active");

                const filter = button.dataset.filter;

                cards.forEach(card => {

                    if (filter === "all") {

                        card.style.display = "block";

                    } else if (card.dataset.specialty === filter) {

                        card.style.display = "block";

                    } else {

                        card.style.display = "none";

                    }

                });

            });

        });

    </script>

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
</body>
</html>