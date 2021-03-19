<?php

namespace vplacek\QRPlatba;


use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use DateTime;

class QRPlatba {

	/**
	 * Verze QR formátu QR Platby.
	 */
	const VERSION = '1.0';

	private int $scale = 5;

	private array $keys = [
		'ACC' => null,
		// Max. 46 - znaků IBAN, BIC Identifikace protistrany !povinny
		'ALT-ACC' => null,
		// Max. 93 - znaků Seznam alternativnich uctu. odddeleny carkou,
		'AM' => null,
		//Max. 10 znaků - Desetinné číslo Výše částky platby.
		'CC' => 'CZK',
		// Právě 3 znaky - Měna platby.
		'DT' => null,
		// Právě 8 znaků - Datum splatnosti YYYYMMDD.
		'MSG' => null,
		// Max. 60 znaků - Zpráva pro příjemce.
		'X-VS' => null,
		// Max. 10 znaků - Celé číslo - Variabilní symbol
		'X-SS' => null,
		// Max. 10 znaků - Celé číslo - Specifický symbol
		'X-KS' => null,
		// Max. 10 znaků - Celé číslo - Konstantní symbol
		'RF' => null,
		// Max. 16 znaků - Identifikátor platby pro příjemce.
		'RN' => null,
		// Max. 35 znaků - Jméno příjemce.
		'PT' => null,
		// Právě 3 znaky - Typ platby.
		'CRC32' => null,
		// Právě 8 znaků - Kontrolní součet - HEX.
		'NT' => null,
		// Právě 1 znak P|E - Identifikace kanálu pro zaslání notifikace výstavci platby.
		'NTA' => null,
		//Max. 320 znaků - Telefonní číslo v mezinárodním nebo lokálním vyjádření nebo E-mailová adresa
		'X-PER' => null,
		// Max. 2 znaky -  Celé číslo - Počet dní, po které se má provádět pokus o opětovné provedení neúspěšné platby
		'X-ID' => null,
		// Max. 20 znaků. -  Identifikátor platby na straně příkazce. Jedná se o interní ID, jehož použití a interpretace závisí na bance příkazce.
		'X-URL' => null,
		// Max. 140 znaků. -  URL, které je možno využít pro vlastní potřebu
	];

	public function __construct() {

	}

	/**
	 * @return int
	 */
	public function getScale(): int {
		return $this->scale;
	}

	/**
	 * @param int $scale
	 * @return QRPlatba
	 */
	public function setScale(int $scale): QRPlatba {
		$this->scale = $scale;
		return $this;
	}


	/**
	 * Nastavení čísla účtu ve formátu 12-3456789012/0100.
	 *
	 * @param $account
	 *
	 * @return $this
	 */
	public function setAccount($account) {
		$this->keys['ACC'] = $account;

		return $this;
	}

	/**
	 * Nastavení částky.
	 *
	 * @param $amount
	 *
	 * @return $this
	 */
	public function setAmount($amount) {
		$this->keys['AM'] = sprintf('%.2f', $amount);

		return $this;
	}

	/**
	 * Nastavení variabilního symbolu.
	 *
	 * @param $vs
	 *
	 * @return $this
	 */
	public function setVariableSymbol($vs) {
		$this->keys['X-VS'] = $vs;

		return $this;
	}

	/**
	 * Nastavení konstatního symbolu.
	 *
	 * @param $cs
	 *
	 * @return $this
	 */
	public function setConstantSymbol($cs) {
		$this->keys['X-CS'] = $cs;

		return $this;
	}

	/**
	 * Nastavení zprávy pro příjemce. Z řetězce bude odstraněna diaktirika.
	 *
	 * @param $msg
	 *
	 * @return $this
	 */
	public function setMessage($msg) {
		$this->keys['MSG'] = mb_substr($this->stripDiacritics($msg), 0, 60);

		return $this;
	}

	/**
	 * Nastavení data úhrady.
	 *
	 * @param DateTime $date
	 *
	 * @return $this
	 */
	public function setDueDate(DateTime $date) {
		$this->keys['DT'] = $date->format('Ymd');

		return $this;
	}


	/**
	 * Vrati QR kod jako string.
	 * V Latte pouzij |datastream
	 *
	 * @return string
	 */
	public function toString(): string {

		$spayd = $this->generateSpayd();

		$options = new QROptions([
			"scale" => $this->getScale(),
			'outputType' => QRCode::OUTPUT_CUSTOM,
			'outputInterface' => QRPlatbaOutput::class,
			'eccLevel' => QRCode::ECC_L,
			'imageBase64' => FALSE,
			'quietzoneSize' => 4,
		]);

		$qrCode = new QRCode($options);

		return $qrCode->render($spayd);
	}

	/**
	 * Metoda vrátí QR Platbu jako textový řetězec.
	 *
	 * @return string
	 */
	private function generateSpayd(): string {
		$chunks = ['SPD', self::VERSION];
		foreach ($this->keys as $key => $value) {
			if ($value === null) {
				continue;
			}
			$chunks[] = $key . ':' . $value;
		}

		return implode('*', $chunks);
	}

	/**
	 * Odstranění diaktitiky.
	 *
	 * @param $string
	 *
	 * @return mixed
	 */
	private function stripDiacritics($string) {
		$string = str_replace(
			[
				'ě', 'š', 'č', 'ř', 'ž', 'ý', 'á', 'í', 'é', 'ú', 'ů',
				'ó', 'ť', 'ď', 'ľ', 'ň', 'ŕ', 'â', 'ă', 'ä', 'ĺ', 'ć',
				'ç', 'ę', 'ë', 'î', 'ń', 'ô', 'ő', 'ö', 'ů', 'ű', 'ü',
				'Ě', 'Š', 'Č', 'Ř', 'Ž', 'Ý', 'Á', 'Í', 'É', 'Ú', 'Ů',
				'Ó', 'Ť', 'Ď', 'Ľ', 'Ň', 'Ä', 'Ć', 'Ë', 'Ö', 'Ü'
			],
			[
				'e', 's', 'c', 'r', 'z', 'y', 'a', 'i', 'e', 'u', 'u',
				'o', 't', 'd', 'l', 'n', 'a', 'a', 'a', 'a', 'a', 'a',
				'c', 'e', 'e', 'i', 'n', 'o', 'o', 'o', 'u', 'u', 'u',
				'E', 'S', 'C', 'R', 'Z', 'Y', 'A', 'I', 'E', 'U', 'U',
				'O', 'T', 'D', 'L', 'N', 'A', 'C', 'E', 'O', 'U'
			],
			$string
		);

		return $string;
	}
}