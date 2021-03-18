<?php


namespace vplacek\QRPlatba;


use chillerlan\QRCode\Data\QRMatrix;
use chillerlan\QRCode\Output\QROutputAbstract;
use chillerlan\QRCode\QRCodeException;
use chillerlan\Settings\SettingsContainerInterface;
use Nette\Utils\Image;

class QRPlatbaOutput extends QROutputAbstract {

	/**
	 * @var Image
	 */
	protected Image $image;

	/**
	 * @inheritDoc
	 *
	 * @throws QRCodeException
	 */
	public function __construct(SettingsContainerInterface $options, QRMatrix $matrix) {

		if (!extension_loaded('gd')) {
			throw new QRCodeException('ext-gd not loaded'); // @codeCoverageIgnore
		}

		parent::__construct($options, $matrix);
	}

	/**
	 * @inheritDoc
	 */
	protected function setModuleValues(): void {

		foreach ($this::DEFAULT_MODULE_VALUES as $M_TYPE => $defaultValue) {
			$v = $this->options->moduleValues[$M_TYPE] ?? null;

			if (!is_array($v) || count($v) < 3) {
				$this->moduleValues[$M_TYPE] = $defaultValue
					? [0, 0, 0]
					: [255, 255, 255];
			} else {
				$this->moduleValues[$M_TYPE] = array_values($v);
			}

		}

	}

	/**
	 * @inheritDoc
	 *
	 * @return string|resource|\GdImage
	 *
	 * @phan-suppress PhanUndeclaredTypeReturnType, PhanTypeMismatchReturn
	 */
	public function dump(string $file = null) {


		$this->image = Image::fromBlank($this->length, $this->length);


		foreach ($this->matrix->matrix() as $y => $row) {
			foreach ($row as $x => $M_TYPE) {
				$this->setPixel($x, $y, $this->moduleValues[$M_TYPE]);
			}
		}

		$this->image->rectangle(0, 0, $this->length - 1, $this->length - 1, Image::rgb(0, 0, 0)); // ohraniceni

		$spodniOkraj = $this->length + (int)($this->length / 10);

		$canvas = Image::fromBlank($this->length, $spodniOkraj, Image::rgb(255, 255, 255)); // novy obrazek

		$canvas->place($this->image, 0, 0); // vlozime qr do noveho obrazku

		$canvas->filledRectangle((int)($this->length / 25), (int)($this->length - $this->length / 25), (int)($this->length / 1.7), $spodniOkraj, Image::rgb(255, 255, 255));

		$font = __DIR__ . "/open_sans.ttf";
		$textSize = (int)(2.5 * $this->scale);
		$canvas->ttfText($textSize, 0, (int)($this->length / 10), $this->length + $textSize, Image::rgb(0, 0, 0), $font, "QR platba");

		return $canvas->toString(Image::PNG, 80); // PNG, kvalita 80%

	}

	/**
	 * Creates  a single QR pixel with the given settings
	 * @param int $x
	 * @param int $y
	 * @param array $rgb
	 */
	protected function setPixel(int $x, int $y, array $rgb): void {
		$this->image->filledRectangle(
			$x * $this->scale,
			$y * $this->scale,
			($x + 1) * $this->scale,
			($y + 1) * $this->scale,
			Image::rgb(...$rgb)
		);

	}
}