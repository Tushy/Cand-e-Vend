<?php
	// Connect met de database.
	require_once('../php/config.php');

	// Start de PHP sessies als deze nog niet gestart zijn.
	if(!isset($_SESSION['cart'])){
		session_start();
	};

	if(!empty($_POST["action"])) {
		switch($_POST["action"]) {
			case "add":
				// Voegt producten toe aan de cart sessie.
				if(!empty($_POST["kwantiteit"])) {
					$productByCode = mysqli_query($connection, "SELECT * FROM producten WHERE product_code='" . $_POST["product_code"] . "'")->fetch_assoc();

					$itemArray = array($productByCode["product_code"]=>array('product_naam'=>$productByCode["product_naam"], 'product_code'=>$productByCode["product_code"], 'kwantiteit'=>$_POST["kwantiteit"], 'prijs'=>$productByCode["prijs"], 'voorraad'=>$productByCode["voorraad"]));
					
					if(!empty($_SESSION['cart'])) {
						if(in_array($productByCode["product_code"],$_SESSION['cart'])) {
							foreach($_SESSION['cart'] as $k => $v) {
								if($productByCode["product_code"] == $k) {
									$_SESSION['cart'][$k]["kwantiteit"] = $_POST["kwantiteit"];
								};
							};
						} else {
							$_SESSION['cart'] = array_merge($_SESSION['cart'],$itemArray);
						};
					} else {
						$_SESSION['cart'] = $itemArray;
					};
					mysqli_close($connection);
				} else {
					if(empty($_SESSION['cart'])) {
						echo '<p style="text-align: center;">Uw winkelmandje is nog leeg.</p>';
					};
				};
			break;
			case "remove":
				// Verwijdert een product uit de cart sessie.
				if(!empty($_SESSION['cart'])) {
					foreach($_SESSION['cart'] as $k => $v) {
						if($_POST["product_code"] == $k) {
							unset($_SESSION['cart'][$k]);
						};
						if(empty($_SESSION['cart'])){
							unset($_SESSION['cart']);
							echo '<p style="text-align: center;">Uw winkelmandje is leeg gemaakt.</p>';
						};
					};
				};
			break;
			case "empty":
				// Verwijdert alle producten uit de cart sessie.
				if(!empty($_SESSION['cart'])) {
					foreach($_SESSION['cart'] as $k => $v) {
						unset($_SESSION['cart'][$k]);
					};
					unset($_SESSION['cart']);
					echo '<p style="text-align: center;">Uw winkelmandje is leeg gemaakt.</p>';
				};
			break;		
		};
	};
?>

<?php
if(isset($_SESSION['cart'])){
    $item_total = 0;
?>	
	<ul>
		<li class="list_header">
			<div><p>Naam</p></div>
			<div><p>Aantal</p></div>
			<div><p>Prijs</p></div>
			<div><p>Totaal</p></div>
		</li>
		<ul>
		<?php
	    	foreach ($_SESSION['cart'] as $item){ ?>
			<li>
				<div><p><?php echo $item["product_naam"]; ?></p></div>
				<div><p><?php echo $item["kwantiteit"]; ?></p></div>
				<div><p><?php echo "€ ".$item["prijs"]; ?></p></div>
				<div class='total'><p><?php echo "€ ".number_format($item["prijs"]*$item["kwantiteit"], 2, '.', ' '); ?></p></div>
				<div><button onclick="cartAction(this, 'remove','<?php echo $item["product_code"]; ?>')" class="btn">Verwijderen</button></div>
			</li>
		<?php 
			$item_total += ($item["prijs"]*$item["kwantiteit"]); }; 
		?>
		</ul>
		<li class="total_price">
			<div><p>Totaal</div>
			<div class='total'><p><?php echo "€ ".number_format($item_total, 2, '.', ' '); ?></p></div>
			<div><button onclick="cartAction(this, 'empty');" class="btn">Leeg het mandje</button></div>
		</li>
	</ul>

	<div class="loader_container">
		<div class="loader">
			<div class="loader_circle"></div>
		</div>
	</div>

	<div id="order_message"></div>

	<div id="orderContainer">
		<button onclick="order();" class="btn">Bestel</button>
	</div>
<?php }; ?>