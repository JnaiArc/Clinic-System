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

<main class="main-content">

    <header class="topbar">
        <h1>Schedule Appointment</h1>
    </header>

    <div class="profile-wrap">

        <form>

            <div class="profile-card">
                <h2>Schedule Appointment</h2>
                <p class="subtitle">Book a consultation with one of our doctors</p>
                <p class="required-note"><span style="color:red">*</span> Required Fields</p>

                <div class="form-grid">

                    <!-- Patient Name -->
                    <div class="field">
                        <label for="patient-name">Patient Name <span style="color:red">*</span></label>
                        <input type="text" id="patient-name" value="Charmane Searchwell">
                    </div>

                    <!-- Phone Number -->
                    <div class="field">
                        <label for="phone-number">Phone Number <span style="color:red">*</span></label>
                        <input type="tel" id="phone-number" value="09870542944">
                    </div>

                    <!-- Date -->
                    <div class="field">
                        <label for="date">Date <span style="color:red">*</span></label>
                        <input type="date" id="date">
                    </div>

                    <!-- Doctor -->
                    <div class="field">
                        <label for="doctor">Doctor <span style="color:red">*</span></label>
                        <select id="doctor">
                            <option selected>Select a date first</option>
                        </select>
                    </div>

                    <!-- Time -->
                    <div class="field">
                        <label for="time">Time <span style="color:red">*</span></label>
                        <select id="time">
                            <option selected>Select a doctor first</option>
                        </select>
                    </div>

                    <!-- Purpose -->
                    <div class="field">
                        <label for="purpose">Purpose <span style="color:red">*</span></label>
                        <select id="purpose">
                            <option selected>Check-up</option>
                            <option>Consultation</option>
                            <option>Follow-up</option>
                            <option>Vaccination</option>
                        </select>
                    </div>

                </div>

                <br>
                <div class="button-group">
                    <button type="submit" class="btn-save">Confirm Appointment</button>
                    <button type="button" class="btn-cancel">Cancel</button>
                </div>
            </div>

        </form>

    </div>

</main>

</body>
</html>