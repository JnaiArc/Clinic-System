<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: http://localhost/clinic1/view/login/login.php");
    exit();
}

require_once 'C:\xampp\htdocs\clinic1\config\Database.php';
require_once 'C:\xampp\htdocs\clinic1\model\Patient.php';

$database = new Database();
$conn = $database->connect();
$patient = new Patient($conn);

$patient_id = $_GET['patient_id'] ?? 0;
$selected_patient = $patient_id ? $patient->getPatientById($patient_id) : null;

// get date today
$today = date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Consultation</title>
    <link rel="stylesheet" href="../css/admin_dashboard.css">
    <link rel="stylesheet" href="../css/admin_add_view.css">
    <link rel="stylesheet" href="../css/admin_appointment.css">
</head>

<body>

<div class="dashboard-container">
    <aside class="sidebar-simple">
        <a href="http://localhost/clinic1/view/admin/admin_patientRecord.php" class="back-btn-top">
            <div class="icon">←</div>
            <span>Back</span>
        </a>
    </aside>

    <main class="main-content">

        <header class="topbar">
            <div class="topbar-left">
                <img src="http://localhost/clinic1/logo.jpg" class="clinic-logo">
                <div class="clinic-text">
                    <h1>SwiftCare Clinic</h1>
                    <p>Schedule Appointment</p>
                </div>
            </div>
            <div class="admin-box">Admin</div>
        </header>

        <section class="table-section">

            <div class="section-header">Schedule Appointment</div>

            <form method="POST" action="http://localhost/clinic1/controller/AppointmentController.php" class="appointment-body">
                
                <div class="form-group">
                    <label>Patient Name</label>
                    <input type="text" value="<?php echo $selected_patient ? $selected_patient['first_name'].' '.$selected_patient['last_name'] : ''; ?>" readonly>
                    <input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
                </div>

                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone" id="phoneInput" value="<?php echo $selected_patient ? $selected_patient['phone'] : ''; ?>" maxlength="11" minlength="11" required pattern="[0-9]{11}" title="Must be exactly 11 digits" oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                </div>

                <div class="form-group">
                    <label>Date</label>
                    <input type="date" name="appointment_date" id="appointmentDate" min="<?php echo $today; ?>" required>
                </div>

                <div class="form-group">
                    <label>Doctor</label>
                    <select id="doctorSelect" name="doctor_id" required>
                        <option value="">Select a date first</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Time</label>
                    <select id="timeSelect" name="appointment_time" required>
                        <option value="">Select a doctor first</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Purpose</label>
                    <select name="purpose" required>
                        <option value="Check-up" selected>Check-up</option>
                    </select>
                </div>

                <div class="view-buttons span-2">
                    <button type="submit" name="bookAppointment" class="save-btn">Confirm Appointment</button>
                    <a href="http://localhost/clinic1/view/admin/admin_patientRecord.php" class="back-link">Cancel</a>
                </div>

            </form>

        </section>

    </main>

</div>

<script src="../js/admin.js"></script>

</body>
</html>