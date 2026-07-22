<?php
session_start();
$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
if ($step < 1 || $step > 2) $step = 1;
// Guard: can't reach step 2 without having sent a code first
if ($step === 2 && empty($_SESSION['pending_registration'])) $step = 1;

$error = $_SESSION['error'] ?? "";
unset($_SESSION['error']);
$success = $_SESSION['success'] ?? "";
unset($_SESSION['success']);
$pending_email = $_SESSION['pending_registration']['email'] ?? '';
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
        #otpTimer {
            text-align: center;
            font-size: 13px;
            color: #64748b;
            margin-top: 4px;
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

    <?php if($success): ?><div class="success"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>
    <?php if($error): ?><div class="error"><?php echo nl2br(htmlspecialchars($error)); ?></div><?php endif; ?>

    <?php if ($step === 1): ?>
    <!-- STEP 1: REGISTRATION DETAILS -->
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
            <span class="pw-hint">We'll send a verification code to this email.</span>
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

        <button class="login-btn" type="submit" name="sendCodeBtn">SEND VERIFICATION CODE</button>

        <p class="switch-text">Already have an account? <a href="http://localhost/clinic1/view/login/login.php">Sign in Here</a></p>

    </form>

    <?php else: ?>
    <!-- STEP 2: EMAIL OTP VERIFICATION (same look as Forgot Password's verification step) -->
    <form action="http://localhost/clinic1/controller/RegisterController.php" method="POST">
        <div class="form-group">
            <label>Verification Code</label>
            <input type="text" name="otp_code" placeholder="Enter the 6-digit code" required>
        </div>
        <div id="otpTimer">Code expires in <span id="otpCountdown">05:00</span></div>
        <button type="submit" name="verifyCodeBtn" class="login-btn">VERIFY</button>
        <button type="submit" name="resendCodeBtn" class="signup-btn" style="border:2px solid #02529c; background:white; cursor:pointer;">RESEND CODE</button>
        <a href="http://localhost/clinic1/view/login/register.php" class="signup-btn">START OVER</a>
    </form>
    <?php endif; ?>

</div>
</div>

<script src="../js/landing.js"></script>
<script src="../js/login.js"></script>
<script src="../js/input-restrictions.js"></script>
<?php if ($step === 2): ?>
<script>
    // Countdown timer showing code expiration (5 minutes)
    (function(){
        var duration = 5 * 60;
        var countdownEl = document.getElementById('otpCountdown');
        var timer = setInterval(function(){
            var minutes = Math.floor(duration / 60);
            var seconds = duration % 60;
            countdownEl.textContent = (minutes < 10 ? '0' : '') + minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
            if (--duration < 0) {
                clearInterval(timer);
                countdownEl.textContent = '00:00';
            }
        }, 1000);
    })();
</script>
<?php endif; ?>

</body>
</html>