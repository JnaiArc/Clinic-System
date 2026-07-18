<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: http://localhost/clinic1/view/login/login.php");
    exit();
}

require_once 'c:/xampp/htdocs/clinic1/config/Database.php';
require_once 'c:/xampp/htdocs/clinic1/config/Validation.php';
require_once 'c:/xampp/htdocs/clinic1/model/User.php';

$database = new Database();
$conn = $database->connect();
$user = new User($conn);
$validation = new Validation();

// ADD STAFF (admin or doctor account, created by an admin)
if ($_SERVER['REQUEST_METHOD'] === 'POST'){

    if (isset($_POST['addStaffBtn'])){
        $role             = $_POST['role'];
        $first_name       = trim($_POST['first_name']);
        $last_name        = trim($_POST['last_name']);
        $email            = trim($_POST['email']);
        $password         = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $username         = !empty($_POST['username'])         ? trim($_POST['username'])         : "";
        $license_number   = !empty($_POST['license_number'])   ? trim($_POST['license_number'])   : "";
        $schedule_days    = !empty($_POST['schedule_days'])    ? $_POST['schedule_days']           : array();
        $schedule_time_start = !empty($_POST['schedule_time_start']) ? $_POST['schedule_time_start'] : "";
        $schedule_time_end   = !empty($_POST['schedule_time_end'])   ? $_POST['schedule_time_end']   : "";
        $profile_photo    = !empty($_FILES['profile_photo'])   ? $_FILES['profile_photo']         : array();

        try {
            if ($role === 'admin') {
                $validation->adminRegister($first_name, $last_name, $email, $username, $password, $confirm_password);
            } else {
                $validation->doctorRegister($first_name, $last_name, $email, $license_number, $schedule_days, $schedule_time_start, $schedule_time_end, $password, $confirm_password);
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: http://localhost/clinic1/view/admin/admin_staff.php?add=1");
            exit();
        }

        if ($user->usernameExists($username)){
            $_SESSION['error'] = "Username already exists.\nPlease choose another username.";
            header("Location: http://localhost/clinic1/view/admin/admin_staff.php?add=1");
            exit();
        }

        if ($user->emailExists($email)){
            $_SESSION['error'] = "Email is already registered.";
            header("Location: http://localhost/clinic1/view/admin/admin_staff.php?add=1");
            exit();
        }

        if ($role === 'doctor' && $user->licenseExists($license_number)){
            $_SESSION['error'] = "License number already exists.";
            header("Location: http://localhost/clinic1/view/admin/admin_staff.php?add=1");
            exit();
        }

        if ($user->registerUser($role, $first_name, $last_name, $email, $username, $license_number, $schedule_days, $schedule_time_start, $schedule_time_end, $profile_photo, $password)){
            $_SESSION['success'] = "Staff account created successfully.";
            header("Location: http://localhost/clinic1/view/admin/admin_staff.php");
            exit();
        } else {
            $_SESSION['error'] = "Staff creation failed. Please try again.";
            header("Location: http://localhost/clinic1/view/admin/admin_staff.php?add=1");
            exit();
        }
    }

}
?>
