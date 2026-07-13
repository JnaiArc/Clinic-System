<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../view/login/login.php");
    exit();
}

require_once 'c:/xampp/htdocs/clinic1/config/Database.php';
require_once 'c:/xampp/htdocs/clinic1/model/Patient.php';

$database = new Database();
$db = $database->connect();
$patientObj = new Patient($db);
$result = $patientObj->getAllPatients();
$patients = $result->fetchAll(PDO::FETCH_ASSOC);

$dom = new DOMDocument("1.0", "UTF-8");
$root = $dom->createElement("patients");
$dom->appendChild($root);

foreach ($patients as $p) {
    $patient = $dom->createElement("patient");
    
    $id = $dom->createElement("id", $p['id']);
    $first_name = $dom->createElement("first_name", $p['first_name']);
    $last_name = $dom->createElement("last_name", $p['last_name']);
    $gender = $dom->createElement("gender", $p['gender']);
    $birthdate = $dom->createElement("birthdate", $p['birthdate']);
    $phone = $dom->createElement("phone", $p['phone']);
    $email = $dom->createElement("email", $p['email']);
    $address = $dom->createElement("address", $p['address']);
    $emergency_contact = $dom->createElement("emergency_contact", $p['emergency_contact']);
    $allergies = $dom->createElement("allergies", $p['allergies']);
    $medical_history = $dom->createElement("medical_history", $p['medical_history']);
    
    $patient->appendChild($id);
    $patient->appendChild($first_name);
    $patient->appendChild($last_name);
    $patient->appendChild($gender);
    $patient->appendChild($birthdate);
    $patient->appendChild($phone);
    $patient->appendChild($email);
    $patient->appendChild($address);
    $patient->appendChild($emergency_contact);
    $patient->appendChild($allergies);
    $patient->appendChild($medical_history);
    
    $root->appendChild($patient);
}

header('Content-Type: application/xml');
header('Content-Disposition: attachment; filename="patients_export.xml"');
echo $dom->saveXML();
exit();
?>