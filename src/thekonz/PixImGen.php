<?php
namespace thekonz;

/**
* PixImGen
* 
* Create pixel graphics with a seed!
* 
* This class uses ImageMagick, so make sure, 
* you have it installed (php5-imagick).
* 
* @author Konstantin Zinnen
*/
class PixImGen
{
	/**
	 * Settings for the image.
	 * @var array
	 */
	private $settings;

	/**
	 * Default settings for the image.
	 * @var array
	 */
	private $defaultSettings = array(
		'seed' => 0,
		'blocksize' => 15,
		'width' => 10,
		'height' => 10,

		'minredsaturation' => 0,
		'maxredsaturation' => 255,

		'mingreensaturation' => 0,
		'maxgreensaturation' => 255,

		'minbluesaturation' => 0,
		'maxbluesaturation' => 255,
	);

	/**
	 * Constructor.
	 * @param array
	 */
	public function __construct(array $settings = array()) 
	{
		$this->setSettings($settings);
	}

	/**
	 * Sets $this->settings.
	 * @param array
	 */
	public function setSettings(array $settings)
	{
		$this->defaultSettings['seed'] = \time();
		$this->settings = \array_merge(
			$this->defaultSettings,
			array_filter($settings, 'trim')
		);
	}

	/**
	 * Gets the Imagick object with the current settings.
	 * @return Imagick
	 */
	public function getImage()
	{
		return $this->generateImage();
	}

	/**
	 * Generates the Imagick object with the current settings.
	 * @return Imagick
	 */
	private function generateImage()
	{
		$this->setRandomSeed();
		
		$image = $this->createImage();
		$this->paintRows($image);
		
		return $image;
	}

	/**
	 * Gets the value of the seed for the random generator.
	 * @return int
	 */
	private function getSeed()
	{
		return hexdec(hash('adler32', $this->settings['seed']));
	}

	/**
	 * Sets the seed for the random generator.
	 */
	private function setRandomSeed()
	{
		srand($this->getSeed());
	}

	/**
	 * Creates and returns a transparent Imagick object.
	 * @return Imagick
	 */
	private function createImage()
	{
		$image = new \Imagick();

		$image->newImage(
			$this->settings['width'] * $this->settings['blocksize'],
			$this->settings['height'] * $this->settings['blocksize'],
			new \ImagickPixel('transparent'),
			'PNG'
		);
		
		return $image;
	}

	/**
	 * Paints the Imagick object row by row.
	 * @param  Imagick $image
	 */
	private function paintRows(\Imagick $image)
	{
		for($i = 0; $i < $this->settings['height']; $i++) 
			$this->paintRow($image, $i);
	}

	/**
	 * Paints a single row block by block.
	 * @param  Imagick $image The Imagick object to paint on.
	 * @param  int     $row Number of the row.
	 */
	private function paintRow(\Imagick $image, $row)
	{
		for ($i=0; $i < $this->settings['width']; $i++)
			$this->paintBlock($image, $row, $i);
	}

	/**
	 * Paints a single block in a random color based on the settings.
	 * @param  Imagick $image  The Imagick object to paint on.
	 * @param  int     $row    The Number of the row.
	 * @param  int     $column The Number of the column.
	 */
	private function paintBlock(\Imagick $image, $row, $column)
	{
		$x1 = $column * $this->settings['blocksize'];
		$y1 = $row * $this->settings['blocksize'];
		$x2 = $x1 + $this->settings['blocksize'];
		$y2 = $y1 + $this->settings['blocksize'];

		$draw = new \ImagickDraw();
		
		$draw->setFillColor($this->generateColor());
		$draw->rectangle($x1, $y1, $x2, $y2);
		
		$image->drawImage($draw);
	}

	/**
	 * Generate a random color based on the settings 
	 * and return it as an ImagickPixel object.
	 * @return ImagickPixel
	 */
	private function generateColor()
	{
		return new \ImagickPixel('rgb('.
			implode(',', array(
				rand(
					$this->settings['minredsaturation'],
					$this->settings['maxredsaturation']
				), 
				rand(
					$this->settings['mingreensaturation'],
					$this->settings['maxgreensaturation']
				),
				rand(
					$this->settings['minbluesaturation'],
					$this->settings['maxbluesaturation']
				)
			).')'
		);
	}
}