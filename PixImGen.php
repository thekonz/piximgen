<?php

/**
* ImgGen
* @author Konstantin Zinnen
*/
class PixImGen
{
	/**
	 * Settings for the object
	 * @var array
	 */
	public $settings;

	/**
	 * Default settings for the object
	 * @var array
	 */
	private $defaultSettings = [
		'seed' => 0,
		'width' => 10,
		'height' => 10,
		'blocksize' => 15,
		'minredsaturation' => 0,
		'maxredsaturation' => 255,
		'mingreensaturation' => 0,
		'maxgreensaturation' => 255,
		'minbluesaturation' => 0,
		'maxbluesaturation' => 255,
	];

	private $minSettings = [
		'seed' => 0,
		'width' => 1,
		'height' => 1,
		'blocksize' => 1,
		'minredsaturation' => 0,
		'maxredsaturation' => 0,
		'mingreensaturation' => 0,
		'maxgreensaturation' => 0,
		'minbluesaturation' => 0,
		'maxbluesaturation' => 0,
	];

	private $maxSettings = [
		'seed' => PHP_INT_MAX,
		'width' => 15,
		'height' => 15,
		'blocksize' => 25,
		'minredsaturation' => 255,
		'maxredsaturation' => 255,
		'mingreensaturation' => 255,
		'maxgreensaturation' => 255,
		'minbluesaturation' => 255,
		'maxbluesaturation' => 255,
	];

	/**
	 * Constructor
	 * @param array $settings Settings for the object
	 */
	public function __construct(array $settings) 
	{
		$this->setSettings($settings);
	}

	public function setSettings(array $settings)
	{
		$this->defaultSettings['seed'] = time();

		$settings = array_merge($this->defaultSettings, array_filter($settings, 'trim'));

		$this->settings = array_map(function($key, $value) { return $this->capSettings($key, $value); }, array_keys($settings), $settings);

	}

	private function capSettings($key, $value)
	{
		return $key => $value;
	}

	public function getImage()
	{
		return $this->generateImage();
	}

	private function generateImage()
	{
		$this->setRandomSeed();
		$image = $this->createImage();
		$this->paintRows($image);
		return $image;
	}

	private function createImage()
	{
		$image = new Imagick();
		$image->newPseudoImage($this->settings['width']*$this->settings['blocksize'], $this->settings['height']*$this->settings['blocksize'], "transparent");
		$image->setImageFormat('png');
		return $image;
	}

	private function paintRows($image)
	{
		for($i = 0; $i < $this->settings['height']; $i++) 
			$this->paintRow($image, $i);
	}

	private function paintRow($image, $row)
	{
		for ($i=0; $i < $this->settings['width']; $i++)
			$this->paintBlock($image, $row, $i);
	}

	private function paintBlock($image, $row, $column)
	{
		$x1 = $column * $this->settings['blocksize'];
		$y1 = $row * $this->settings['blocksize'];
		$x2 = $x1 + $this->settings['blocksize'];
		$y2 = $y1 + $this->settings['blocksize'];
		$draw = new ImagickDraw();
		$draw->setFillColor($this->generateColor($image));
		$draw->rectangle($x1, $y1, $x2, $y2);
		$image->draw($draw);
	}

	private function generateColor($image)
	{
		return new ImagickPixel('rgb('.
			rand($this->settings['minredsaturation'], $max = $this->settings['maxredsaturation']).','. 
			rand($this->settings['mingreensaturation'], $max = $this->settings['maxgreensaturation']).','. 
			rand($this->settings['minbluesaturation'], $max = $this->settings['maxbluesaturation']).')'
		);
	}

	private function getSeed()
	{
		return hexdec(hash('adler32', $this->settings['seed']));
	}

	private function setRandomSeed()
	{
		srand($this->getSeed());
	}
}