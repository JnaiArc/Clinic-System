<?php
require_once 'c:/xampp/htdocs/clinic1/config/Database.php';
require_once 'c:/xampp/htdocs/clinic1/model/Appointment.php';

$database = new Database();
$conn = $database->connect();
$appointment = new Appointment($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    
    // BOOK APPOINTMENT
    if (isset($_POST['bookAppointment'])){
        $patient_id = $_POST['patient_id'];
        $doctor_id = $_POST['doctor_id'];
        $appointment_date = $_POST['appointment_date'];
        $appointment_time = $_POST['appointment_time'];
        $purpose = !empty($_POST['purpose']) ? $_POST['purpose'] : 'Check-up';
        
        try {
            $result = $appointment->addAppointment($patient_id, $doctor_id, $appointment_date, $appointment_time, $purpose);
            
            if ($result){
                header("Location: http://localhost/clinic1/view/admin/admin_appointments.php");
                exit();
            } else {
                echo "Failed to book appointment - result was false";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
    
    // UPDATE APPOINTMENT
    if (isset($_POST['updateAppointment'])){
        $id = $_POST['appointment_id'];
        $doctor_id = $_POST['doctor_id'];
        $appointment_date = $_POST['appointment_date'];
        $appointment_time = $_POST['appointment_time'];
        $purpose = $_POST['purpose'] ?? 'Check-up';

        if ($appointment->updateAppointmentSimple($id, $doctor_id, $appointment_date, $appointment_time, $purpose)){
            header("Location: http://localhost/clinic1/view/admin/admin_appointments.php");
            exit();
        }
    }
    
    // DELETE APPOINTMENT
    if (isset($_POST['deleteAppointment'])){
        $id = $_POST['id'];
        $appointment->deleteAppointment($id);
        header("Location: http://localhost/clinic1/view/admin/admin_appointments.php");
        exit();
    }
    
}

if (isset($_GET['delete'])){
    $id = (int)$_GET['delete'];
    $appointment->deleteAppointment($id);
    header("Location: http://localhost/clinic1/view/admin/admin_appointments.php");
    exit();
}
?>