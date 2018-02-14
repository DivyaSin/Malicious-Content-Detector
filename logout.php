<?php
	session_start();

	// destroy session and user is redirected to index page
	session_destroy();
	header('Location: index.php');
	exit;
?>