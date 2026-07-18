<?php
require_once 'c:/xampp/htdocs/clinic1/config/Database.php';

class User {
    public $id;
    public $role;
    public $first_name;
    public $last_name;
    public $email;
    public $username;
    public $license_number;
    public $schedule_days;
    public $schedule_time_start;
    public $schedule_time_end;
    public $profile_photo;
    public $password;

    private $conn;
    
    function __construct($db){
        $this->conn = $db;
    }

    // === USERS
    // === CRUD OPERATIONS

    // CREATE/REGISTER USER
    function registerUser($role, $first_name, $last_name, $email, $username, $license_number, $schedule_days, $schedule_time_start, $schedule_time_end, $profile_photo, $password){
        
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $days_string = is_array($schedule_days) ? implode(",", $schedule_days) : "";

        $photo_name = "";
        if($profile_photo && $profile_photo['name'] != ""){
            $photo_name = time() . "_" . basename($profile_photo['name']);
            $target_dir = "c:/xampp/htdocs/clinic1/uploads/";
            $target_file = $target_dir . $photo_name;
            move_uploaded_file($profile_photo['tmp_name'], $target_file);
        }

        $query = "INSERT INTO users (role, first_name, last_name, email, username, license_number, schedule_days, schedule_time_start, schedule_time_end, profile_photo, password) 
                  VALUES (:role, :first_name, :last_name, :email, :username, :license_number, :schedule_days, :schedule_time_start, :schedule_time_end, :profile_photo, :password)";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":role", $role);
        $stmt->bindParam(":first_name", $first_name);
        $stmt->bindParam(":last_name", $last_name);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":license_number", $license_number);
        $stmt->bindParam(":schedule_days", $days_string);
        $stmt->bindParam(":schedule_time_start", $schedule_time_start);
        $stmt->bindParam(":schedule_time_end", $schedule_time_end);
        $stmt->bindParam(":profile_photo", $photo_name);
        $stmt->bindParam(":password", $hashed_password);

        return $stmt->execute();
    }

    // GET ALL USERS(READ)
    function getAllUsers(){
        $query = "SELECT * FROM users ORDER BY role, first_name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // GET USER BY ID
    function getUserById($id){
        $query = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // UPDATE USER
    function updateUser($id, $role, $first_name, $last_name, $email, $username, $license_number, $schedule_days, $schedule_time_start, $schedule_time_end){
        $query = "UPDATE users SET role = :role, first_name = :first_name, last_name = :last_name, email = :email, username = :username, license_number = :license_number, schedule_days = :schedule_days, schedule_time_start = :schedule_time_start, schedule_time_end = :schedule_time_end WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":role", $role);
        $stmt->bindParam(":first_name", $first_name);
        $stmt->bindParam(":last_name", $last_name);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":license_number", $license_number);
        $stmt->bindParam(":schedule_days", $schedule_days);
        $stmt->bindParam(":schedule_time_start", $schedule_time_start);
        $stmt->bindParam(":schedule_time_end", $schedule_time_end);

        return $stmt->execute();
    }

     // DELETE USER
    function deleteUser($id){
        $query = "DELETE FROM users WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    
    // CHECK IF USERNAME EXISTS (optionally excluding a specific user id, e.g. the user's own record when self-editing)
    function usernameExists($username, $exclude_id = null){
        if(empty($username)) return false;
        $query = "SELECT id FROM users WHERE username = :username" . ($exclude_id ? " AND id != :exclude_id" : "");
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        if ($exclude_id) $stmt->bindParam(":exclude_id", $exclude_id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    // CHECK IF EMAIL EXISTS (optionally excluding a specific user id)
    function emailExists($email, $exclude_id = null){
        if(empty($email)) return false;
        $query = "SELECT id FROM users WHERE email = :email" . ($exclude_id ? " AND id != :exclude_id" : "");
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        if ($exclude_id) $stmt->bindParam(":exclude_id", $exclude_id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    // CHECK IF LICENSE NUMBER EXISTS
    function licenseExists($license_number){
        if(empty($license_number)) return false;
        $query = "SELECT id FROM users WHERE license_number = :license_number";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":license_number", $license_number);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    // FIND USER BY USERNAME (admin/patient username, or doctor license number)
    function findByLogin($loginInput){
        $query = "SELECT * FROM users WHERE username = :loginInput OR license_number = :loginInput";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":loginInput", $loginInput);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // VERIFY PASSWORD AGAINST HASH
    function verifyPassword($password, $hashed_password){
        return password_verify($password, $hashed_password);
    }

    // GET USER BY EMAIL (forgot password)
    function getUserByEmail($email){
        $query = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // UPDATE PASSWORD BY EMAIL (forgot password)
    function updatePasswordByEmail($email, $newPassword){
        $hashed_password = password_hash($newPassword, PASSWORD_DEFAULT);
        $query = "UPDATE users SET password = :password WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":password", $hashed_password);
        $stmt->bindParam(":email", $email);
        return $stmt->execute();
    }


    // === MY PROFILE (self-service, used by admin/doctor/patient My Profile pages)

    // UPDATE OWN NAME/USERNAME/EMAIL (does not touch role-specific fields)
    function updateOwnProfile($id, $first_name, $last_name, $email, $username){
        $query = "UPDATE users SET first_name = :first_name, last_name = :last_name, email = :email, username = :username WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":first_name", $first_name);
        $stmt->bindParam(":last_name", $last_name);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":username", $username);
        return $stmt->execute();
    }

    // UPDATE OWN PASSWORD (current password already verified by the caller)
    function updatePasswordById($id, $newPassword){
        $hashed_password = password_hash($newPassword, PASSWORD_DEFAULT);
        $query = "UPDATE users SET password = :password WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":password", $hashed_password);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    // === DOCTORS 

    // GET ALL DOCTORS
    function getAllDoctors(){
        $query = "SELECT * FROM users WHERE role = 'doctor' ORDER BY first_name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // COUNT DOCTORS
    function countDoctors(){
        $query = "SELECT COUNT(*) as total FROM users WHERE role = 'doctor'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
}