<?php 
	// Connect met de database.
	require_once('../php/config.php');

	// Start de PHP sessies als deze nog niet gestart zijn.
	if((!isset($_SESSION['klant_email'])) || (!isset($_SESSION['klant_wachtwoord']))){
		session_start();
	};

	$email = $_SESSION['klant_email'];
	$password = $_SESSION['klant_wachtwoord'];

	$credits = $_POST['credits'];

	// Voegt credits toe aan het account van de klant.
	$sql = "UPDATE klanten SET credits=credits + $credits WHERE email_adres='$email' AND wachtwoord='$password'";
	$result = mysqli_query($connection, $sql);

	// Haalt de credits opnieuw op om de pagina te updaten.
	$sql = "SELECT credits FROM klanten WHERE email_adres='$email' AND wachtwoord='$password'";
	$result = mysqli_query($connection, $sql);
	$new_credits = mysqli_fetch_assoc($result);

	echo $new_credits['credits'];

	// Sluit de database connectie.
	mysqli_close($connection);
?>