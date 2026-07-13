<?php
session_start();
require_once 'c:/xampp/htdocs/clinic1/config/Database.php';
require_once 'c:/xampp/htdocs/clinic1/config/Validation.php';
require_once 'c:/xampp/htdocs/clinic1/model/User.php';
require_once 'c:/xampp/htdocs/clinic1/model/PasswordReset.php';
require_once 'c:/xampp/htdocs/clinic1/config/Mailer.php';

$database = new Database();
$conn = $database->connect();
$user = new User($conn);
$passwordReset = new PasswordReset($conn);
$validation = new Validation();

if ($_SERVER['REQUEST_METHOD'] === 'POST'){

    // STEP 1: SEND CODE
    if (isset($_POST['sendCodeBtn'])){
        $email = trim($_POST['email']);

        try {
            $validation->forgotPasswordEmail($email);
        } catch (Exception $e) {
            header("Location: http://localhost/clinic1/view/login/forgotPassword.php?step=1&error=" . urlencode($e->getMessage()));
            exit();
        }

        if (!$user->getUserByEmail($email)){
            header("Location: http://localhost/clinic1/view/login/forgotPassword.php?step=1&error=" . urlencode("Email address does not exist."));
            exit();
        }

        $otp_code = $passwordReset->createOtp($email);

        // Send the code to the user's email via PHPMailer
        $sent = Mailer::sendOtp($email, $otp_code);

        if (!$sent) {
            header("Location: http://localhost/clinic1/view/login/forgotPassword.php?step=1&error=" . urlencode("Failed to send verification code. Please try again later."));
            exit();
        }

        $_SESSION['reset_email'] = $email;
        header("Location: http://localhost/clinic1/view/login/forgotPassword.php?step=2&success=" . urlencode("Verification code has been sent to your email."));
        exit();
    }

    // STEP 2: VERIFY CODE
    if (isset($_POST['verifyCodeBtn'])){
        $email    = $_SESSION['reset_email'] ?? '';
        $otp_code = trim($_POST['otp_code']);

        try {
            $validation->forgotPasswordOtp($otp_code);
        } catch (Exception $e) {
            header("Location: http://localhost/clinic1/view/login/forgotPassword.php?step=2&error=" . urlencode($e->getMessage()));
            exit();
        }

        if (empty($email)){
            header("Location: http://localhost/clinic1/view/login/forgotPassword.php?step=1&error=" . urlencode("Session expired. Please start again."));
            exit();
        }

        $status = $passwordReset->verifyOtp($email, $otp_code);

        if ($status === "expired"){
            header("Location: http://localhost/clinic1/view/login/forgotPassword.php?step=2&error=" . urlencode("Verification code has expired."));
            exit();
        }

        if ($status === "invalid"){
            header("Location: http://localhost/clinic1/view/login/forgotPassword.php?step=2&error=" . urlencode("Invalid verification code."));
            exit();
        }

        $_SESSION['reset_verified'] = true;
        header("Location: http://localhost/clinic1/view/login/forgotPassword.php?step=3");
        exit();
    }

    // STEP 3: CHANGE PASSWORD
    if (isset($_POST['changePasswordBtn'])){
        $email            = $_SESSION['reset_email'] ?? '';
        $newPassword      = $_POST['newPassword'];
        $confirmPassword  = $_POST['confirmPassword'];

        if (empty($email) || empty($_SESSION['reset_verified'])){
            header("Location: http://localhost/clinic1/view/login/forgotPassword.php?step=1&error=" . urlencode("Session expired. Please start again."));
            exit();
        }

        try {
            $validation->forgotPasswordReset($newPassword, $confirmPassword);
        } catch (Exception $e) {
            header("Location: http://localhost/clinic1/view/login/forgotPassword.php?step=3&error=" . urlencode($e->getMessage()));
            exit();
        }

        $user->updatePasswordByEmail($email, $newPassword);
        $passwordReset->deleteByEmail($email);

        unset($_SESSION['reset_email']);
        unset($_SESSION['reset_verified']);

        header("Location: http://localhost/clinic1/view/login/login.php?success=" . urlencode("Password successfully changed."));
        exit();
    }

}

header("Location: http://localhost/clinic1/view/login/forgotPassword.php");
exit();
?>
