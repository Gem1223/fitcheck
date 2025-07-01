<?php
session_start();
session_unset();
session_destroy();
session_start();
$alertType = 'success';
$alertText = urlencode('You have been logged out successfully.');
header("Location: fitness_home.html?alertType=$alertType&alertText=$alertText");
exit;
?>
