<?php
	session_start();
	
	// Verwijdert alle huidige sessies.
	if(session_destroy()) {
		exit();
	};
?>