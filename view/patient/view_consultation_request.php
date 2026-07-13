<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard</title>
    <link rel="stylesheet" href="../css/admin_dashboard.css">
</head>

<body>

<div class="dashboard-container">

    <aside class="sidebar">

        <div class="sidebar-top">
           <!-- PATIENT NAME -->
            <h1>Reymar</h1>
            <h3>Patient</h3>
        </div>

        <nav class="sidebar-menu">
            <a href="patient_dashboard.php" class="menu-item">Home</a>
            <a href="" class="menu-item">Patient Profile</a>
            <a href="request_consultation.php" class="menu-item ">Request Consultation</a>
            <a href="view_consultation_request.php" class="menu-item active">View Request Status</a>
            <a href="#" class="menu-item">Appointments</a>
            <a href="#" class="menu-item">Request Medical Documents</a>

            <a href="#" class="menu-item">Services</a>

            <a href="#" class="menu-item">Account</a>
            <a href="#" class="menu-item logout-btn" onclick="return confirm('Logout?')">Logout</a>
        </nav>

    </aside>

    <main class="main-content">

        <header class="topbar">
            <h1>APPOINTMENT INFO</h1>
        </header>

        

    </main>

</div>

</body>

</html>