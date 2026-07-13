<?php
session_start();
$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
if ($step < 1 || $step > 3) $step = 1;
// Guard: can't reach step 2/3 without going through the prior step
if ($step === 2 && empty($_SESSION['reset_email'])) $step = 1;
if ($step === 3 && empty($_SESSION['reset_verified'])) $step = 1;

$error = isset($_GET['error']) ? $_GET['error'] : '';
$success = isset($_GET['success']) ? $_GET['success'] : '';
?>
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
        #otpTimer {
            text-align: center;
            font-size: 13px;
            color: #64748b;
            margin-top: 4px;
        }
    </style>
</head>
<body>
<div class="login-box">
    <div class="login-header"><h2>FORGOT PASSWORD</h2></div>

    <?php if(!empty($error)): ?><div class="error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <?php if(!empty($success)): ?><div class="success"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>

    <?php if ($step === 1): ?>
    <!-- STEP 1: EMAIL -->
    <form action="http://localhost/clinic1/controller/forgotPasswordController.php" method="POST">
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" placeholder="Enter your registered email" required>
        </div>
        <button type="submit" name="sendCodeBtn" class="login-btn">SEND CODE</button>
        <a href="http://localhost/clinic1/view/login/login.php" class="signup-btn">BACK TO LOGIN</a>
    </form>

    <?php elseif ($step === 2): ?>
    <!-- STEP 2: VERIFICATION CODE -->
    <form action="http://localhost/clinic1/controller/forgotPasswordController.php" method="POST">
        <div class="form-group">
            <label>Verification Code</label>
            <input type="text" name="otp_code" placeholder="Enter the 6-digit code" required>
        </div>
        <div id="otpTimer">Code expires in <span id="otpCountdown">05:00</span></div>
        <button type="submit" name="verifyCodeBtn" class="login-btn">VERIFY</button>
        <a href="http://localhost/clinic1/view/login/forgotPassword.php" class="signup-btn">START OVER</a>
    </form>

    <?php else: ?>
    <!-- STEP 3: NEW PASSWORD -->
    <form action="http://localhost/clinic1/controller/forgotPasswordController.php" method="POST">
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
            </div>
        </div>
        <button type="submit" name="changePasswordBtn" class="login-btn">CHANGE PASSWORD</button>
    </form>
    <?php endif; ?>

</div>
<script src="../js/login.js"></script>
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
