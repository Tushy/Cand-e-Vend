<?php
	// Connect met de database.
	require_once('../php/config.php');

	// 0 = False, 1 = True, 2 = Email al in database.
	$validation = array();

	// Als alle velden zijn ingevuld.
	if ((!empty($_POST['voornaam'])) && (!empty($_POST['achternaam'])) && (!empty($_POST['email_adres'])) && (!empty($_POST['wachtwoord'])) && (!empty($_POST['wachtwoord_bevestiging']))) {

		$voornaam = ucfirst($_POST['voornaam']);
		$achternaam = ucfirst($_POST['achternaam']);
		$email = $_POST['email_adres'];
		$wachtwoord = $_POST['wachtwoord'];
		$wachtwoord_bevestiging = $_POST['wachtwoord_bevestiging'];

		// Voorkomt SQL injectie.
		$voornaam = stripslashes($voornaam);
		$achternaam = stripslashes($achternaam);
		$email = stripslashes($email);
		$wachtwoord = stripslashes($wachtwoord);
		$wachtwoord_bevestiging = stripslashes($wachtwoord_bevestiging);

		$voornaam = mysqli_real_escape_string($connection, $voornaam);
		$achternaam = mysqli_real_escape_string($connection, $achternaam);
		$email = mysqli_real_escape_string($connection, $email);
		$wachtwoord= mysqli_real_escape_string($connection, $wachtwoord);
		$wachtwoord_bevestiging = mysqli_real_escape_string($connection, $wachtwoord_bevestiging);

		// Valideert voornaam
		if (ctype_alpha($_POST['voornaam'])) {
			$validation['voornaam'] = '1';
		} else {
			$validation['voornaam'] = '0';
		};

		// Valideert achternaam
		$achternaam_rules = "/(?:[A-Za-z.'-]+\s*){2,3}$/";
		$achternaam = preg_replace('/\s{2,}/', ' ', $_POST['achternaam']);

		if(preg_match($achternaam_rules, $achternaam)) {
			$validation['achternaam'] = '1';
		} else {
			$validation['achternaam'] = '0';
		};

		// Valideert Email
		$email = $_POST['email_adres'];

		// Verwijdert alle illegale karakters.
		$email = filter_var($email, FILTER_SANITIZE_EMAIL);

		// Validate e-mail
		if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
			$validation['email_adres'] = '0';
		} else {
			// Controleert of de email al bestaat.
			$sql = "SELECT email_adres FROM klanten WHERE email_adres='$email'";
			$result = mysqli_query($connection, $sql);

			if (mysqli_num_rows($result) > 0) {
				$validation['email_adres'] = '2';
			} else {
				$validation['email_adres'] = '1';
			};
		};

		// Valideert wachtwoord
		$password_rules = "/^(?=.*\d)(?=.*[@#\-_$%^&+=§!\?])(?=.*[a-z])(?=.*[A-Z])[0-9A-Za-z@#\-_$%^&+=§!\?]{4,20}$/";

		if(preg_match($password_rules, $_POST['wachtwoord'])) {
			$validation['wachtwoord'] = '1';
		} else {
			$validation['wachtwoord'] = '0';
		};

		if (preg_match($password_rules, $_POST['wachtwoord_bevestiging'])) {
			$validation['wachtwoord_bevestiging'] = '1';
		} else {
			$validation['wachtwoord_bevestiging'] = '0';
		};

		if (($_POST['wachtwoord']) == ($_POST['wachtwoord_bevestiging'])) {
			$validation['wachtwoorden_gelijk'] = '1';
		} else {
			$validation['wachtwoorden_gelijk'] = '0';
		};

		// Als alle inputs correct zijn
		if ((!in_array('0', $validation)) && (!in_array('2', $validation))){
			
			// Hash het wachtwoord.
			$wachtwoord = password_hash($wachtwoord, PASSWORD_DEFAULT);

			//Insert de gegevens in de database.
			mysqli_query($connection, "INSERT INTO klanten (voornaam, achternaam, email_adres, wachtwoord)
			VALUES ('$voornaam', '$achternaam', '$email','$wachtwoord')") or die(mysql_error());
			$validation['correct'] = '1';
		};
	} else {
		$validation['leeg'] = '0';
	};

	// Sluit de database connectie.
	mysqli_close($connection);

	echo json_encode($validation);
?>