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
$otpModel = new PasswordReset($conn); // reused for registration email verification too (same email+otp_code+expiry mechanism)
$validation = new Validation();

// REGISTER PATIENT (public sign up - always creates role = patient)
if ($_SERVER['REQUEST_METHOD'] === 'POST'){

    // STEP 1: VALIDATE DETAILS + SEND EMAIL VERIFICATION CODE
    if (isset($_POST['sendCodeBtn'])){
        $first_name       = trim($_POST['first_name']);
        $last_name        = trim($_POST['last_name']);
        $email            = trim($_POST['email']);
        $username         = trim($_POST['username']);
        $password         = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        try {
            $validation->patientRegister($first_name, $last_name, $email, $username, $password, $confirm_password);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: http://localhost/clinic1/view/login/register.php");
            exit();
        }

        if ($user->usernameExists($username)){
            $_SESSION['error'] = "Username already exists.\nPlease choose another username.";
            header("Location: http://localhost/clinic1/view/login/register.php");
            exit();
        }

        if ($user->emailExists($email)){
            $_SESSION['error'] = "Email is already registered.";
            header("Location: http://localhost/clinic1/view/login/register.php");
            exit();
        }

        // Hold the registration details in the session until the email is verified
        $_SESSION['pending_registration'] = [
            'first_name' => $first_name,
            'last_name'  => $last_name,
            'email'      => $email,
            'username'   => $username,
            'password'   => $password,
        ];

        $otp_code = $otpModel->createOtp($email);
        $sent = Mailer::sendOtp($email, $otp_code, 'registration');

        if (!$sent) {
            $_SESSION['error'] = "Failed to send verification code. Please try again later.";
            unset($_SESSION['pending_registration']);
            header("Location: http://localhost/clinic1/view/login/register.php");
            exit();
        }

        header("Location: http://localhost/clinic1/view/login/register.php?step=2");
        exit();
    }

    // STEP 1b: RESEND CODE (uses the details already held in session)
    if (isset($_POST['resendCodeBtn'])){
        $pending = $_SESSION['pending_registration'] ?? null;

        if (empty($pending)){
            $_SESSION['error'] = "Session expired. Please start over.";
            header("Location: http://localhost/clinic1/view/login/register.php");
            exit();
        }

        $otp_code = $otpModel->createOtp($pending['email']);
        $sent = Mailer::sendOtp($pending['email'], $otp_code, 'registration');

        if (!$sent) {
            $_SESSION['error'] = "Failed to resend verification code. Please try again later.";
        }

        header("Location: http://localhost/clinic1/view/login/register.php?step=2");
        exit();
    }

    // STEP 2: VERIFY CODE + CREATE THE ACCOUNT
    if (isset($_POST['verifyCodeBtn'])){
        $pending  = $_SESSION['pending_registration'] ?? null;
        $otp_code = trim($_POST['otp_code']);

        if (empty($pending)){
            $_SESSION['error'] = "Session expired. Please start over.";
            header("Location: http://localhost/clinic1/view/login/register.php");
            exit();
        }

        if (empty($otp_code)){
            $_SESSION['error'] = "Verification code is required.";
            header("Location: http://localhost/clinic1/view/login/register.php?step=2");
            exit();
        }

        $status = $otpModel->verifyOtp($pending['email'], $otp_code);

        if ($status === "expired"){
            $_SESSION['error'] = "Verification code has expired. Please request a new one.";
            header("Location: http://localhost/clinic1/view/login/register.php?step=2");
            exit();
        }

        if ($status === "invalid"){
            $_SESSION['error'] = "Invalid verification code.";
            header("Location: http://localhost/clinic1/view/login/register.php?step=2");
            exit();
        }

        // Re-check for race conditions (someone else grabbed the username/email while this one waited on the code)
        if ($user->usernameExists($pending['username'])){
            $_SESSION['error'] = "Username already exists.\nPlease choose another username.";
            unset($_SESSION['pending_registration']);
            header("Location: http://localhost/clinic1/view/login/register.php");
            exit();
        }
        if ($user->emailExists($pending['email'])){
            $_SESSION['error'] = "Email is already registered.";
            unset($_SESSION['pending_registration']);
            header("Location: http://localhost/clinic1/view/login/register.php");
            exit();
        }

        if ($user->registerUser('patient', $pending['first_name'], $pending['last_name'], $pending['email'], $pending['username'], array(), $pending['password'])){
            $otpModel->deleteByEmail($pending['email']);
            unset($_SESSION['pending_registration']);
            $_SESSION['success'] = "Email verified! Registered successfully. Please login.";
            header("Location: http://localhost/clinic1/view/login/login.php");
            exit();
        } else {
            $_SESSION['error'] = "Registration failed. Please try again.";
            header("Location: http://localhost/clinic1/view/login/register.php?step=2");
            exit();
        }
    }

}
?>