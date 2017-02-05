
<?php
	// Connect met de database.
	require_once('../php/config.php');
	
	// 0 = False, 1 = True, 2 = Email niet in database.
	$validation = array();

	if (!empty($_POST['password_recovery_email'])) {
		$email = $_POST['password_recovery_email'];

		// Voorkomt SQL injectie.
		$email = stripslashes($email);
		$email = mysqli_real_escape_string($connection, $email);

		// Verwijdert alle illegale karakters.
		$email = filter_var($email, FILTER_SANITIZE_EMAIL);

		// Validate e-mail
		if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
			$validation['password_recovery_email'] = '0';
		} else {

			// Controleert of het emailadres bestaat.
			$sql = "SELECT email_adres FROM klanten WHERE email_adres='$email'";
			$result = mysqli_query($connection, $sql);

			if (mysqli_num_rows($result) == 1) {
				$validation['password_recovery_email'] = '1';
			} else {
				$validation['password_recovery_email'] = '2';
			};
		};

		// Als het email veld correct is.
		if ((!in_array('0', $validation)) && (!in_array('2', $validation))) {

			// Haal de klant informatie op.
			$sql = "SELECT voornaam, achternaam, wachtwoord FROM klanten WHERE email_adres='$email'";
			$result = mysqli_query($connection, $sql);
			$numrows = mysqli_num_rows($result);

			// Verstuurt een email naar klant met een link naar de niet bestaande wachtwoord recovery pagina.
			if ($numrows == 1) {
				$klant_info = mysqli_fetch_array($result);

				$subject = 'Cand-e-vend - Password recovery';
				$message = '
				<html>
				<head>
				  <title>Cand-e-vend - Password recovery</title>
				</head>
				<body>
				  <p>Hallo '.$klant_info["voornaam"].' '.$klant_info["achternaam"].',<br><br>

				  Er is zojuist aangegeven bij ons dat u uw wachtwoord bent vergeten. Dat is geen probleem!<br>
				  Bij deze sturen wij u een link naar de pagina waar u uw wachtwoord kan veranderen.<br><br>

				  <a href="#">nickspc146.146.axc.nl/reset</a><br><br>

				  Met vriendelijke groet,<br>

				  Het Cand-e- Vend team.</p>

				  <p style="font-size: 12px;margin-top:20px;">Dit is een automatisch gegenereerd bericht. U kan reageren op deze mail maar het zal helaas niet gelezen worden.<br> 
				  Mocht u vragen hebben of problemen ondervinden kunt u contact opnemen met onze klantenservice op 0612345678 of een e-mail sturen naar <a href="mailto:contact@nickspc146.146.axc.nl">contact@nickspc146.146.axc.nl</a>.</p>
				  </body>
				</html>
				';

				// De benodigde HTML headers voor de email.
				$headers = "MIME-Version: 1.0" . "\r\n";
				$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

				// Extra header
				$headers .= 'From: <no-reply@nickspc146.146.axc.nl>' . "\r\n";
				
				// Verstuurt de email met de bovenstaande informatie.
				if(mail($email,$subject,$message,$headers)){
					$validation['correct'] = '1';
				} else {
					$validation['correct'] = '0';
				};
			} else {
				$validation['correct'] = '0';
			};
		};
	} else {
		$validation['leeg'] = '0';
	};

	// Sluit de database connectie.
	mysqli_close($connection);

	echo json_encode($validation);
?>