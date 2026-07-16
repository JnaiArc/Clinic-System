<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../view/login/login.php");
    exit();
}

require_once 'c:/xampp/htdocs/clinic1/config/Database.php';
require_once 'c:/xampp/htdocs/clinic1/model/Patient.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['xml_file'])) {

    $file = $_FILES['xml_file'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $message = "error|File upload failed.";
    } 
    elseif (pathinfo($file['name'], PATHINFO_EXTENSION) !== 'xml') {
        $message = "error|Please upload .xml file only.";
    } 
    else {
        $dom = new DOMDocument();
        $dom->load($file['tmp_name']);
        $patients = $dom->getElementsByTagName("patient");
        
        if ($patients->length === 0) {
            $message = "error|No patient records found.";
        } 
        else {
            $database = new Database();
            $db = $database->connect();
            $patientObj = new Patient($db);
            
            $imported = 0;
            $skipped = 0;
            
            foreach ($patients as $p) {
                $first_name = $p->getElementsByTagName("first_name")->item(0)->nodeValue;
                $last_name = $p->getElementsByTagName("last_name")->item(0)->nodeValue;
                $gender = $p->getElementsByTagName("gender")->item(0)->nodeValue;
                $birthdate = $p->getElementsByTagName("birthdate")->item(0)->nodeValue;
                $phone = $p->getElementsByTagName("phone")->item(0)->nodeValue;
                $email = $p->getElementsByTagName("email")->item(0)->nodeValue;
                $address = $p->getElementsByTagName("address")->item(0)->nodeValue;
                $emergency_contact = $p->getElementsByTagName("emergency_contact")->item(0)->nodeValue;
                $allergies = $p->getElementsByTagName("allergies")->item(0)->nodeValue;
                $medical_history = $p->getElementsByTagName("medical_history")->item(0)->nodeValue;
                
                if (empty($first_name) || empty($last_name)) {
                    $skipped++;
                    continue;
                }
                
                $patientObj->addPatient(
                    $first_name, $last_name, $gender, $birthdate,
                    $phone, $email, $address, $emergency_contact,
                    $allergies, $medical_history
                );
                $imported++;
            }
            
            $message = "success|Imported $imported patient(s).";
            if ($skipped > 0) $message .= " $skipped skipped.";
        }
    }
}

$msgType = "";
$msgText = "";
if ($message) {
    [$msgType, $msgText] = explode("|", $message, 2);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Import Patients</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../view/css/admin.css">
</head>
<body>
<div class="container mt-5" style="max-width: 550px;">
    <h4 class="mb-4">Import Patients from XML</h4>

    <?php if ($msgText): ?>
        <div class="alert alert-<?= $msgType === 'success' ? 'success' : 'danger' ?>">
            <?= htmlspecialchars($msgText) ?>
        </div>
    <?php endif; ?>

    <div class="card p-4">
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label>Select XML File</label>
                <input type="file" name="xml_file" accept=".xml" class="form-control" required>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Import</button>
                <a href="../view/admin/admin_patientRecord.php" class="btn btn-secondary">Back</a>
            </div>
        </form>
    </div>

    <div class="mt-4">
        <p>XML Format</p>
        <pre class="bg-light p-3">
            &lt;patients&gt;
                &lt;patient&gt;
                    &lt;id&gt;1&lt;/id&gt;
                    &lt;first_name&gt;Jonai&lt;/first_name&gt;
                    &lt;last_name&gt;Arcenal&lt;/last_name&gt;
                    &lt;gender&gt;Female&lt;/gender&gt;
                    &lt;birthdate&gt;2005-12-1&lt;/birthdate&gt;
                    &lt;phone&gt;09123456789&lt;/phone&gt;
                    &lt;email&gt;jonai@email.com&lt;/email&gt;
                    &lt;address&gt;Cavitela&lt;/address&gt;
                    &lt;emergency_contact&gt;09998765432&lt;/emergency_contact&gt;
                    &lt;allergies&gt;Peanuts&lt;/allergies&gt;
                    &lt;medical_history&gt;ASthma&lt;/medical_history&gt;
                &lt;/patient&gt;
            &lt;/patients&gt;
        </pre>
    </div>
</div>
</body>
</html>