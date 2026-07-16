<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../css/landing.css">
    <link rel="stylesheet" href="../css/login.css">
    <style>
        #loginPw::-ms-reveal,
        #loginPw::-ms-clear{
            display: none;
        }
    </style>
</head>

<body>

<!-- NAVBAR -->
<header class="navbar">
    <div class="navbar-inner">
        <a href="../../index.php" class="nav-brand">
            <img src="../../logo.jpg" alt="SwiftCare Clinic logo">
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
        <h2>LOGIN</h2>
    </div>

    <form action="http://localhost/clinic1/controller/LoginController.php" method="POST">

        <div class="form-group">
            <label>Username</label>
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

        <p class="switch-text">Don't have an account? <a href="http://localhost/clinic1/view/login/register.php">Register Here</a></p>

    </form>

</div>
</div>

<script src="../js/landing.js"></script>
<script src="../js/login.js"></script>

</body>
</html>