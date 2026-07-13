<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: http://localhost/clinic1/view/login/login.php");
    exit();
}

require_once 'C:\xampp\htdocs\clinic1\config\Database.php';
require_once 'C:\xampp\htdocs\clinic1\model\Appointment.php';

$database = new Database();
$conn = $database->connect();
$appointment = new Appointment($conn);

$appointment_id = (int)$_GET['id'];
$appointment_data = $appointment->getAppointmentById($appointment_id);

$consultation = null;
$medicines = null;
$recommendations = null;

if ($appointment_data) {
    $consultation = $appointment->getConsultationForAppointment($appointment_id, $appointment_data['patient_id']);
    
    if ($consultation) {
        $medicines = $appointment->getMedicinesByConsultationId($consultation['id']);
        $recommendations = $appointment->getRecommendationsByConsultationId($consultation['id']);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Follow-Up</title>
    <link rel="stylesheet" href="../css/admin_dashboard.css">
    <link rel="stylesheet" href="../css/admin_info.css">
</head>

<body>
<div class="dashboard-container">
    <aside class="sidebar-simple">
        <a href="http://localhost/clinic1/view/admin/admin_followup.php?id=<?php echo $appointment_id; ?>" class="back-btn-top">
            <div class="icon">←</div>
            <span>Back</span>
        </a>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <div class="topbar-left">
                <img src="http://localhost/clinic1/logo.jpg" class="clinic-logo">
                <div class="clinic-text"><h1>SwiftCare Clinic</h1><p>Edit Follow-Up</p></div>
            </div>
            <div class="admin-box">Admin</div>
        </header>

        <section class="table-section section-gap">
            <div class="section-header">Appointment Information</div>
            <div class="info-grid info-grid-pad">
                <div class="info-item"><label>Patient</label><span><?php echo $appointment_data['patient_name']; ?></span></div>
                <div class="info-item"><label>Doctor</label><span>Dr. <?php echo $appointment_data['doctor_name']; ?></span></div>
                <div class="info-item"><label>Date</label><span><?php echo $appointment_data['appointment_date']; ?></span></div>
                <div class="info-item"><label>Time</label><span><?php echo $appointment_data['appointment_time']; ?></span></div>
                <div class="info-item"><label>Purpose</label><span><?php echo $appointment_data['purpose']; ?></span></div>
                <div class="info-item"><label>Status</label><span class="status <?php echo $appointment_data['status']; ?>"><?php echo ucfirst($appointment_data['status']); ?></span></div>
            </div>
        </section>

        <form method="POST" action="http://localhost/clinic1/controller/FollowUpController.php">
            <input type="hidden" name="appointment_id" value="<?php echo $appointment_id; ?>">
            
            <section class="table-section rx-section">
                <div class="section-header">Prescription (Check when done)</div>
                
                <?php if($medicines && $medicines->rowCount() > 0): ?>
                <div class="info-grid-pad">
                    <label class="rx-label">Medicines</label>
                    <?php while($med = $medicines->fetch(PDO::FETCH_ASSOC)): 
                        $is_done = isset($med['is_done']) ? $med['is_done'] : 0; 
                    ?>
                    <div class="rx-item-edit">
                        <input type="checkbox" name="medicines_done[]" value="<?php echo $med['id']; ?>" <?php echo $is_done ? 'checked' : ''; ?>>
                        <span class="rx-flex"><?php echo $med['medicine_name']; ?> - <?php echo $med['dosage']; ?> x <?php echo $med['frequency']; ?> for <?php echo $med['duration']; ?></span>
                    </div>
                    <?php endwhile; ?>
                </div>
                <?php endif; ?>
                
                <?php if($recommendations && $recommendations->rowCount() > 0): ?>
                <div class="info-grid-pad">
                    <label class="rx-label">Recommendations</label>
                    <?php while($rec = $recommendations->fetch(PDO::FETCH_ASSOC)): 
                        $is_done = isset($rec['is_done']) ? $rec['is_done'] : 0; 
                    ?>
                    <div class="rx-item-edit">
                        <input type="checkbox" name="recommendations_done[]" value="<?php echo $rec['id']; ?>" <?php echo $is_done ? 'checked' : ''; ?>>
                        <span class="rx-flex"><?php echo $rec['recommendation']; ?></span>
                    </div>
                    <?php endwhile; ?>
                </div>
                <?php endif; ?>
            </section>

            <div class="btn-row">
                <button type="submit" name="savePrescription" class="btn-save">Save</button>
            </div>
        </form>
    </main>
</div>

</body>

</html>