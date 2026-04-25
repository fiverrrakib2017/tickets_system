<?php 


include 'include/db_connect.php';

if (isset($_GET['id'])) {
	echo $id=$_GET['id'];

	$result=$con->query("DELETE FROM `internal_tickets` WHERE id='$id'");
	if ($result==true) {
		header("location:internal_tickets.php");
	}else{

	}
}


 ?>