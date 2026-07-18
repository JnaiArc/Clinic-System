<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pediatrics | SwiftCare</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../css/patient.css">
    <link rel="stylesheet" href="../css/patient_dashboardd.css">
</head>

<body>

<!-- ================= TOP NAVBAR ================= -->

<header class="topbar">

    <div class="logo">
        <a href="patient_dashboard.php">
            <img src="../../img/logo.png" alt="SwiftCare">
        </a>
        <span>SwiftCare</span>
    </div>

     <nav class="top-nav">
            <a href="patient_dashboard.php">Home</a>
            <a href="patient_request consultation.php">Request Consultation</a>
            <a href="patient_view_appointment.php">Appointment</a>
            <a href="patient_request medical.php">Request Medical Documents</a>
            <a href="patient_services.php"class="active">Services</a>
            <a href="all doctors.php"> All Doctors</a>
        </nav>

    <div class="profile-menu">
            <button type="button" class="profile-menu-toggle" onclick="togglePatientMenu(this)">
                <img src="../../img/Bayani.png" class="profile-avatar" alt="Profile">
                <span class="dropdown-arrow">&#9662;</span>
            </button>
            <div class="profile-dropdown">
                <a href="my_profile.php" class="profile-dropdown-item">My Profile</a>
                <a href="http://localhost/clinic1/controller/logoutController.php" class="profile-dropdown-item signout" onclick="return confirm('Logout?')">Sign Out</a>
            </div>
        </div>

</header>

<!-- ================= PAGE HEADER ================= -->
<section class="service-container">

    <div class="service-title">
        <h1>GENERAL MEDICINE</h1>
    </div>

    <div class="service-description">
        <p>
            General Medicine provides comprehensive healthcare for adults, focusing on the prevention, 
            diagnosis, and treatment of common illnesses and chronic medical conditions to 
            promote overall health and well-being.
        </p>

    </div>

    <div class="service-content">

        <!-- SERVICES -->
        <div class="service-box">

            <h2>Services</h2>

            <ul>
                <li>General Medical Consultation</li>
                <li>Physical Examination</li>
                <li>Treatment for Fever, Cough & Colds</li>
                <li>Hypertension & Diabetes Management</li>
                <li>Preventive Health Check-up</li>
            </ul>
        </div>

        <!-- WHEN TO VISIT -->
        <div class="service-box">

            <h2>When to Visit</h2>

            <p class="visit-text">
                Visit a General Medicine physician if you experience:
            </p>

            <ul>
                <li>Fever or persistent cough</li>
                <li>High blood pressure</li>
                <li>Headache or dizziness</li>
                <li>Stomach pain or digestive problems</li>
                <li>Need for a routine health check-up</li>
            </ul>
        </div>
    </div>

</section>
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