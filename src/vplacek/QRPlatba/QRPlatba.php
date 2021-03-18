<?php

namespace vplacek\QRPlatba;


use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class QRPlatba {

	private string $iban;
	private string $currency = "CZK";

	public function __construct() {

	}

	public function toString() {

		$options = new QROptions([
			"scale" => 4,
			'outputType' => QRCode::OUTPUT_CUSTOM,
			'outputInterface' => QRPlatbaOutput::class,
			'eccLevel' => QRCode::ECC_L,
			'imageBase64' => FALSE,
			'quietzoneSize' => 4,
		]);

		$qrCode = new QRCode($options);

		return $qrCode->render("123123123");
	}

}