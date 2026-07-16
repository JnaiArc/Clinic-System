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
            <a href="patient_request medical.php"class="active">Request Medical Documents</a>
            <a href="patient_services.php">Services</a>
            <a href="all doctors.php"> All Doctors</a>
        </nav>

        <!-- Profile -->
        <div class="profile">
            <img src="../../img/Taroy.jpg" alt="Patient Profile">
        </div>

    </header>


    <main class="main-content">

        <h1 class="page-title">Request Medical Documents</h1>
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

    </main>

</div>

</body>
</html>