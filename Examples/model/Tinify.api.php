<?php
/**
 * Copyright (c) 2016 Jan Kotrba https://jkotrba.net/
 */

namespace App\AdminModule\Model;

use Nette;
use Tinify\Source;
use Tracy\Debugger;

/**
 * Class Tinify
 * @package App\AdminModule\Model
 */
class Tinify extends Nette\Object
{

	/**
	 * @var \Tinify\Tinify
	 */
	private $tinifyApi;

	/**
	 * Tinify constructor.
	 * @param \Tinify\Tinify $tinifyApi
	 */
	public function __construct(\Tinify\Tinify $tinifyApi)
	{
		$tinifyApi->setKey('xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
		$this->tinifyApi = $tinifyApi;
	}

	/**
	 * @param $sourcePath
	 * @param $destinationPath
	 * @return bool
	 * @throws ImageSaveException
	 * @throws Nette\Utils\UnknownImageFileException
	 */
	public function compress($sourcePath, $destinationPath)
	{
		/** @var Nette\Utils\Image $image */
		$image = Nette\Utils\Image::fromFile($sourcePath);
		$image->resize(900, 426, Nette\Utils\Image::FIT);

		@mkdir('upload/our-menu', 775, true);

		if (!$image->save($destinationPath)) {
			throw new ImageSaveException('Obrázek nebylo možné uložit.');
		}

		try {
			/** @var Source $source */
			$source = \Tinify\fromFile($destinationPath);
			if($source->toFile($destinationPath)) {
				@chmod($destinationPath, 775);
			}
		} catch (\Exception $e) {
			return false;
		}

		return true;
	}
}

/**
 * Class TinifyException
 * @package App\AdminModule\Model
 */
class TinifyException extends \Exception
{
}

/**
 * Class ImageSaveException
 * @package App\AdminModule\Model
 */
class ImageSaveException extends \Exception
{
}