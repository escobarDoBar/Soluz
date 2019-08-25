<?php
	session_start();
	if (!isset($_SESSION['matricula']))
		header("location:entrar.php");
?>