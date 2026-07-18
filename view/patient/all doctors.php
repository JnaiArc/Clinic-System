   
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
            <a href="patient_request medical.php">Request Medical Documents</a>
            <a href="patient_services.php">Services</a>
            <a href="all doctors.php"class="active"> All Doctors</a>
        </nav>


        <!-- Profile -->
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

   <!-- DOCTOR SPECIALIST SECTION -->
    <section class="doctor-specialist">

        <h2>Browse through the doctors specialist.</h2>

        <div class="doctor-container">

            <!-- LEFT SIDE -->
            <div class="specialty-list">

                <button class="specialty-btn active" data-filter="all">
                    All
                </button>

                <button class="specialty-btn" data-filter="pediatrics">
                    Pediatrics
                </button>

                <button class="specialty-btn" data-filter="general">
                    Genaral Medicine
                </button>

                <button class="specialty-btn" data-filter="internal">
                    Internal Medicine 
                </button>

                <button class="specialty-btn" data-filter="Obgyne">
                    Obstetrics & Gynecology
                </button>

            </div>

            <!-- RIGHT SIDE -->
            <div class="doctor-grid">

                <!-- Pediatrician 1 -->
                <div class="doctor-card" data-specialty="pediatrics">

                    <img src="../../img/Taroy2.png" alt="Doctor">

                    <div class="doctor-info">

                        <h3>Dr. Taroy, Sarah Jane</h3>

                        <p>Pediatrician</p>

                    </div>

                </div>

                <!-- Pediatrician 2 -->
                <div class="doctor-card" data-specialty="pediatrics">

                    <img src="../../img/Tomadong.png" alt="Doctor">

                    <div class="doctor-info">

                        <h3>Dr. Tomadong, Johanna</h3>

                        <p>Pediatrician</p>

                    </div>

                </div>


                <!--General Medicine 1-->
                <div class="doctor-card" data-specialty="general">

                    <img src="../../img/Macas.png" alt="Doctor">

                    <div class="doctor-info">

                        <h3>Dr. Macas, Reymar</h3>

                        <p>General Medicine</p>

                    </div>

                </div>

                 <!--General Medicine 2-->
                <div class="doctor-card" data-specialty="general">

                    <img src="../../img/Bayani.png" alt="Doctor">

                    <div class="doctor-info">

                        <h3>Dr. Bayani, Khane</h3>

                        <p>General Medicine</p>

                    </div>

                </div>

                <!-- Internal Medicine 1 -->
                <div class="doctor-card" data-specialty="internal">

                    <img src="../../img/Arcenall.png" alt="Doctor">

                    <div class="doctor-info">

                        <h3>Dr. Arcenal, Jonalyn</h3>

                        <p>Internal Medicine</p>

                    </div>

                </div>

                 <!-- Internal Medicine 2-->
                <div class="doctor-card" data-specialty="internal">

                    <img src="../../img/Pagsolingan.png" alt="Doctor">

                    <div class="doctor-info">

                        <h3>Dr. Pagsolingan, Serj</h3>

                        <p>Internal Medicine</p>

                    </div>

                </div>

                <!-- Obgyne 1-->
                <div class="doctor-card" data-specialty="Obgyne">

                    <img src="../../img/Garduque.png" alt="Doctor">

                    <div class="doctor-info">

                        <h3>Dr. Garduque, Jody</h3>

                        <p>Obstetrics & Gynecology</p>

                    </div>

                </div>

                 <!-- Obgyne 2-->
                <div class="doctor-card" data-specialty="Obgyne">

                    <img src="../../img/Taroy2.png" alt="Doctor">

                    <div class="doctor-info">

                        <h3>Dr. Taroy, Sarah Jane</h3>

                        <p>Obstetrics & Gynecology</p>

                    </div>

                </div>

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