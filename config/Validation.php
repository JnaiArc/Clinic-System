<?php

class Validation {

    // REGISTER -------
    private function validateRegisterShared($first_name, $last_name, $email, $password, $confirm_password) {
        if (empty($first_name) || empty($last_name) || empty($email) || empty($password) || empty($confirm_password)) {
            throw new Exception("All required fields must be filled out.");
        }
        if (!preg_match('/^[A-Za-z\s\-]+$/', $first_name)) {
            throw new Exception("First name must contain letters only (no numbers or special characters).");
        }
        if (!preg_match('/^[A-Za-z\s\-]+$/', $last_name)) {
            throw new Exception("Last name must contain letters only (no numbers or special characters).");
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Please enter a valid email address.");
        }
        if (strlen($password) < 6) {
            throw new Exception("Password must be at least 6 characters.");
        }
        if ($password !== $confirm_password) {
            throw new Exception("Passwords do not match.");
        }
    }

    public function adminRegister($first_name, $last_name, $email, $username, $password, $confirm_password) {
        $this->validateRegisterShared($first_name, $last_name, $email, $password, $confirm_password);

        if (empty($username)) {
            throw new Exception("Username is required for admin.");
        }
        if (!preg_match('/^[A-Za-z0-9_]+$/', $username)) {
            throw new Exception("Username must contain only letters, numbers, or underscore.");
        }
        if (strlen($username) < 3 || strlen($username) > 30) {
            throw new Exception("Username must be between 3 and 30 characters.");
        }
    }

    public function patientRegister($first_name, $last_name, $email, $username, $password, $confirm_password) {
        $this->validateRegisterShared($first_name, $last_name, $email, $password, $confirm_password);

        if (empty($username)) {
            throw new Exception("Username is required.");
        }
        if (!preg_match('/^[A-Za-z0-9_]+$/', $username)) {
            throw new Exception("Username must contain only letters, numbers, or underscore.");
        }
        if (strlen($username) < 3 || strlen($username) > 30) {
            throw new Exception("Username must be between 3 and 30 characters.");
        }
    }

    public function doctorRegister($first_name, $last_name, $email, $license_number, $schedule_days, $schedule_time_start, $schedule_time_end, $password, $confirm_password) {
        $this->validateRegisterShared($first_name, $last_name, $email, $password, $confirm_password);

        if (empty($license_number)) {
            throw new Exception("License number is required for doctor.");
        }
        if (!preg_match('/^[A-Za-z0-9\-]+$/', $license_number)) {
            throw new Exception("License number must be alphanumeric (letters, numbers, or dash).");
        }
        if (empty($schedule_days) || !is_array($schedule_days) || count($schedule_days) === 0) {
            throw new Exception("Please select at least one schedule day.");
        }
        if (empty($schedule_time_start)) {
            throw new Exception("Schedule start time is required.");
        }
        if (empty($schedule_time_end)) {
            throw new Exception("Schedule end time is required.");
        }
    }

    // LOGIN --------------------
    public function login($loginInput, $password) {
        if (empty($loginInput) || empty($password)) {
            throw new Exception("All fields are required.");
        }
    }

    // FORGOT PASSWORD ------------------------
    public function forgotPasswordEmail($email) {
        if (empty($email)) {
            throw new Exception("Email address is required.");
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Please enter a valid email address.");
        }
    }

    public function forgotPasswordOtp($otp_code) {
        if (empty($otp_code)) {
            throw new Exception("Verification code is required.");
        }
    }

    public function forgotPasswordReset($newPassword, $confirmPassword) {
        if (empty($newPassword) || empty($confirmPassword)) {
            throw new Exception("All fields are required.");
        }
        if (strlen($newPassword) < 6) {
            throw new Exception("New password must be at least 6 characters.");
        }
        if ($newPassword !== $confirmPassword) {
            throw new Exception("Passwords do not match.");
        }
    }

    // PATIENT -------------
    public function patient($first_name, $last_name, $gender, $birthdate, $phone, $email, $address) {
        if (empty($first_name) || empty($last_name) || empty($gender) || empty($birthdate) || empty($phone) || empty($email) || empty($address)) {
            throw new Exception("All required patient fields must be filled out.");
        }
        if (!preg_match('/^[A-Za-z\s\-]+$/', $first_name)) {
            throw new Exception("Patient first name must contain letters only.");
        }
        if (!preg_match('/^[A-Za-z\s\-]+$/', $last_name)) {
            throw new Exception("Patient last name must contain letters only.");
        }
        if (!in_array($gender, ['Male', 'Female'])) {
            throw new Exception("Please select a valid gender.");
        }
        if (strtotime($birthdate) > time()) {
            throw new Exception("Birthdate cannot be in the future.");
        }
        if (!preg_match('/^[0-9]{11}$/', $phone)) {
            throw new Exception("Contact number must be exactly 11 digits.");
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Please enter a valid email address.");
        }
    }

    // USER: admin/doctor edit -----------------
    public function adminEdit($first_name, $last_name, $email, $username) {
        if (empty($first_name) || empty($last_name) || empty($email) || empty($username)) {
            throw new Exception("All required fields must be filled out.");
        }
        if (!preg_match('/^[A-Za-z\s\-]+$/', $first_name)) {
            throw new Exception("First name must contain letters only.");
        }
        if (!preg_match('/^[A-Za-z\s\-]+$/', $last_name)) {
            throw new Exception("Last name must contain letters only.");
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Please enter a valid email address.");
        }
        if (!preg_match('/^[A-Za-z0-9_]+$/', $username)) {
            throw new Exception("Username must contain only letters, numbers, or underscore.");
        }
        if (strlen($username) < 3 || strlen($username) > 30) {
            throw new Exception("Username must be between 3 and 30 characters.");
        }
    }

    public function doctorEdit($first_name, $last_name, $email, $license_number) {
        if (empty($first_name) || empty($last_name) || empty($email) || empty($license_number)) {
            throw new Exception("All required fields must be filled out.");
        }
        if (!preg_match('/^[A-Za-z\s\-]+$/', $first_name)) {
            throw new Exception("First name must contain letters only.");
        }
        if (!preg_match('/^[A-Za-z\s\-]+$/', $last_name)) {
            throw new Exception("Last name must contain letters only.");
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Please enter a valid email address.");
        }
        if (!preg_match('/^[A-Za-z0-9\-]+$/', $license_number)) {
            throw new Exception("License number must be alphanumeric (letters, numbers, or dash).");
        }
    }
}
