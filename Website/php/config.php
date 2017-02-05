<?php
	// Database connectie
	$servername = "nickspc146.146.axc.nl";
	$username = "nickspc146_candy";
	$DBpassword = "TAJeJQfxV";
	$database = "nickspc146_candy";

	$connection= mysqli_connect($servername, $username, $DBpassword, $database) or die(mysql_error());

	// Als er geen connectie kan worden gemaakt wordt PHP afgesloten.
	if (!$connection) {
	    echo "Unable to connect to DB: " . mysql_error();
	    exit;
	};
?>