<?php 
	// Connect met de database.
	require_once('../php/config.php');

	// Start de PHP sessies als deze nog niet gestart zijn.
	if((!isset($_SESSION['klant_email'])) || (!isset($_SESSION['klant_wachtwoord']))){
		session_start();
	};

	$email = $_SESSION['klant_email'];
	$password = $_SESSION['klant_wachtwoord'];

	if((isset($_SESSION['klant_email'])) || (isset($_SESSION['klant_wachtwoord']))){

		// Haalt de klant informatie op.
		$sql = "SELECT voornaam, achternaam FROM klanten WHERE email_adres='$email' AND wachtwoord='$password'";
		$result = mysqli_query($connection, $sql);
		$account_info = mysqli_fetch_array($result);

		// Haalt alle producten op.
		$sql = "SELECT * FROM producten";
		$products_query = mysqli_query($connection, $sql);

		$product_info_array = array();
		$voedingswaarden_array = array();

		// Voor elk product worden de producten + product_info + voedingswaarden toegevoegt aan de arrays.
		foreach ($products_query as $product) {

			// Haalt de product_info tabel op.
			$sql = "SELECT * FROM product_info WHERE product_ID='" . $product["product_ID"] . "'";
			$products_info = mysqli_fetch_array(mysqli_query($connection, $sql));

			// Zet de product info in de $product_info_array.
			$product_info_array[$product["product_ID"]] = $products_info;

			// Haalt de voedingswaarden op.
			$sql = "SELECT * FROM voedingswaarden WHERE product_ID='" . $product["product_ID"] . "'";
			$voedingswaarden = mysqli_fetch_array(mysqli_query($connection, $sql));

			// Zet de voedingswaarden in de $voedingswaarden_array.
			$voedingswaarden_array[$product["product_ID"]] = $voedingswaarden;
		};
	};

	// Sluit de database connectie.
	mysqli_close($connection);
?>

<!-- Pagina navigatie !-->
<nav class="clearfix">
	<a href="#" onclick="return false;" class="identity"><h1>Cand-e-vend</h1></a>
	<ul>
		<li>
			<a href="#" onclick="classToggle('checkout', false); return false;">Winkelmand</a>
			<span id="cart_message"></span>
		</li>
		<li>
			<a href="#" onclick="return false;">Account</a>
			<div id="account_dropdown" class="dropdown_content">
				<a href="#" onclick="classToggle('bestellingen', true, true); return false;">Bestellingen</a>
				<a href="#" onclick="classToggle('account', true, false); return false;">Informatie</a>
				<a href="#" onclick="logout(); return false;">Uitloggen</a>
			</div>
		</li>
	</ul>
</nav>

<!-- Account informatie !-->
<section id="account">
	<div class="loader_container">
		<div class="loader">
			<div class="loader_circle"></div>
		</div>
	</div>
	<div class="content"></div>
</section>

<!-- Bestelde producten van de klant. !-->
<section id="bestellingen">
	<div class="loader_container">
		<div class="loader">
			<div class="loader_circle"></div>
		</div>
	</div>
	<div class="content"></div>
</section>

<!-- Header met welkom bericht. !-->
<section id="header">
	<h2>Welkom <?php echo $account_info['voornaam'];?></h2>
</section>

<!-- De lijst met alle producten. !-->
<section id="product_container">
	<ul>
		<li class=list_header>
				<button id="sortProduct" onclick="sort(this.id);" class="sort"><span>Product naam</span></button>

				<button id="sortPrice" onclick="sort(this.id);" class="sort"><span>Prijs</span></button>

				<button id="sortStock" onclick="sort(this.id);" class="sort"><span>Aantal</span></button>
		</li>
		<?php
			// Als er producten zijn.
			if (!empty($products_query)) {
				// Voor elk product wordt de juiste informatie uitgelezen en geplaatst.	
				foreach ($products_query as $product) {

				$products_info = $product_info_array[$product['product_ID']];
				$voedingswaarden = $voedingswaarden_array[$product['product_ID']];
		?>
		<li class=list_item>
			<div>
				<div>
					<h3 class="product"><?php echo $product['product_naam']; ?></h3>
				</div>
				<div>
					<p class='price'>€ <?php echo $product['prijs']; ?></p>
				</div>
				<div class="select_container">
					<select class="stock">
						<?php 
							$i = 0;
							while($i <= $product['voorraad']) { 
								echo '<option value='.$i.'>'.$i.'</option>';
								$i++;
							}; 
						?>
					</select>
					<?php echo '<span>('.$product['voorraad'].')</span>';?>
				</div>
				<div>
					<button onclick="cartAction(this, 'add', '<?php echo $product['product_code']; ?>');" class="btn" <?php if($product['voorraad'] == 0){echo 'disabled';}; ?>>Toevoegen</button>
				</div>
			</div>
			<div class="extra_info">
				<button onclick="description(this);" class="extra_info_button"></button>
				<div class="details_container clearfix">

					<?php if (empty($voedingswaarden)) { 
						echo '<p>Geen extra informatie beschikbaar.</p>';
						} else {
					?>	
					<div class="block_left">
						<img src="<?php echo $products_info['afbeelding']; ?>"/>
					</div>
					<div class="block_right">
						<div>
							<h5>Omschrijving</h5>
							<p><?php echo $products_info['product_omschrijving']; ?></p>
						</div>
						<div class="one_third">
							<div>
								<h5>Inhoud: </h5> 
								<p><?php echo $products_info['inhoud']; ?> Portie(s)</p>
							</div>
							<div>						
								<h5>Gram: </h5> 
								<p><?php echo $products_info['gram']; ?></p>
							</div>
							<div>
								<h5>Allergieën: </h5> 
								<p><?php echo $products_info['allergieen']; ?></p>
							</div>
						</div>
						<div class="two_third">
							<div>
								<h5>Voedingswaarden</h5>
								<p>Deze waarden gelden voor het onbereide product.</p>
							</div>
							<table>
								<tr>
									<th></th>
									<th>Per 100 gram</th> 
								</tr>
								<tr>
									<td>Energie</td>
									<td><?php echo $voedingswaarden['energie'].' kj ('.round($voedingswaarden['energie'] / 4.184, 2).'kcal)'; ?></td>
								</tr>
								<tr>
									<td>Vet</td> 
									<td><?php echo '< '.$voedingswaarden['vet'].' g'; ?></td> 
								</tr>
								<tr>
									<td>Waarvan verzadigd</td>
									<td><?php echo $voedingswaarden['waarvan_verzadigd'].' g'; ?></td> 

								</tr>
								<tr>
									<td>Koolhydraten</td>
									<td><?php echo $voedingswaarden['koolhydraten'].' g'; ?></td> 
								</tr>
								<tr>
									<td>Waarvan suikers</td>
									<td><?php echo $voedingswaarden['waarvan_suikers'].' g'; ?></td> 
								</td>
								</tr>
								<tr>
									<td>Eiwitten</td>
									<td><?php echo $voedingswaarden['eiwitten'].' g'; ?></td> 
								</tr>
									<td>Zout</td>
									<td><?php echo $voedingswaarden['zout'].' g'; ?></td> 
								</tr>
							</table>
						</div>
						<h5>Ingrediënten: </h5> 
						<p><?php echo $products_info['ingredienten']; ?></p>
					</div>
					<?php }; ?>
				</div>
			</div>
		</li>
		<?php
				$in_session = "0";
				if(!empty($_SESSION["cart_item"])) {
					$session_code_array = array_keys($_SESSION["cart_item"]);
				    if(in_array($product['product_code'],$session_code_array)) {
						$in_session = "1";
				    };
				};
			};
		};
		?>
	</ul>
</section>

<!-- De winkelmand !-->
<section id="checkout">
	<div>
		<h2>Bestelling</h2>
		<a href="#" onclick="classToggle('checkout', false); return false;" class="close"></a>
		<div class="content">
			<?php
				if (isset($_SESSION['cart'])) {
					require_once('cart.php');
				} else {
					echo '<p style="text-align: center;">Uw winkelmandje is nog leeg.</p>';
				};
			?>
		</div>
	</div>
</section>