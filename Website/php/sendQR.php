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

		// Haalt het klantnummer op.
		$sql = "SELECT * FROM klanten WHERE email_adres='$email' AND wachtwoord='$password'";
		$result = mysqli_query($connection, $sql);
		$klant_info = mysqli_fetch_array($result);

		$klantnummer = $klant_info['klantnummer'];
		$klant_credits = $klant_info['credits'];

		// Haalt de bestellingen op.
		$sql = "SELECT * FROM bestellingen WHERE klantnummer='$klantnummer' AND voltooid='0'";
		$bestellingen_result = mysqli_query($connection, $sql);
		$bestellingen = mysqli_fetch_array($bestellingen_result);

		$bestel_nummer = $bestellingen['bestel_nummer'];

		$bestelling_bestaat = array();
		$product_kwantiteit = array();
		$totale_kosten = array();

		// Voor elk product in de winkelmand.
		foreach ($_SESSION['cart'] as $key) {

			$sql = "SELECT product_ID, prijs, voorraad FROM producten WHERE product_code='".$key['product_code']."'";
			$result = mysqli_query($connection, $sql);
			$product_info = mysqli_fetch_array($result);

			$product_ID = $product_info['product_ID'];
			$product_prijs = $product_info['prijs'];
			$product_voorraad = $product_info['voorraad'];

			$sql = "SELECT * FROM bestelling WHERE bestel_nummer='$bestel_nummer' AND product_ID='$product_ID'";
			$bestelling_result = mysqli_query($connection, $sql);

			// Aantal en voorraad naar array.
			$product_kwantiteit[$product_ID] = array('product_voorraad'=>$product_voorraad, 'order_kwantiteit'=>$key['kwantiteit']);

			array_push($totale_kosten, $product_prijs*$key['kwantiteit']);

			// Als een bestelling van het product al bestaat.
			if (mysqli_num_rows($bestelling_result) != 0) {
				array_push($bestelling_bestaat, $key['product_naam']);
			};
		};

		$over = $klant_credits - array_sum($totale_kosten);

		// Als de klant genoeg credits heeft.
		if ($over >= 0) {
			if (count($bestelling_bestaat) > 0) {
				// Bestelling bestaat al.
				echo '<p>U heeft '.implode(", ", $bestelling_bestaat).' al besteld. Als u deze bestelling wilt aanpassen moet u de vorige bestelling annuleren. Dit kunt u doen door naar account -> bestellingen te gaan. Zodra de vorige bestelling geannuleerd is kunt u doorgaan met deze bestelling.</p>';
				exit;
			} else {
				foreach ($product_kwantiteit as $item) {
					if ($item['order_kwantiteit'] <= $item['product_voorraad']) {
						$kwantiteit_check = true;
					} else {
						echo '<p>Dit product heeft er nog maar '.$item['product_voorraad'].' op voorraad.</p>';
						exit;
					};
				};

				if ($kwantiteit_check) {

					// Genereert een 16 karakter unieke code die niet in de database staat.
					$sql = "SELECT QR_code FROM bestellingen WHERE voltooid='0'";
					$codes_result = mysqli_query($connection, $sql);
					$codes = mysqli_fetch_array($codes_result);

					while(true){
						$code = substr(str_shuffle(str_repeat("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789", 16)), 0, 16);

						if (!in_array($code, $codes)){
							break;
						};
					};

					// Controlleert of de code 16 karakters lang is.
					if (strlen($code) == 16) {

						// Als de klant al een bestelling heeft geplaatst.
						if (mysqli_num_rows($bestellingen_result) == 1) {
							$next_increment = $bestellingen['bestel_nummer'];
						} else {

							// Haalt de volgende AUTO INCREMENT op.
							$result = mysqli_query($connection, "SHOW TABLE STATUS LIKE 'bestellingen'");
							$data = mysqli_fetch_assoc($result);
							$next_increment = $data['Auto_increment'];

							// Insert bestelling in de database.
							$sql = "INSERT INTO bestellingen (bestel_nummer, klantnummer, voltooid, QR_code) VALUES ('$next_increment', '$klantnummer', '0', '$code')";
							$result = mysqli_query($connection, $sql) or die(mysql_error());
						};

						// Insert elk bestelde product in de database.
						foreach ($_SESSION['cart'] as $key) {
							$sql = "SELECT product_ID FROM producten WHERE product_code='".$key['product_code']."'";
							$result = mysqli_query($connection, $sql);
							$product_info = mysqli_fetch_assoc($result);

							$product_ID = $product_info['product_ID'];
							$kwantiteit = $key['kwantiteit'];

							// Haalt de bestellingen op.
							$sql = "INSERT INTO bestelling(bestel_nummer, product_ID, kwantiteit) VALUES ('$next_increment', '$product_ID','$kwantiteit')";
							$result = mysqli_query($connection, $sql) or die(mysql_error());
						};

						if (mysqli_num_rows($bestellingen_result) == 1) {
							echo '<p>Uw bestelling is aangepast. U kunt nog steeds de vorige QR-code gebruiken.</p>';
						} else {
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

									<img src="http://nickspc146.146.axc.nl/php/qrcode.php?code='.$code.'" alt="QR_code" style="margin:20px 0;"/>

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

							// Verstuurt de email met de bovenstaande informatie.
							if(mail($email,$subject,$message,$headers)){
								echo '<p>De bestelling is gelukt. U zult een mail ontvangen met een QR-code die u bij het apparaat kan scannen.</p>';
							} else {
								echo '<p>De bestelling is niet gelukt. Probeer het later opnieuw.</p>';						
							};
						};
					} else {
						echo '<p>Het genereren van een code is mislukt</p>';
						exit;
					};
				};
			};
		} else {
			echo '<p>U heeft niet genoeg credits om deze bestelling te voltooien. Als u credits wilt toevoegen moet u naar account > informatie gaan.</p>';
			exit;
		};
	};
?>