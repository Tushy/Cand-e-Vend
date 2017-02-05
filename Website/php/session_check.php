<?php
	// Connect met de database.
	require_once('../php/config.php');

	// Start alle sessies.
	session_start();

	// Als de onderstaande sessies niet gestart zijn.
	if((!isset($_SESSION['klant_email'])) && (!isset($_SESSION['klant_password']))){
		$bool_value = False;
	} else {
		$bool_value = True;
	};

	echo (int)$bool_value;
?>