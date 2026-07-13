<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="../css/login.css">
    <style>
        #newPw::-ms-reveal,
        #newPw::-ms-clear, 
        #confirmPw::-ms-reveal,
        #confirmPw::-ms-clear{
            display: none;
        }
    </style>
</head>
<body>
<div class="login-box">
    <div class="login-header"><h2>FORGOT PASSWORD</h2></div>
    <form action="http://localhost/clinic1/controller/forgotPasswordController.php" method="POST">
        <div class="form-group">
            <label>User Type</label>
            <select name="userType" required>
                <option value="" disabled selected>Select User Type</option>
                <option value="admin">Admin</option>
                <option value="doctor">Doctor</option>
            </select>
        </div>
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" placeholder="Enter your registered email" required>
        </div>
        <div class="form-group">
            <label>Username / License Number</label>
            <input type="text" name="username" placeholder="Enter your username" required>
        </div>
        <div class="form-group">
            <label>New Password</label>
            <div class="pw-wrap">
                <input type="password" name="newPassword" id="newPw" placeholder="Enter new password" required>
                <button type="button" class="eye-btn" onclick="togglePw('newPw',this)" title="Show/Hide">&#128065;</button>
            </div>
            <span class="pw-hint">Must be 6 or more characters</span>
        </div>
        <div class="form-group">
            <label>Confirm Password</label>
            <div class="pw-wrap">
                <input type="password" name="confirmPassword" id="confirmPw" placeholder="Confirm new password" required>
                <button type="button" class="eye-btn" onclick="togglePw('confirmPw',this)" title="Show/Hide">&#128065;</button>
            </div>
        </div>
        <?php
        $error = isset($_GET['error']) ? $_GET['error'] : '';
        $success = isset($_GET['success']) ? $_GET['success'] : '';
        ?>
        <?php if(!empty($error)): ?><div class="error"><?php echo $error; ?></div><?php endif; ?>
        <?php if(!empty($success)): ?><div class="success"><?php echo $success; ?></div><?php endif; ?>
        <button type="submit" class="login-btn">RESET PASSWORD</button>
        <a href="http://localhost/clinic1/view/login/login.php" class="signup-btn">BACK TO LOGIN</a>
    </form>
</div>
<script src="../js/login.js"></script>
</body>
</html>
