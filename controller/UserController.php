<?php
require_once 'c:/xampp/htdocs/clinic1/config/Database.php';
require_once 'c:/xampp/htdocs/clinic1/config/Validation.php';
require_once 'c:/xampp/htdocs/clinic1/model/User.php';

$database = new Database();
$conn = $database->connect();
$user = new User($conn);
$validation = new Validation();

// Redirect target for "My Profile" self-service actions, based on the logged-in user's own role.
function myProfileRedirectUrl($role){
    if ($role === 'admin')  return "http://localhost/clinic1/view/admin/my_profile.php";
    if ($role === 'doctor') return "http://localhost/clinic1/view/doctor/my_profile.php";
    return "http://localhost/clinic1/view/patient/my_profile.php";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST'){

    // MY PROFILE: UPDATE OWN NAME / USERNAME / EMAIL
    if (isset($_POST['updateMyProfile'])){
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header("Location: http://localhost/clinic1/view/login/login.php");
            exit();
        }

        $id         = $_SESSION['user_id']; // never trust a posted id for self-service actions
        $first_name = trim($_POST['first_name']);
        $last_name  = trim($_POST['last_name']);
        $email      = trim($_POST['email']);

        // Doctors don't have a username field on their profile form anymore,
        // so keep their existing username unchanged if it wasn't posted.
        if (isset($_POST['username'])) {
            $username = trim($_POST['username']);
        } else {
            $current_user_data = $user->getUserById($id);
            $username = $current_user_data['username'];
        }

        try {
            $validation->adminEdit($first_name, $last_name, $email, $username);

            if ($user->usernameExists($username, $id)) {
                throw new Exception("That username is already in use.");
            }
            if ($user->emailExists($email, $id)) {
                throw new Exception("That email is already in use.");
            }
        } catch (Exception $e) {
            $_SESSION['profile_error'] = $e->getMessage();
            header("Location: " . myProfileRedirectUrl($_SESSION['role']));
            exit();
        }

        if ($user->updateOwnProfile($id, $first_name, $last_name, $email, $username)){
            $_SESSION['name'] = $first_name . ' ' . $last_name;
            $_SESSION['profile_success'] = "Your profile has been updated successfully!";
        } else {
            $_SESSION['profile_error'] = "Failed to update profile.";
        }
        header("Location: " . myProfileRedirectUrl($_SESSION['role']));
        exit();
    }

    // MY PROFILE: CHANGE PASSWORD
    if (isset($_POST['changeMyPassword'])){
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header("Location: http://localhost/clinic1/view/login/login.php");
            exit();
        }

        $id              = $_SESSION['user_id'];
        $current_password = $_POST['current_password'];
        $new_password      = $_POST['new_password'];
        $confirm_password  = $_POST['confirm_password'];

        try {
            $validation->forgotPasswordReset($new_password, $confirm_password);

            $current_user = $user->getUserById($id);
            if (!$current_user || !$user->verifyPassword($current_password, $current_user['password'])) {
                throw new Exception("Your current password is incorrect.");
            }
        } catch (Exception $e) {
            $_SESSION['password_error'] = $e->getMessage();
            header("Location: " . myProfileRedirectUrl($_SESSION['role']));
            exit();
        }

        if ($user->updatePasswordById($id, $new_password)){
            $_SESSION['password_success'] = "Your password has been changed successfully!";
        } else {
            $_SESSION['password_error'] = "Failed to change password.";
        }
        header("Location: " . myProfileRedirectUrl($_SESSION['role']));
        exit();
    }

    // UPDATE USER
    if (isset($_POST['updateUser'])){
        $id                  = $_POST['id'];
        $role                = $_POST['role'];
        $first_name          = trim($_POST['first_name']);
        $last_name           = trim($_POST['last_name']);
        $email               = trim($_POST['email']);
        $username            = !empty($_POST['username'])         ? trim($_POST['username'])         : "";
        $license_number      = !empty($_POST['license_number'])   ? trim($_POST['license_number'])   : "";
        $schedule_days       = !empty($_POST['schedule_days'])    ? $_POST['schedule_days']           : "";
        $schedule_time_start = !empty($_POST['schedule_time_start']) ? $_POST['schedule_time_start'] : "";
        $schedule_time_end   = !empty($_POST['schedule_time_end'])   ? $_POST['schedule_time_end']   : "";

        try {
            if ($role === 'admin') {
                $validation->adminEdit($first_name, $last_name, $email, $username);
            } else {
                $validation->doctorEdit($first_name, $last_name, $email, $license_number);
            }
        } catch (Exception $e) {
            session_start();
            $_SESSION['error'] = $e->getMessage();
            header("Location: http://localhost/clinic1/view/admin/admin_staff.php?edit=" . $id);
            exit();
        }

        if ($user->updateUser($id, $role, $first_name, $last_name, $email, $username, $license_number, $schedule_days, $schedule_time_start, $schedule_time_end)){
            header("Location: http://localhost/clinic1/view/admin/admin_staff.php");
            exit();
        }
    }
    
    // DELETE USER
    if (isset($_POST['deleteUser'])){
        $id = $_POST['id'];
        $user->deleteUser($id);
        header("Location: http://localhost/clinic1/view/admin/admin_staff.php");
        exit();
    }
    
}
?>
