<?php
require_once __DIR__ . '/../PHPMailer/Exception.php';
require_once __DIR__ . '/../PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer {

    const GMAIL_ADDRESS      = "clinic.swiftcare@gmail.com";  
    const GMAIL_APP_PASSWORD = "bhqp knjz cskx cnxa";        
    const SENDER_NAME        = "SwiftCare Clinic";

    // SEND OTP CODE TO A USER'S EMAIL. Returns true on success, false on failure.
    public static function sendOtp($toEmail, $otp_code){
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = self::GMAIL_ADDRESS;
            $mail->Password   = self::GMAIL_APP_PASSWORD;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom(self::GMAIL_ADDRESS, self::SENDER_NAME);
            $mail->addAddress($toEmail);

            $mail->isHTML(false);
            $mail->Subject = 'SwiftCare Clinic - Password Reset Code';
            $mail->Body    = "Your verification code is: " . $otp_code . "\r\nThis code will expire in 5 minutes.\r\n\r\nIf you did not request this, you can ignore this email.";

            $mail->send();
            return true;

        } catch (Exception $e) {
            // error reason(for debug)
            error_log("Mailer::sendOtp failed for {$toEmail}: " . $mail->ErrorInfo);
            return false;
        }
    }
}
?>
