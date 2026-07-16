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
            <a href="patient_profile.php">Patient Profile</a>
            <a href="patient_request consultation.php">Request Consultation</a>
            <a href="patient_view request status.php">View Request Status</a>
            <a href="patient_view_appointment.php">Appointment</a>
            <a href="patient_request medical.php">Request Medical Documents</a>
            <a href="patient_services.php"class="active">Services</a>
            <a href="all doctors.php"> All Doctors</a>
        </nav>

        <!-- Profile -->
        <div class="profile">
            <img src="../../img/Taroy.jpg" alt="Patient Profile">
        </div>

    </header>

    <main class="main-content">

        <header class="page-title">
            <h1>Our Services</h1>
        </header>

        <div class="services-container">

            <!--Pediatrics-->

            <a href="services_pediatrics.php" class="service-card">

                <img src="../../img/Pediatrics.png" alt="Pediatrics">

                <div class="service-title">
                    PEDIATRICS
                </div>

                <div class="overlay">

                    <h3>Pediatrics</h3>

                    <p>
                        Medical care for infants, children and adolescents.
                    </p>

                    <span class="learn-more">
                        Click for more →
                    </span>

                </div>

            </a>

            <!--General Medicine-->

            <a href="services_general med.php" class="service-card">

                <img src="../../img/General Med.png" alt="General Medicine">

                <div class="service-title">
                    GENERAL MEDICINE
                </div>

                <div class="overlay">

                    <h3>General Medicine</h3>

                    <p>
                        Diagnosis and treatment of common illnesses and routine health checkups.
                    </p>

                    <span class="learn-more">
                       Click for more →
                    </span>

                </div>

            </a>

            <!--Internal Medicine-->

            <a href="services_internal med.php" class="service-card">

                <img src="../../img/Internal Meds.png" alt="Internal Medicine">

                <div class="service-title">
                    INTERNAL MEDICINE
                </div>

                <div class="overlay">

                    <h3>Internal Medicine</h3>

                    <p>
                        Prevention and treatment of diseases affecting adults.
                    </p>

                    <span class="learn-more">
                        Click for more →
                    </span>

                </div>

            </a>

            <!--OBSTETRICS & GYNECOLOGY-->

            <a href="services_obgyne.php" class="service-card">

                <img src="../../img/Obs and Gyne.png" alt="OBGYNE">

                <div class="service-title">
                    OBSTETRICS & GYNECOLOGY
                </div>

                <div class="overlay">

                    <h3>Obstetrics & Gynecology</h3>

                    <p>
                        Women's reproductive health, pregnancy and childbirth services.
                    </p>

                    <span class="learn-more">
                        Click for more →
                    </span>

                </div>

            </a>

        </div>

    </main>

</div>

</body>
</html>