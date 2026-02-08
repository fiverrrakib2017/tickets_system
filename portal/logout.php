<?php
if (!isset($_SESSION)) {
    session_start();
}

if (!empty($_SESSION)) {
    session_unset();  
    session_destroy(); 
}

header("Location: customer_login.php");
exit;
