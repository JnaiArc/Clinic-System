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
            <a href="patient_dashboard.php"class="active">Home</a>
            <a href="patient_profile.php">Patient Profile</a>
            <a href="patient_request consultation.php">Request Consultation</a>
            <a href="patient_view request status.php">View Request Status</a>
            <a href="patient_view_appointment.php">Appointment</a>
            <a href="patient_request medical.php">Request Medical Documents</a>
            <a href="patient_services.php">Services</a>
            <a href="all doctors.php"> All Doctors</a>
        </nav>

        <!-- Profile -->
        <div class="profile">
            <img src="../../img/Taroy.jpg" alt="Patient Profile">
        </div>

    </header>

    <main class="main-content">

        <!-- <header class="topbar">
            <h1>Home</h1>
        </header> -->

        <!-- WELCOME BANNER -->
        <section class="welcome-banner">
            <div class="welcome-text">
                <h2>Welcome back, Charmane!</h2>
                <p>Here's what's happening with your care today.</p>
            </div>
            <a href="patient_request consultation.php" class="btn-book">Book a Consultation</a>
        </section>

        <!-- DASHBOARD CONTENT -->
        <section class="dashboard-grid">

            <!-- YOUR NEXT APPOINTMENT -->
            <div class="appointment-card">

                <div class="card-title">
                    <h2>Your Next Appointment</h2>
                </div>

                <div class="appointment-details">

                    <div class="detail-row">
                        <span>Appointment Status</span>
                        <strong>Pending</strong>
                    </div>

                    <div class="detail-row">
                        <span>Consultation Date</span>
                        <strong>July 22, 2026</strong>
                    </div>

                    <div class="detail-row">
                        <span>Main Complaint</span>
                        <strong>Fever</strong>
                    </div>

                    <div class="detail-row">
                        <span>Clinic</span>
                        <strong>Online Clinic</strong>
                    </div>

                    <div class="detail-row">
                        <span>Consultation Time Slot</span>
                        <strong>1:00 PM - 1:30 PM</strong>
                    </div>

                    <div class="detail-row">
                        <span>Remarks</span>
                        <strong>Waiting for doctor's approval</strong>
                    </div>

                </div>

            </div>

            <!-- AVAILABLE SLOTS -->
            <div class="slot-card">

                <h2>Available</h2>
                <h2>Slots Today</h2>

                <div class="slot-number">
                    8
                </div>

                <p>Available Slots</p>

            </div>
        </section>
    </main>
</div>

</body>
</html>