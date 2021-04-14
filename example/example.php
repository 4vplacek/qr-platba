<?php

use vplacek\QRPlatba\QRPlatba;

require __DIR__ . '/../vendor/autoload.php';


header('Content-Type: image/png');

$qrPlatba = new QRPlatba();
$qrPlatba->setIban("CZ1427000000000000333999")
	->setAmount(250)
	->setScale(5) //velikost QR kodu
	->setCurrency("EUR")
	->setVariableSymbol(123456)
	->setSpecificSymbol(1414)
	->setRecipientName("Petr Novák")
	->setDueDate(new DateTime("+ 14 days"))
	->setMessage("Fond Humanity Českého červeného kříže");

echo $qrPlatba->generateQr();
