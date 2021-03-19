# QR Platba

Knihovna pro generování QR plateb v PHP. 

Podporuje PHP 7.4+

## Instalace pomocí Composeru

`composer require 4vplacek/qr-platba`

## Použití

```php
<?php

use vplacek\QRPlatba\QRPlatba;

require __DIR__ . '/../vendor/autoload.php';


header('Content-Type: image/png');

$qrPlatba = new QRPlatba();
$qrPlatba->setIban("CZ1427000000000000333999")
	->setAmount(250)
	->setMessage("Fond Humanity Českého červeného kříže");

echo $qrPlatba->generateQr();

```

![Ukázka](qr_example.png)
