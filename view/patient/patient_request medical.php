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
            <a href="patient_request medical.php"class="active">Request Medical Documents</a>
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
            <h1>Request Medical Documents</h1>
        </header>

        <div class="profile-wrap">

            <p class="page-description">
                Select the medical document you would like to request.
            </p>

            <div class="document-card">

                <h2>Available Documents</h2>

                <form action="process_document_request.php" method="POST">

                    <label class="document-option">
                        <input type="checkbox" name="documents[]" value="Medical Certificate">
                        Medical Certificate
                    </label>


                    <label class="document-option">
                        <input type="checkbox" name="documents[]" value="Medical Records">
                        Medical Records
                    </label>


                    <label class="document-option">
                        <input type="checkbox" name="documents[]" value="Laboratory Results">
                        Laboratory Results
                    </label>


                    <label class="document-option">
                        <input type="checkbox" name="documents[]" value="Prescription History">
                        Prescription History
                    </label>


                    <button class="request-btn" type="submit">
                        Submit Request
                    </button>

                </form>

            </div>

        </div>

    </main>

</div>

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