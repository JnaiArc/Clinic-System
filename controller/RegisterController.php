<?php
session_start();

require_once 'c:/xampp/htdocs/clinic1/config/Database.php';
require_once 'c:/xampp/htdocs/clinic1/config/Validation.php';
require_once 'c:/xampp/htdocs/clinic1/model/User.php';

$database = new Database();
$conn = $database->connect();
$user = new User($conn);
$validation = new Validation();

// REGISTER PATIENT (public sign up - always creates role = patient)
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    
    if (isset($_POST['registerBtn'])){
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

        if ($user->registerUser('patient', $first_name, $last_name, $email, $username, array(), $password)){
            $_SESSION['success'] = "Registered successfully! Please login.";
            header("Location: http://localhost/clinic1/view/login/login.php");
            exit();
        } else {
            $_SESSION['error'] = "Registration failed. Please try again.";
            header("Location: http://localhost/clinic1/view/login/register.php");
            exit();
        }
    }
    
}
?>