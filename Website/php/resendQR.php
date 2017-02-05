<?php
	// Connect met de database.
	require_once('../php/config.php');
	
	// Start de PHP sessies als deze nog niet gestart zijn.
	if((!isset($_SESSION['cart'])) || (!isset($_SESSION['klant_email'])) || (!isset($_SESSION['klant_wachtwoord']))){
		session_start();
	};

	if(isset($_SESSION['cart'])){
		$email = $_SESSION['klant_email'];
		$password = $_SESSION['klant_wachtwoord'];

		// Haalt de klant informatie op.
		$sql = "SELECT klantnummer, voornaam, achternaam FROM klanten WHERE email_adres='$email' AND wachtwoord='$password'";
		$result = mysqli_query($connection, $sql);
		$klant_info = mysqli_fetch_array($result);

		$klantnummer = $klant_info['klantnummer'];

		// Selecteert de QR_code van de niet voltooide bestelling.
		$sql = "SELECT QR_code FROM bestellingen WHERE klantnummer='$klantnummer' AND voltooid='0'";
		$result = mysqli_query($connection, $sql);
		$code = mysqli_fetch_assoc($result);

		// Als de resultaten niet leeg zijn.
		if (!empty($code)){
			$subject = 'Cand-e-vend - QR-code';
			$message = '
			<html>
				<head>
					<title>Cand-e-vend - QR-code</title>
				</head>
				<body>
					<p>Hallo '.$klant_info["voornaam"].' '.$klant_info["achternaam"].',<br><br>

					Er is zojuist een bestelling geplaatst bij Cand-e-Vend!<br>
					Om uw bestelling op te halen bij een van onze automaten kunt u onderstaande QR-code voor de camera van het apparaat houden.<br>
					Uw bestelling is terug te vinden op onze website door op “Account” te klikken in de rechter bovenhoek en vervolgens “Bestellingen” te selecteren.<br>
					Er wordt geen tegoed van uw account afgeschreven tot de bestelling is afgerond.</p>

					<img src="http://nickspc146.146.axc.nl/php/qrcode.php?code='.$code['QR_code'].'" alt="QR_code" style="margin:20px 0;"/>

					<p>Met vriendelijke groet,<br>Het Cand-e-Vend team.</p>

					<p style="font-size: 12px;margin-top:20px;">Dit is een automatisch gegenereerd bericht. U kan reageren op deze mail maar het zal helaas niet gelezen worden.<br> 
					Mocht u vragen hebben of problemen ondervinden kunt u contact opnemen met onze klantenservice op 0612345678 of een e-mail sturen naar <a href="mailto:contact@nickspc146.146.axc.nl">contact@nickspc146.146.axc.nl</a></p>
				</body>
			</html>
			';

			// De benodigde HTML headers voor de email.
			$headers = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

			// Extra header
			$headers .= 'From: <no-reply@nickspc146.146.axc.nl>' . "\r\n";

			// Stuurt de email met de QR_code naar de klant.
			if(mail($email,$subject,$message,$headers)){
				echo '<p>De Qr-code is opnieuw verstuurd.</p>';
			} else {
				echo '<p>Het is niet gelukt om de QR-code opnieuw op te sturen.</p>';						
			};
		} else {
			echo '<p>U heeft geen bestellingen.</p>';
		};	
	};

	// Sluit de database connectie.
	mysqli_close($connection);
?>