<?php

use vplacek\QRPlatba\QRPlatba;

require __DIR__ . '/../vendor/autoload.php';


header('Content-Type: image/png');

$qrPlatba = new QRPlatba();
$qrPlatba->setIban("CZ1427000000000000333999")
	->setAmount(250)
	->setScale(5)
	->setMessage("Fond Humanity Českého červeného kříže");

echo $qrPlatba->generateQr();
