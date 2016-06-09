<?php
/**
 * Copyright (c) 2016 Jan Kotrba https://jkotrba.net/
 */

namespace App\WebModule\Model;

use App\AdminModule\Model\BaguettesManager;
use App\AdminModule\Model\Entities\BaguetteResource;
use Nette;

/**
 * Class BaguetteFacade
 * @package App\WebModule\Model
 */
class BaguetteFacade extends Nette\Object
{

	/** @var BaguettesManager */
	private $baguettesManager;

	/**
	 * BaguetteFacade constructor.
	 * @param BaguettesManager $baguettesManager
	 */
	public function __construct(BaguettesManager $baguettesManager)
	{
		$this->baguettesManager = $baguettesManager;
	}

	/**
	 * @return array
	 */
	public function getAll()
	{
		return $this->baguettesManager->getAllBaguettes();
	}

	/**
	 * @param $results
	 * @return array
	 */
	private function convert($results)
	{

		$resultArray = [];

		/** @var BaguetteResource $result */
		foreach ($results as $result) {
			$resultArray[$result->getId()] = $result->getName() . '~' . $result->getPrice();
		}

		return $resultArray;
	}

	/**
	 * @param $type
	 * @return array
	 */
	public function getResourcesArrayByType($type)
	{
		return self::convert($this->baguettesManager->getAllResourcesByType($type));
	}

	/**
	 * @param $id
	 * @return array
	 */
	public function getResourcesById(array $id)
	{
		$resources = $this->baguettesManager->getResourcesById($id);

		if (!$resources) {
			return false;
		}

		if (is_array($resources)) {
			return $resources;
		} else {
			return [$resources];
		}
	}

	/**
	 * @param $id
	 * @return mixed|null|object
	 */
	public function getMenuBaguette($id)
	{
		return $this->baguettesManager->getBaguette($id);
	}
}