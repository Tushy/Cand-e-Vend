<?php 
	// Connect met de database.
	require_once('../php/config.php');

	// Start de PHP sessies als deze nog niet gestart zijn.
	if((!isset($_SESSION['klant_email'])) || (!isset($_SESSION['klant_wachtwoord']))){
		session_start();
	};

	$email = $_SESSION['klant_email'];
	$password = $_SESSION['klant_wachtwoord'];

	// Haalt het klantnummer op.
	$sql = "SELECT klantnummer FROM klanten WHERE email_adres='$email' AND wachtwoord='$password'";
	$result = mysqli_query($connection, $sql);
	$klantnummer = mysqli_fetch_assoc($result);

	$klantnummer = $klantnummer['klantnummer'];

	// Haalt de bestellingen op.
	$sql = "SELECT * FROM bestellingen WHERE klantnummer='$klantnummer' AND voltooid='0'";
	$result = mysqli_query($connection, $sql);
	$bestellingen = mysqli_fetch_array($result);

	$bestel_nummer = $bestellingen['bestel_nummer'];

	// Haalt elke bestelling apart op.
	$sql = "SELECT * FROM bestelling WHERE bestel_nummer='$bestel_nummer'";
	$bestelling_result = mysqli_query($connection, $sql);

	// Als er POST informatie meegegeven is.
	if((!empty($_POST['bestel_nummer'])) && (!empty($_POST['product_ID']))) {

		// Haalt de bestellingen op afhankelijk van het bestelnummer van het product dat de klant wilt verwijderen.
		$sql = "SELECT klantnummer FROM bestellingen WHERE bestel_nummer='".$_POST['bestel_nummer']."' AND voltooid='0'";
		$result = mysqli_query($connection, $sql);
		$klantnummer_bestelling = mysqli_fetch_assoc($result);

		// Als het klantnummer overeenkomt met het klantnummer op de bestelling.
		if($klantnummer == $klantnummer_bestelling['klantnummer']){

			// Verwijdert het bestelde product.
			$sql = "DELETE FROM bestelling WHERE bestel_nummer='".$_POST['bestel_nummer']."' AND product_ID='".$_POST['product_ID']."'";
			$product_result = mysqli_query($connection, $sql);
			
			// Haalt elke bestelling opnieuw apart op.
			$sql = "SELECT * FROM bestelling WHERE bestel_nummer='$bestel_nummer'";
			$bestelling_result = mysqli_query($connection, $sql);
		};
	};

	// Bestelde producten
	$ordered_products = array();

	if(mysqli_num_rows($bestelling_result)!=0) {
		foreach ($bestelling_result as $bestelling) {

    		// Haalt de product gegevens op.
			$sql = "SELECT * FROM producten WHERE product_ID='".$bestelling["product_ID"]."'";
			$product_result = mysqli_query($connection, $sql);
			$product = mysqli_fetch_array($product_result);

			// Voegt de bestelde producten toe aan de $ordered_products array.
			$ordered_products[$bestelling["product_ID"]] = $product;
		};
	};

	// Sluit de database connectie.
	mysqli_close($connection);
?>

<h2>Bestelling</h2>
<a href="#" onclick="classToggle('bestellingen', false); return false;" class="close"></a>
<p>Hier vind u een overzicht van uw openstaande bestelling.</p>
<div>
	<?php
		// Als de bestelling niet leeg is.
		if (mysqli_num_rows($bestelling_result)!=0) {
		?>	
			<div class=list_header>
				<div>
					<p>Product naam</p>
				</div>
				<div>
					<p>Prijs</p>
				</div>
				<div>
					<p>Aantal</p>
				</div>
				<div>
					<p>Totaal</p>
				</div>
			</div>
			<ul>
				<?php
				// Voor elk product in de bestelling.
				foreach ($bestelling_result as $bestelling) {
					$ordered_product = $ordered_products[$bestelling['product_ID']];
				?>
					<li class='list_item'>
						<div>
							<h3 class="product"><?php echo $ordered_product['product_naam']; ?></h3>
						</div>
						<div>
							<p><?php echo "€ ".number_format($ordered_product["prijs"], 2, '.', ' '); ?></p>
						</div>
						<div>
							<p><?php echo $bestelling['kwantiteit']; ?></p>
						</div>
						<div class='total'>
							<p><?php echo "€ ".number_format($ordered_product['prijs']*$bestelling['kwantiteit'], 2, '.', ' '); ?></p>
						</div>
						<div>
							<button onclick="removeOrder(this, '<?php echo $bestelling['bestel_nummer']; ?>', '<?php echo $bestelling['product_ID']; ?>');" class="btn">Annuleren</button>
						</div>
					</li>
				<?php }; ?>
			</ul>
	<?php
		} else {
			echo '<p>U heeft nog geen producten toegevoegd aan uw bestelling.</p>';
		};
	?>
</div>
<div id="resend_message"></div>
<button onclick="resendQR(this);" class="btn">Qr-code opnieuw sturen</button>