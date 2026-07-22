<?php
session_start();

require_once 'c:/xampp/htdocs/clinic1/config/Database.php';
require_once 'c:/xampp/htdocs/clinic1/model/Appointment.php';

$database = new Database();
$conn = $database->connect();
$appointment_model = new Appointment($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    
    // SAVE CONSULTATION
    if (isset($_POST['saveConsultation'])){
        $appointment_id = $_POST['appointment_id'];
        $patient_id = $_POST['patient_id'];
        $findings = $_POST['findings'];
        $followup_needed = $_POST['followup_needed'];
        $followup_date = !empty($_POST['followup_date']) ? $_POST['followup_date'] : null;

        // Guard: can't complete a consultation before the appointment's scheduled date arrives
        $existing_appointment = $appointment_model->getAppointmentById($appointment_id);
        if (!$existing_appointment || (int)$existing_appointment['doctor_id'] !== (int)$_SESSION['user_id']) {
            header("Location: http://localhost/clinic1/view/doctor/doctor_appointments.php");
            exit();
        }
        if ($existing_appointment['appointment_date'] > date('Y-m-d') && $existing_appointment['status'] !== 'completed') {
            $_SESSION['consult_error'] = "This appointment is scheduled for " . date('F j, Y', strtotime($existing_appointment['appointment_date'])) . ". You can't complete the consultation until that date.";
            header("Location: http://localhost/clinic1/view/doctor/doctor_appointments.php");
            exit();
        }
        
        // Insert consultation
        $query = "INSERT INTO consultations (appointment_id, doctor_id, patient_id, findings, followup_needed, followup_date, status) 
                  VALUES (:appointment_id, :doctor_id, :patient_id, :findings, :followup_needed, :followup_date, :status)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(":appointment_id", $appointment_id);
        $stmt->bindParam(":doctor_id", $_SESSION['user_id']);
        $stmt->bindParam(":patient_id", $patient_id);
        $stmt->bindParam(":findings", $findings);
        $stmt->bindParam(":followup_needed", $followup_needed);
        $stmt->bindParam(":followup_date", $followup_date);
        
        if($followup_needed == 'yes') {
            $stmt->bindValue(":status", "pending");
        } else {
            $stmt->bindValue(":status", "completed");
        }
        
        $stmt->execute();
        $consultation_id = $conn->lastInsertId();
        
        // Save medicines
        if(!empty($_POST['medicine_name'][0])){
            $medicine_names = $_POST['medicine_name'];
            $dosages = $_POST['dosage'];
            $frequencies = $_POST['frequency'];
            $durations = $_POST['duration'];
            
            for($i = 0; $i < count($medicine_names); $i++){
                if(!empty($medicine_names[$i])){
                    $query = "INSERT INTO consultation_medicines (consultation_id, medicine_name, dosage, frequency, duration) 
                              VALUES (:consultation_id, :medicine_name, :dosage, :frequency, :duration)";
                    $stmt = $conn->prepare($query);
                    $stmt->bindParam(":consultation_id", $consultation_id);
                    $stmt->bindParam(":medicine_name", $medicine_names[$i]);
                    $stmt->bindParam(":dosage", $dosages[$i]);
                    $stmt->bindParam(":frequency", $frequencies[$i]);
                    $stmt->bindParam(":duration", $durations[$i]);
                    $stmt->execute();
                }
            }
        }
        
        // Save recommendations
        if(!empty($_POST['recommendation'][0])){
            $recommendations = $_POST['recommendation'];
            
            foreach($recommendations as $rec){
                if(!empty($rec)){
                    $query = "INSERT INTO consultation_recommendations (consultation_id, recommendation) VALUES (:consultation_id, :recommendation)";
                    $stmt = $conn->prepare($query);
                    $stmt->bindParam(":consultation_id", $consultation_id);
                    $stmt->bindParam(":recommendation", $rec);
                    $stmt->execute();
                }
            }
        }
        
        // Update appointment status
        $new_status = ($followup_needed == 'yes') ? 'completed' : 'completed';
        $query = "UPDATE appointments SET status = :status WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(":status", $new_status);
        $stmt->bindParam(":id", $appointment_id);
        $stmt->execute();
        
        // If followup needed(yes), create followup appointment
        if($followup_needed == 'yes' && $followup_date){
            $followup_time = !empty($_POST['followup_time']) ? $_POST['followup_time'] : '9:00 AM';
            $query = "INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, purpose, status) 
                      VALUES (:patient_id, :doctor_id, :followup_date, :followup_time, 'Follow-up', 'pending')";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(":patient_id", $patient_id);
            $stmt->bindParam(":doctor_id", $_SESSION['user_id']);
            $stmt->bindParam(":followup_date", $followup_date);
            $stmt->bindParam(":followup_time", $followup_time);
            $stmt->execute();
        }
        
        header("Location: http://localhost/clinic1/view/doctor/doctor_appointments.php");
        exit();
    }
    
}
?>