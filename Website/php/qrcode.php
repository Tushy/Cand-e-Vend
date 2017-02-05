<?php
	// Haalt de QR_code library op.
	require_once('../php/qr_code_generator/src/QrCode.php');

	// Genereert een QR_code.
	use Endroid\QrCode\QrCode;

	if(isset($_GET['code'])) {
		$qr = new QrCode();
		$qr
			->setText($_GET['code'])
			->setSize(300)
			->setPadding(10)
			->setErrorCorrection('high')
			->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0])
			->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0])
			->setLabel('Scan de code')
			->setLabelFontSize(16)
			->setImageType(QrCode::IMAGE_TYPE_PNG)
		;

		$qr->render();
	};
?>