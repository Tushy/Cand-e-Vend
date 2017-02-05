<?php 
	// Connect met de database.
	require_once('../php/config.php');

	// Start de PHP sessies als deze nog niet gestart zijn.
	if((!isset($_SESSION['klant_email'])) || (!isset($_SESSION['klant_wachtwoord']))){
		session_start();
	};

	$email = $_SESSION['klant_email'];
	$password = $_SESSION['klant_wachtwoord'];

	// Haalt de klant informatie op afhankelijk van de sessie.
	$sql = "SELECT * FROM klanten WHERE email_adres='$email' AND wachtwoord='$password'";
	$result = mysqli_query($connection, $sql);
	$account_info = mysqli_fetch_array($result);

	// Sluit de database connectie.
	mysqli_close($connection);
?>

<h2>Accountinformatie</h2>
<p>Hallo <?php echo $account_info['voornaam'].' '.$account_info['achternaam'];?></p>
<p>Email: <?php echo $account_info['email_adres'];?></p>
<p>Uw account aangemaakt op: <?php echo $account_info['datum_creatie'];?></p>
<div id="credits">
	<p>U heeft momenteel € <span class="credit"><?php echo $account_info['credits'];?></span> op uw account staan.</p>
	<p>Wilt u uw tegoed opwaarderen, selecteer hieronder dan een van de opties en klik op “Voeg tegoed toe”. Volg hierna de stappen op het scherm om af te rekenen.</p>
	<div class="add_credits">
		<select class="credits">
			<option value='1'>€ 1.00</option>
			<option value='5'>€ 5.00</option>
			<option value='10'>€ 10.00</option>
			<option value='20'>€ 20.00</option>
			<option value='50'>€ 50.00</option>
			<option value='100'>€ 100.00</option>
		</select>
		<button onclick="addCredits(this);" class="btn">Voeg tegoed toe</button>
	</div>
</div>

<a href="#" onclick="classToggle('account', false); return false;" class="close"></a>