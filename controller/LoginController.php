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
    $userType   = trim($_POST['userType']);
    $loginInput = trim($_POST['loginInput']);
    $password   = $_POST['password'];

    try {
        $validation->login($loginInput, $password);
    } catch (Exception $e) {
        header("Location: http://localhost/clinic1/view/login/login.php?error=" . urlencode($e->getMessage()));
        exit();
    }

    $result = $user->loginUser($userType, $loginInput, $password);

    if ($result) {
        $_SESSION['user_id'] = $result['id'];
        $_SESSION['role']    = $result['role'];
        $_SESSION['name']    = $result['first_name'] . ' ' . $result['last_name'];
        $_SESSION['photo']   = $result['profile_photo'];

        if ($userType === 'admin') {
            header("Location: http://localhost/clinic1/view/admin/admin_dashboard.php");
        } else {
            header("Location: http://localhost/clinic1/view/doctor/doctor_dashboard.php");
        }
        exit();
    } else {
        header("Location: http://localhost/clinic1/view/login/login.php?error=" . urlencode("Invalid credentials."));
        exit();
    }
}

header("Location: http://localhost/clinic1/view/login/login.php");
exit();
?>
