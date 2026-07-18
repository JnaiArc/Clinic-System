<?php
require_once 'c:/xampp/htdocs/clinic1/config/Database.php';
require_once 'c:/xampp/htdocs/clinic1/config/Validation.php';
require_once 'c:/xampp/htdocs/clinic1/model/Patient.php';

$database = new Database();
$conn = $database->connect();
$patient = new Patient($conn);
$validation = new Validation();

if ($_SERVER['REQUEST_METHOD'] === 'POST'){

    // PATIENT PORTAL: SAVE OWN PROFILE (create on first fill-up, update afterwards)
    if (isset($_POST['savePatientProfile'])){
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
            header("Location: http://localhost/clinic1/view/login/login.php");
            exit();
        }

        $first_name       = trim($_POST['first_name']);
        $last_name        = trim($_POST['last_name']);
        $gender           = $_POST['gender'];
        $birthdate        = $_POST['birthdate'];
        $phone            = trim($_POST['phone']);
        $email            = trim($_POST['email']);
        $address          = trim($_POST['address']);
        $emergency_contact = $_POST['emergency_contact'];
        $allergies        = $_POST['allergies'];
        $medical_history  = $_POST['medical_history'];

        try {
            $validation->patient($first_name, $last_name, $gender, $birthdate, $phone, $email, $address);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: http://localhost/clinic1/view/patient/patient_profile.php" . (isset($_POST['id']) && $_POST['id'] ? "?edit=1" : ""));
            exit();
        }

        // Does this patient account already have a linked patients record?
        $existing = $patient->getPatientByUserId($_SESSION['user_id']);

        if ($existing) {
            $result = $patient->updatePatient($existing['id'], $first_name, $last_name, $gender, $birthdate, $phone, $email, $address, $emergency_contact, $allergies, $medical_history);
        } else {
            $result = $patient->addPatient($first_name, $last_name, $gender, $birthdate, $phone, $email, $address, $emergency_contact, $allergies, $medical_history, $_SESSION['user_id']);
        }

        if ($result){
            $_SESSION['success'] = "Your profile details have been updated successfully!";
            header("Location: http://localhost/clinic1/view/patient/patient_profile.php");
            exit();
        } else {
            echo "Failed to save patient profile";
        }
    }

    // ADD PATIENT
    if (isset($_POST['addPatient'])){
        $first_name       = trim($_POST['first_name']);
        $last_name        = trim($_POST['last_name']);
        $gender           = $_POST['gender'];
        $birthdate        = $_POST['birthdate'];
        $phone            = trim($_POST['phone']);
        $email            = trim($_POST['email']);
        $address          = trim($_POST['address']);
        $emergency_contact = $_POST['emergency_contact'];
        $allergies        = $_POST['allergies'];
        $medical_history  = $_POST['medical_history'];

        try {
            $validation->patient($first_name, $last_name, $gender, $birthdate, $phone, $email, $address);
        } catch (Exception $e) {
            session_start();
            $_SESSION['error'] = $e->getMessage();
            header("Location: http://localhost/clinic1/view/admin/add_patient.php");
            exit();
        }

        if ($patient->addPatient($first_name, $last_name, $gender, $birthdate, $phone, $email, $address, $emergency_contact, $allergies, $medical_history)){
            header("Location: http://localhost/clinic1/view/admin/admin_patientRecord.php");
            exit();
        } else {
            echo "Failed to add patient";
        }
    }
    
    // UPDATE PATIENT
    if (isset($_POST['updatePatient'])){
        $id               = $_POST['id'];
        $first_name       = trim($_POST['first_name']);
        $last_name        = trim($_POST['last_name']);
        $gender           = $_POST['gender'];
        $birthdate        = $_POST['birthdate'];
        $phone            = trim($_POST['phone']);
        $email            = trim($_POST['email']);
        $address          = trim($_POST['address']);
        $emergency_contact = $_POST['emergency_contact'];
        $allergies        = $_POST['allergies'];
        $medical_history  = $_POST['medical_history'];

        try {
            $validation->patient($first_name, $last_name, $gender, $birthdate, $phone, $email, $address);
        } catch (Exception $e) {
            session_start();
            $_SESSION['error'] = $e->getMessage();
            header("Location: http://localhost/clinic1/view/admin/view_patient.php?id=" . $id);
            exit();
        }

        if ($patient->updatePatient($id, $first_name, $last_name, $gender, $birthdate, $phone, $email, $address, $emergency_contact, $allergies, $medical_history)){
            header("Location: http://localhost/clinic1/view/admin/view_patient.php?id=" . $id);
            exit();
        } else {
            echo "Failed to update patient";
        }
    }
    
    // DELETE PATIENT
    if (isset($_POST['deletePatient'])){
        $id = $_POST['id'];
        $patient->deletePatient($id);
        header("Location: http://localhost/clinic1/view/admin/admin_patientRecord.php");
        exit();
    }
    
}
?>
