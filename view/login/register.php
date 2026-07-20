<?php
session_start();
$error = $_SESSION['error'] ?? "";
unset($_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Signup</title>
    <link rel="stylesheet" href="../css/landing.css">
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

<!-- NAVBAR -->
<header class="navbar">
    <div class="navbar-inner">
        <a href="../../index.php" class="nav-brand">
            <img src="../../img/logo.png" alt="SwiftCare Clinic logo">
            <span>SwiftCare</span>
        </a>

        <nav class="nav-links">
            <a href="../../index.php#home">Home</a>
            <a href="../../index.php#services">Services</a>
            <a href="../../index.php#about">About</a>
            <a href="../../index.php#contact">Contact</a>
        </nav>

        <div class="nav-actions">
            <a href="login.php" class="btn-login">Login</a>
            <a href="register.php" class="btn-register">Register</a>
        </div>

        <button class="nav-toggle" aria-label="Toggle menu">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>
</header>

<div class="auth-content">
<div class="login-box">

    <div class="login-header">
        <h2>SIGNUP</h2>
    </div>

    <form action="http://localhost/clinic1/controller/RegisterController.php" method="POST">

        <!-- name -->
        <div class="name-row">
            <div class="form-group">
                <label>First Name <span class="req">*</span></label>
                <input type="text" name="first_name" required>
            </div>

            <div class="form-group">
                <label>Last Name <span class="req">*</span></label>
                <input type="text" name="last_name" required>
            </div>
        </div>

        <!-- email -->
        <div class="form-group">
            <label>Email <span class="req">*</span></label>
            <input type="email" name="email" required placeholder="example@email.com">
        </div>

        <!-- username -->
        <div class="form-group">
            <label>Username <span class="req">*</span></label>
            <input type="text" name="username" required>
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
            </div>
        </div>

        <?php if($error): ?>
        <div class="error"><?php echo nl2br(htmlspecialchars($error)); ?></div>
        <?php endif; ?>

        <button class="login-btn" type="submit" name="registerBtn">REGISTER</button>

        <p class="switch-text">Already have an account? <a href="http://localhost/clinic1/view/login/login.php">Sign in Here</a></p>

    </form>

</div>
</div>

<script src="../js/landing.js"></script>
<script src="../js/login.js"></script>

</body>
</html>