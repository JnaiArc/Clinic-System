<?php
require_once 'c:/xampp/htdocs/clinic1/config/Database.php';
require_once 'c:/xampp/htdocs/clinic1/config/Validation.php';
require_once 'c:/xampp/htdocs/clinic1/model/User.php';

$database = new Database();
$conn = $database->connect();
$user = new User($conn);
$validation = new Validation();

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    
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
