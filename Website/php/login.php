<?php
	// Connect met de database.
	require_once('../php/config.php');

	session_start(); // Starting Session

	// 0 = False, 1 = True, 2 = Email niet in database.
	$validation = array();

	// Als beide velden niet leeg zijn.
	if ((!empty($_POST['email_login'])) && (!empty($_POST['wachtwoord_login']))) {
		$email = $_POST['email_login'];
		$password = $_POST['wachtwoord_login'];

		// Voorkomt SQL injectie.
		$email = stripslashes($email);
		$password = stripslashes($password);

		$email = mysqli_real_escape_string($connection, $email);
		$password = mysqli_real_escape_string($connection, $password);

		$email = $_POST['email_login'];

		// Verwijdert alle illegale karakters.
		$email = filter_var($email, FILTER_SANITIZE_EMAIL);

		// Validate e-mail
		if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
			$validation['email_login'] = '0';
		} else {

			// Haalt Alle emails op om te controleren of een email al bestaat.
			$sql = "SELECT email_adres FROM klanten WHERE email_adres='$email'";
			$result = mysqli_query($connection, $sql);

			if (mysqli_num_rows($result) == 1) {
				$validation['email_login'] = '1';
			} else {
				$validation['email_login'] = '2';
			};
		};

		// Valideert wachtwoord
		$password_rules = '/^(?=.*\d)(?=.*[@#\-_$%^&+=§!\?])(?=.*[a-z])(?=.*[A-Z])[0-9A-Za-z@#\-_$%^&+=§!\?]{4,20}$/';

		// Als het wachtwoord voldoet aan de eisen.
		if(preg_match($password_rules, $_POST['wachtwoord_login'])) {

			// Haal het gehashde wachtwoord op.
			$sql = "SELECT wachtwoord FROM klanten WHERE email_adres='$email'";
			$result = mysqli_query($connection, $sql);
			$password_hash = mysqli_fetch_assoc($result);

			$hash = $password_hash['wachtwoord']; 

			// Controleert of deze overeenkomt met het ingevulde wachtwoord.
			if (password_verify($password, $hash)) {
				$validation['wachtwoord_login'] = '1';

				$sql = "SELECT email_adres, wachtwoord FROM klanten WHERE email_adres='$email'";
				$result = mysqli_query($connection, $sql);
				$numrows = mysqli_num_rows($result);

				// Als alle velden kloppen
				if (($numrows == 1) && (!in_array('0', $validation)) && (!in_array('2', $validation))) {
					
					// Initialiseert de email sessie.
					$_SESSION['klant_email'] = $email;
					
					// Initialiseert de wachtwoord sessie.
					$_SESSION['klant_wachtwoord'] = $hash; 
					
					$validation['login'] = '1';
				};
			} else {
				$validation['login'] = '0';
			};
		} else {
			$validation['wachtwoord_login'] = '0';
		};
	} else {
		$validation['leeg'] = '0';
	};
	
	// Sluit de database connectie.
	mysqli_close($connection);

	echo json_encode($validation);
?>