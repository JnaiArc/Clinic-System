<?php
session_start();
require_once 'c:/xampp/htdocs/clinic1/config/Database.php';
require_once 'c:/xampp/htdocs/clinic1/model/Appointment.php';
require_once 'c:/xampp/htdocs/clinic1/model/Patient.php';

$database = new Database();
$conn = $database->connect();
$appointment = new Appointment($conn);
$patientModel = new Patient($conn);

$is_patient_user = isset($_SESSION['user_id']) && $_SESSION['role'] === 'patient';

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    
    // BOOK APPOINTMENT
    if (isset($_POST['bookAppointment'])){
        $doctor_id = $_POST['doctor_id'];
        $appointment_date = $_POST['appointment_date'];
        $appointment_time = $_POST['appointment_time'];
        $purpose = !empty($_POST['purpose']) ? $_POST['purpose'] : 'Check-up';
        $complaint = !empty($_POST['complaint']) ? trim($_POST['complaint']) : '';
        $consultation_type = !empty($_POST['consultation_type']) ? $_POST['consultation_type'] : 'In Person';

        if ($is_patient_user){
            // Patient portal booking: never trust a posted patient_id, always resolve to the
            // patient record linked to the logged-in account so patients can't book for others.
            $own_patient = $patientModel->getPatientByUserId($_SESSION['user_id']);
            if (!$own_patient || !$patientModel->isProfileComplete($own_patient)){
                header("Location: http://localhost/clinic1/view/patient/patient_profile.php");
                exit();
            }
            // Prevent double-booking: a patient can only have one pending/confirmed appointment at a time.
            if ($appointment->hasActiveAppointment($own_patient['id'])){
                header("Location: http://localhost/clinic1/view/patient/patient_request consultation.php");
                exit();
            }
            $patient_id = $own_patient['id'];
        } else {
            // Admin/front-desk booking (admin_patientRecord.php -> book_consultation.php)
            $patient_id = $_POST['patient_id'];
        }

        // Guard against double-booking the same doctor/date/time slot (race condition safety net,
        // on top of the client-side calendar/time picker that already greys out taken slots).
        if ($appointment->isSlotTaken($doctor_id, $appointment_date, $appointment_time)){
            $_SESSION['booking_error'] = "Sorry, that time slot was just taken by another patient. Please pick a different time.";
            if ($is_patient_user){
                header("Location: http://localhost/clinic1/view/patient/patient_request consultation.php");
            } else {
                $back_patient_id = $_POST['patient_id'] ?? '';
                header("Location: http://localhost/clinic1/view/admin/book_consultation.php?patient_id=" . urlencode($back_patient_id));
            }
            exit();
        }
        
        try {
            $result = $appointment->addAppointment($patient_id, $doctor_id, $appointment_date, $appointment_time, $purpose, $consultation_type, $complaint);
            
            if ($result){
                if ($is_patient_user){
                    header("Location: http://localhost/clinic1/view/patient/patient_view_appointment.php");
                } else {
                    header("Location: http://localhost/clinic1/view/admin/admin_appointments.php");
                }
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
        $complaint = trim($_POST['complaint'] ?? '');

        if ($appointment->updateAppointmentSimple($id, $doctor_id, $appointment_date, $appointment_time, $purpose, $complaint)){
            header("Location: http://localhost/clinic1/view/admin/admin_appointments.php");
            exit();
        }
    }

    // CANCEL APPOINTMENT (patient portal)
    if (isset($_POST['cancelAppointment'])){
        if (!$is_patient_user){
            header("Location: http://localhost/clinic1/view/login/login.php");
            exit();
        }
        $own_patient = $patientModel->getPatientByUserId($_SESSION['user_id']);
        $id = $_POST['appointment_id'];
        if ($own_patient){
            $appointment->cancelAppointment($id, $own_patient['id']);
        }
        header("Location: http://localhost/clinic1/view/patient/patient_view_appointment.php");
        exit();
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

// CANCEL APPOINTMENT (admin portal, via link)
if (isset($_GET['adminCancel'])){
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
        header("Location: http://localhost/clinic1/view/login/login.php");
        exit();
    }
    $id = (int)$_GET['adminCancel'];
    $appointment->cancelAppointmentAdmin($id);
    header("Location: http://localhost/clinic1/view/admin/admin_appointments.php");
    exit();
}
?>