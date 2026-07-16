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
            <a href="patient_view_appointment.php"class="active">Appointment</a>
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
            <h1>Appointment Info</h1>
        </header>

        <div class="appointments-wrap">

            <div class="info-banner">
                <p>Select an appointment that you want to view or manage.</p>
            </div>

            <!-- Tabs Navigation -->
            <div class="tabs-container">
                <button class="tab-btn active" onclick="switchTab(event, 'outstanding')">Outstanding Requests</button>
                <button class="tab-btn" onclick="switchTab(event, 'past')">Past Requests</button>
            </div>

            <!-- Outstanding Requests Tab Panel -->
            <div id="outstanding" class="tab-content active">
                <table class="appointments-table">
                    <thead>
                        <tr>
                            <th>Consult Reference Number</th>
                            <th>Chief Complaint</th>
                            <th>Date of Appointment</th>
                            <th>Status</th>
                            <th style="text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>APT-2026-001</strong></td>
                            <td>Regular Checkup / Follow-up on lab results</td>
                            <td>July 18, 2026</td>
                            <td><span class="status-badge pending">Pending Approval</span></td>
                            <td style="text-align: center;"><a href="#" class="btn-view">View</a></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Past Requests Tab Panel -->
            <div id="past" class="tab-content">
                <table class="appointments-table">
                    <thead>
                        <tr>
                            <th>Consult Reference Number</th>
                            <th>Chief Complaint</th>
                            <th>Date of Appointment</th>
                            <th>Status</th>
                            <th style="text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>APT-2026-002</strong></td>
                            <td>Fever and dry cough for 3 days</td>
                            <td>January 12, 2026</td>
                            <td><span class="status-badge completed">Completed</span></td>
                            <td style="text-align: center;"><a href="#" class="btn-view">View</a></td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>

    </main>

</div>

<script>
function switchTab(event, tabId) {
    const contents = document.querySelectorAll('.tab-content');
    contents.forEach(content => content.classList.remove('active'));

    const buttons = document.querySelectorAll('.tab-btn');
    buttons.forEach(btn => btn.classList.remove('active'));

    document.getElementById(tabId).classList.add('active');
    event.currentTarget.classList.add('active');
}
</script>

</body>
</html>