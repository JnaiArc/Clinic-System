<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic Login</title>
    <link rel="stylesheet" href="../css/login.css">
    <style>
        #loginPw::-ms-reveal,
        #loginPw::-ms-clear{
            display: none;
        }
    </style>
</head>

<body>

<div class="login-box">

    <div class="login-header">
        <h2>CLINIC LOGIN</h2>
    </div>

    <form action="http://localhost/clinic1/controller/LoginController.php" method="POST">

        <div class="form-group">
            <label>User Type</label>
            <select name="userType" id="userType" required onchange="changeLoginField()">
                <option value="" disabled selected>Select User Type</option>
                <option value="admin">Admin</option>
                <option value="doctor">Doctor</option>
            </select>
        </div>

        <div class="form-group">
            <label id="loginLabel">Username</label>
            <input type="text" name="loginInput" id="loginInput" required placeholder="Enter Username">
        </div>

        <div class="form-group">
            <label>Password</label>
            <div class="pw-wrap">
                <input type="password" name="password" id="loginPw" required>
                <button type="button" class="eye-btn" onclick="togglePw('loginPw',this)" title="Show/Hide">&#128065;</button>
            </div>
        </div>

        <div class="forgot-password">
            <a href="http://localhost/clinic1/view/login/forgotPassword.php">
                Forgot Password?
            </a>
        </div>

        <div id="errorMsg" class="error">
            <?php 
            if(isset($_GET['error'])) { 
                echo htmlspecialchars($_GET['error']); 
            } 
            ?>
        </div>

        <button class="login-btn" type="submit" name="loginBtn">LOGIN</button>

        <a href="http://localhost/clinic1/view/login/register.php" class="signup-btn">SIGN UP</a>

    </form>

</div>

<script src="../js/login.js"></script>

</body>
</html>