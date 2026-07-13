<?php
require_once 'c:/xampp/htdocs/clinic1/config/Database.php';

class PasswordReset {
    private $conn;

    function __construct($db){
        $this->conn = $db;
    }

    // CREATE OTP FOR EMAIL (5 minute expiry, only the latest is valid)
    function createOtp($email){
        // invalidate any previous codes for this email
        $this->deleteByEmail($email);

        $otp_code = str_pad(strval(random_int(0, 999999)), 6, "0", STR_PAD_LEFT);

        $query = "INSERT INTO password_resets (email, otp_code, expires_at) 
                  VALUES (:email, :otp_code, DATE_ADD(NOW(), INTERVAL 5 MINUTE))";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":otp_code", $otp_code);
        $stmt->execute();

        return $otp_code;
    }

    // GET LATEST OTP RECORD FOR EMAIL
    function getLatestOtp($email){
        $query = "SELECT * FROM password_resets WHERE email = :email ORDER BY id DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // VERIFY OTP: returns "valid", "expired", or "invalid"
    function verifyOtp($email, $otp_code){
        $record = $this->getLatestOtp($email);

        if(!$record || $record['otp_code'] !== $otp_code){
            return "invalid";
        }

        if(strtotime($record['expires_at']) < time()){
            return "expired";
        }

        return "valid";
    }

    // DELETE/INVALIDATE OTP FOR EMAIL
    function deleteByEmail($email){
        $query = "DELETE FROM password_resets WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        return $stmt->execute();
    }
}
?>
