<?php
session_start();
session_destroy();

header("Location: http://localhost/clinic1/view/login/login.php");
exit();
?>