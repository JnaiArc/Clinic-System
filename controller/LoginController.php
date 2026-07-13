<?php
session_start();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/Validation.php';
require_once __DIR__ . '/../model/User.php';

$database = new Database();
$conn = $database->connect();
$user = new User($conn);
$validation = new Validation();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['loginBtn'])) {
    $loginInput = trim($_POST['loginInput']);
    $password   = $_POST['password'];

    try {
        $validation->login($loginInput, $password);
    } catch (Exception $e) {
        header("Location: http://localhost/clinic1/view/login/login.php?error=" . urlencode($e->getMessage()));
        exit();
    }

    $result = $user->findByLogin($loginInput);

    if (!$result) {
        header("Location: http://localhost/clinic1/view/login/login.php?error=" . urlencode("Username does not exist."));
        exit();
    }

    if (!$user->verifyPassword($password, $result['password'])) {
        header("Location: http://localhost/clinic1/view/login/login.php?error=" . urlencode("Incorrect password."));
        exit();
    }

    $_SESSION['user_id'] = $result['id'];
    $_SESSION['role']    = $result['role'];
    $_SESSION['name']    = $result['first_name'] . ' ' . $result['last_name'];
    $_SESSION['photo']   = $result['profile_photo'];

    if ($result['role'] === 'admin') {
        header("Location: http://localhost/clinic1/view/admin/admin_dashboard.php");
    } elseif ($result['role'] === 'doctor') {
        header("Location: http://localhost/clinic1/view/doctor/doctor_dashboard.php");
    } else {
        header("Location: http://localhost/clinic1/view/patient/patient_dashboard.php");
    }
    exit();
}

header("Location: http://localhost/clinic1/view/login/login.php");
exit();
?>
