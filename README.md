# QR Platba

Knihovna pro generování QR plateb v PHP. 

Inspirováno [dfridrich/QRPlatba](https://github.com/dfridrich/QRPlatba) , ale je to postaveno nad knihovnou [chillerlan/php-qrcode](https://github.com/chillerlan/php-qrcode) takže malá velikost a minimum závislostí :heart:


Přidává kolem QR kódu rámeček a label QR platba.

PHP 7.4+

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

## Použití v Nette (Latte)

```php
<?php

//...

$this->template->qrPlatba = $qrPlatba->generateQr();
```

### Šablona

```html
<img src="{$qrPlatba|dataStream}" />
```

![Ukázka](qr_example.png)
