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
            <a href="patient_request consultation.php"class="active">Request Consultation</a>
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

<div class="form-container">

    <!-- Page Title -->
    <h1 class="page-title">Schedule Appointment</h1>

    <!-- Form Card -->
    <div class="form-card">

        <h2 class="card-title">Schedule Appointment</h2>

        <form class="appointment-form">

            <!-- Patient Name & Phone -->
            <div class="form-row">

                <div class="form-group">
                    <label for="patient-name">Patient Name</label>
                    <input type="text" id="patient-name" value="Charmane Searchwell">
                </div>

                <div class="form-group">
                    <label for="phone-number">Phone Number</label>
                    <input type="tel" id="phone-number" value="09870542944">
                </div>

            </div>

            <!-- Date & Doctor -->
            <div class="form-row">

                <div class="form-group">
                    <label for="date">Date</label>
                    <input type="date" id="date">
                </div>

                <div class="form-group">
                    <label for="doctor">Doctor</label>
                    <select id="doctor">
                        <option selected>Select a date first</option>
                    </select>
                </div>

            </div>

            <!-- Time & Purpose -->
            <div class="form-row">

                <div class="form-group">
                    <label for="time">Time</label>
                    <select id="time">
                        <option selected>Select a doctor first</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="purpose">Purpose</label>
                    <select id="purpose">
                        <option selected>Check-up</option>
                        <option>Consultation</option>
                        <option>Follow-up</option>
                        <option>Vaccination</option>
                    </select>
                </div>

            </div>

            <!-- Buttons -->
            <div class="form-actions">

                <button type="submit" class="btn btn-primary">
                    Confirm Appointment
                </button>

                <button type="button" class="btn btn-secondary">
                    Cancel
                </button>

            </div>

        </form>

    </div>

</div>

</body>
</html>