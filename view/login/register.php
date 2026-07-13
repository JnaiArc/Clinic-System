<?php
session_start();
$error = $_SESSION['error'] ?? "";
unset($_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Clinic Register</title>
    <link rel="stylesheet" href="../css/login.css">
    <style>
        #regPw::-ms-reveal,
        #regPw::-ms-clear, 
        #regPw2::-ms-reveal,
        #regPw2::-ms-clear{
            display: none;
        }
    </style>
</head>

<body>

<div class="login-box">

    <div class="login-header">
        <h2>CLINIC REGISTER</h2>
    </div>

    <?php if($error): ?>
    <div class="error-box"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="http://localhost/clinic1/controller/RegisterController.php" method="POST" enctype="multipart/form-data">

        <!-- role -->
        <div class="form-group">
            <label>Role <span class="req">*</span></label>
            <select name="role" id="role" required onchange="showFields()">
                <option value="" disabled selected>Select Role</option>
                <option value="admin">Admin</option>
                <option value="doctor">Doctor</option>
            </select>
        </div>

        <!-- name -->
        <div class="form-group">
            <label>First Name <span class="req">*</span></label>
            <input type="text" name="first_name" required>
        </div>

        <div class="form-group">
            <label>Last Name <span class="req">*</span></label>
            <input type="text" name="last_name" required>
        </div>

        <!-- email -->
        <div class="form-group">
            <label>Email <span class="req">*</span></label>
            <input type="email" name="email" required placeholder="example@email.com">
        </div>

        <!-- admin role-->
        <div id="adminFields" style="display: none;">
            <div class="form-group">
                <label>Username <span class="req">*</span></label>
                <input type="text" name="username" id="usernameInput">
            </div>
        </div>

        <!-- doctor role -->
        <div id="doctorFields" style="display: none;">

            <div class="form-group">
                <label>License Number <span class="req">*</span></label>
                <input type="text" name="license_number" id="licenseInput">
            </div>

            <div class="form-group">
                <label>Schedule Days <span class="req">*</span></label>
                <small style="color: #666; font-size: 11px;">Select available days</small>
                <div class="checkbox-group">
                    <label><input type="checkbox" name="schedule_days[]" value="Monday"> Monday</label>
                    <label><input type="checkbox" name="schedule_days[]" value="Tuesday"> Tuesday</label>
                    <label><input type="checkbox" name="schedule_days[]" value="Wednesday"> Wednesday</label>
                    <label><input type="checkbox" name="schedule_days[]" value="Thursday"> Thursday</label>
                    <label><input type="checkbox" name="schedule_days[]" value="Friday"> Friday</label>
                    <label><input type="checkbox" name="schedule_days[]" value="Saturday"> Saturday</label>
                    <label><input type="checkbox" name="schedule_days[]" value="Sunday"> Sunday</label>
                </div>
            </div>

            <div class="form-group">
                <label>Start Time <span class="req">*</span></label>
                <select name="schedule_time_start" id="timeStart">
                    <option value="">Select Start Time</option>
                    <option value="8:00 AM">8:00 AM</option>
                    <option value="9:00 AM">9:00 AM</option>
                    <option value="10:00 AM">10:00 AM</option>
                    <option value="11:00 AM">11:00 AM</option>
                    <option value="12:00 PM">12:00 PM</option>
                    <option value="1:00 PM">1:00 PM</option>
                    <option value="2:00 PM">2:00 PM</option>
                </select>
            </div>

            <div class="form-group">
                <label>End Time <span class="req">*</span></label>
                <select name="schedule_time_end" id="timeEnd">
                    <option value="">Select End Time</option>
                    <option value="12:00 PM">12:00 PM</option>
                    <option value="1:00 PM">1:00 PM</option>
                    <option value="2:00 PM">2:00 PM</option>
                    <option value="3:00 PM">3:00 PM</option>
                    <option value="4:00 PM">4:00 PM</option>
                    <option value="5:00 PM">5:00 PM</option>
                    <option value="6:00 PM">6:00 PM</option>
                </select>
            </div>

        </div>

        <!-- pass -->
        <div class="form-group">
            <label>Password <span class="req">*</span></label>
            <div class="pw-wrap">
                <input type="password" name="password" id="regPw" required>
                <button type="button" class="eye-btn" onclick="togglePw('regPw',this)" title="Show/Hide">&#128065;</button>
            </div>
            <span class="pw-hint">Must be 6 or more characters</span>
        </div>

        <div class="form-group">
            <label>Confirm Password <span class="req">*</span></label>
            <div class="pw-wrap">
                <input type="password" name="confirm_password" id="regPw2" required>
                <button type="button" class="eye-btn" onclick="togglePw('regPw2',this)" title="Show/Hide">&#128065;</button>
            </div>
        </div>

        <div class="profile-photo">
            <label>Profile Photo (Optional)</label>
            <input type="file" name="profile_photo" accept="image/*">
        </div>

        <button class="login-btn" type="submit" name="registerBtn">REGISTER</button>

        <a href="http://localhost/clinic1/view/login/login.php" class="signup-btn">BACK TO LOGIN</a>

    </form>

</div>

<script src="../js/login.js"></script>

</body>
</html>
