<?php 
if(isset($_SESSION)){
    session_destroy();
    header("Location: customer_login.php");
    exit();
}

if(!isset($_SESSION)){
    header("Location: customer_login.php");
    exit();
}




?>