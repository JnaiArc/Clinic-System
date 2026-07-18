<?php
session_start();

require_once 'C:\xampp\htdocs\clinic1\config\Database.php';
require_once 'C:\xampp\htdocs\clinic1\model\Appointment.php';

$database = new Database();
$conn = $database->connect();
$appointment = new Appointment($conn);

// This checklist is a doctor-only action (moved over from the old admin follow-up page).
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: http://localhost/clinic1/view/login/login.php");
    exit();
}

// SAVE PERSCRIPTION: CHECKBOX
if (isset($_POST['savePrescription'])) {
    $appointment_id = (int)($_POST['appointment_id'] ?? 0);
    
    if ($appointment_id > 0) {
        $appointment_data = $appointment->getAppointmentById($appointment_id);
        
        // Only the doctor assigned to this appointment may update its checklist.
        if ($appointment_data && (int)$appointment_data['doctor_id'] === (int)$_SESSION['user_id']) {
            $consultation = $appointment->getConsultationForAppointment($appointment_id, $appointment_data['patient_id']);
            
            if ($consultation) {
                $consultation_id = $consultation['id'];
                
                // MEDICINES
                // Get all medicines
                $sql = "SELECT id FROM consultation_medicines WHERE consultation_id = :consultation_id";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(":consultation_id", $consultation_id);
                $stmt->execute();
                $all_medicines = $stmt->fetchAll(PDO::FETCH_COLUMN);
                
                // Get checked medicines
                $medicines_done = isset($_POST['medicines_done']) ? $_POST['medicines_done'] : [];
                
                // Update each medicine: set to 1 if checked, 0 if not checked
                foreach ($all_medicines as $med_id) {
                    $med_id = (int)$med_id;
                    $is_done = in_array($med_id, $medicines_done) ? 1 : 0;
                    
                    $sql = "UPDATE consultation_medicines SET is_done = :is_done WHERE id = :id";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(":is_done", $is_done);
                    $stmt->bindParam(":id", $med_id);
                    $stmt->execute();
                }
                
                // RECOMMENDATIONS
                // Get all recommendations
                $sql = "SELECT id FROM consultation_recommendations WHERE consultation_id = :consultation_id";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(":consultation_id", $consultation_id);
                $stmt->execute();
                $all_recommendations = $stmt->fetchAll(PDO::FETCH_COLUMN);
                
                // Get checked recommendations
                $recommendations_done = isset($_POST['recommendations_done']) ? $_POST['recommendations_done'] : [];
                
                // Update each recommendation - set to 1 if checked, 0 if not checked
                foreach ($all_recommendations as $rec_id) {
                    $rec_id = (int)$rec_id;
                    $is_done = in_array($rec_id, $recommendations_done) ? 1 : 0;
                    
                    $sql = "UPDATE consultation_recommendations SET is_done = :is_done WHERE id = :id";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(":is_done", $is_done);
                    $stmt->bindParam(":id", $rec_id);
                    $stmt->execute();
                }
            }
        }
    }
    
    header("Location: http://localhost/clinic1/view/doctor/doctor_followup.php?edit=" . $appointment_id);
    exit();
}

header("Location: http://localhost/clinic1/view/doctor/doctor_followup.php");
exit();
?>