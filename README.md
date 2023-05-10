# QR Platba

[![Latest Stable Version](http://poser.pugx.org/4vplacek/qr-platba/v)](https://packagist.org/packages/4vplacek/qr-platba) [![Total Downloads](http://poser.pugx.org/4vplacek/qr-platba/downloads)](https://packagist.org/packages/4vplacek/qr-platba)  [![License](http://poser.pugx.org/4vplacek/qr-platba/license)](https://packagist.org/packages/4vplacek/qr-platba) [![PHP Version Require](http://poser.pugx.org/4vplacek/qr-platba/require/php)](https://packagist.org/packages/4vplacek/qr-platba)

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
	->setScale(5) //velikost QR kodu
	->setCurrency("EUR") //právě 3 znaky - ISO_4217. Výchozí je CZK
	->setVariableSymbol(123456)
	->setSpecificSymbol(1414)
	->setRecipientName("Petr Novák")
	->setDueDate(new DateTime("+ 14 days")) // nastaví datum splatnosti. Nedoporučuju používat. Banka zařadí platbu mezi plánované platby a klient nebude vědět, jestli ji odeslal
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
