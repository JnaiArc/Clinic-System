<?php
require_once 'c:/xampp/htdocs/clinic1/config/Database.php';

$database = new Database();
$conn = $database->connect();

$day = $_GET['day'] ?? '';

$doctors = [];

if ($day) {
    $query = "SELECT * FROM users WHERE role = 'doctor' AND schedule_days LIKE :day";
    $stmt = $conn->prepare($query);
    $stmt->bindValue(":day", "%$day%");
    $stmt->execute();
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $doctors[] = $row;
    }
}

echo json_encode($doctors);
?>