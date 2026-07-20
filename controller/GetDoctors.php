<?php
require_once 'c:/xampp/htdocs/clinic1/config/Database.php';

header('Content-Type: application/json');

$database = new Database();
$conn = $database->connect();

// Doctors are now looked up by specialization (the specialization -> doctor step of the
// booking flow), instead of the old day-of-week lookup.
$specialization = $_GET['specialization'] ?? '';

$doctors = [];

if ($specialization !== '') {
    $query = "SELECT u.id, u.first_name, u.last_name, d.specialization, d.license_number, u.profile_photo, 
                     d.schedule_days, d.schedule_time_start, d.schedule_time_end
              FROM users u
              INNER JOIN doctors d ON d.user_id = u.id
              WHERE u.role = 'doctor' AND d.specialization = :specialization 
              ORDER BY u.first_name ASC";
    $stmt = $conn->prepare($query);
    $stmt->bindValue(":specialization", $specialization);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $doctors[] = $row;
    }
}

echo json_encode($doctors);
