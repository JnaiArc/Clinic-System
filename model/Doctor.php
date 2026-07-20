<?php
require_once 'c:/xampp/htdocs/clinic1/config/Database.php';

// Doctor-only info (specialization, license number, schedule) lives in its own
// `doctors` table, one row per doctor, linked back to `users` via user_id.
// Run migration_doctors_table.sql once against the database before using this model.
class Doctor {
    public $id;
    public $user_id;
    public $specialization;
    public $license_number;
    public $schedule_days;
    public $schedule_time_start;
    public $schedule_time_end;

    private $conn;

    function __construct($db){
        $this->conn = $db;
    }

    // CREATE DOCTOR INFO (called right after User::registerUser() for role='doctor')
    function insertDoctorInfo($user_id, $license_number, $specialization, $schedule_days, $schedule_time_start, $schedule_time_end){
        $days_string = is_array($schedule_days) ? implode(",", $schedule_days) : $schedule_days;

        $query = "INSERT INTO doctors (user_id, license_number, specialization, schedule_days, schedule_time_start, schedule_time_end)
                  VALUES (:user_id, :license_number, :specialization, :schedule_days, :schedule_time_start, :schedule_time_end)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":license_number", $license_number);
        $stmt->bindParam(":specialization", $specialization);
        $stmt->bindParam(":schedule_days", $days_string);
        $stmt->bindParam(":schedule_time_start", $schedule_time_start);
        $stmt->bindParam(":schedule_time_end", $schedule_time_end);
        return $stmt->execute();
    }

    // UPDATE DOCTOR INFO (upsert — creates the row if one doesn't exist yet for this user)
    function updateDoctorInfo($user_id, $license_number, $specialization, $schedule_days, $schedule_time_start, $schedule_time_end){
        $days_string = is_array($schedule_days) ? implode(",", $schedule_days) : $schedule_days;

        if (!$this->doctorInfoExists($user_id)){
            return $this->insertDoctorInfo($user_id, $license_number, $specialization, $days_string, $schedule_time_start, $schedule_time_end);
        }

        $query = "UPDATE doctors SET license_number = :license_number, specialization = :specialization, schedule_days = :schedule_days, schedule_time_start = :schedule_time_start, schedule_time_end = :schedule_time_end WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":license_number", $license_number);
        $stmt->bindParam(":specialization", $specialization);
        $stmt->bindParam(":schedule_days", $days_string);
        $stmt->bindParam(":schedule_time_start", $schedule_time_start);
        $stmt->bindParam(":schedule_time_end", $schedule_time_end);
        return $stmt->execute();
    }

    // CHECK IF A doctors ROW ALREADY EXISTS FOR THIS USER
    function doctorInfoExists($user_id){
        $query = "SELECT id FROM doctors WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    // GET DOCTOR INFO BY USER ID (raw doctors row only)
    function getDoctorInfoByUserId($user_id){
        $query = "SELECT * FROM doctors WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // CHECK IF LICENSE NUMBER EXISTS (optionally excluding a specific user's own row, for edits)
    function licenseExists($license_number, $exclude_user_id = null){
        if(empty($license_number)) return false;
        $query = "SELECT id FROM doctors WHERE license_number = :license_number" . ($exclude_user_id ? " AND user_id != :exclude_user_id" : "");
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":license_number", $license_number);
        if ($exclude_user_id) $stmt->bindParam(":exclude_user_id", $exclude_user_id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    // GET ALL DOCTORS (joined with users for name/email/photo/etc.)
    function getAllDoctors(){
        $query = "SELECT u.*, d.specialization, d.license_number, d.schedule_days, d.schedule_time_start, d.schedule_time_end
                  FROM users u
                  INNER JOIN doctors d ON d.user_id = u.id
                  WHERE u.role = 'doctor' ORDER BY u.first_name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // GET DOCTORS UNDER A SPECIFIC SPECIALIZATION (used by the specialization -> doctor booking step)
    function getDoctorsBySpecialization($specialization){
        $query = "SELECT u.*, d.specialization, d.license_number, d.schedule_days, d.schedule_time_start, d.schedule_time_end
                  FROM users u
                  INNER JOIN doctors d ON d.user_id = u.id
                  WHERE u.role = 'doctor' AND d.specialization = :specialization ORDER BY u.first_name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":specialization", $specialization);
        $stmt->execute();
        return $stmt;
    }

    // GET DISTINCT LIST OF SPECIALIZATIONS THAT ACTUALLY HAVE A DOCTOR ASSIGNED
    // (drives both the "All Doctors" filter buttons and the booking specialization dropdown)
    function getAllSpecializations(){
        $query = "SELECT DISTINCT d.specialization
                  FROM doctors d
                  INNER JOIN users u ON u.id = d.user_id
                  WHERE u.role = 'doctor' AND d.specialization IS NOT NULL AND d.specialization != ''
                  ORDER BY d.specialization ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // COUNT DOCTORS
    function countDoctors(){
        $query = "SELECT COUNT(*) as total FROM users WHERE role = 'doctor'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
}
