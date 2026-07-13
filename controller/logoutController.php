<?php
session_start();
session_destroy();

header("Location: http://localhost/clinic1/index.php");
exit();
?>