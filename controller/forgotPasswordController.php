<?php
require_once 'c:/xampp/htdocs/clinic1/config/Database.php';
require_once 'c:/xampp/htdocs/clinic1/config/Validation.php';
require_once 'c:/xampp/htdocs/clinic1/model/User.php';

$database = new Database();
$conn = $database->connect();
$user = new User($conn);
$validation = new Validation();

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    
    $userType        = $_POST['userType'];
    $email           = trim($_POST['email']);
    $loginInput      = trim($_POST['username']);
    $newPassword     = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    try {
        $validation->forgotPassword($email, $loginInput, $newPassword, $confirmPassword);
    } catch (Exception $e) {
        header("Location: http://localhost/clinic1/view/login/forgotPassword.php?error=" . urlencode($e->getMessage()));
        exit();
    }

    $result = $user->resetPassword($userType, $email, $loginInput, $newPassword);

    if ($result){
        header("Location: http://localhost/clinic1/view/login/login.php?success=" . urlencode("Password reset successfully."));
        exit();
    } else {
        header("Location: http://localhost/clinic1/view/login/forgotPassword.php?error=" . urlencode("Invalid user details. Please check your information."));
        exit();
    }
    
}
?>
