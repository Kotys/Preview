<?php
/**
 * Copyright (c) 2016 Jan Kotrba https://jkotrba.net/
 */

namespace App\AdminModule\Model;

use App\AdminModule\Model\Entities\BaguetteResource;
use App\AdminModule\Model\Entities\MenuBaguette;
use Nette;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Class AdminBaguettesFacade
 * @package App\AdminModule\Model
 */
class AdminBaguettesFacade extends Nette\Object
{

	/**
	 * @var BaguettesManager
	 */
	private $manager;

	/**
	 * AdminBaguettesFacade constructor.
	 * @param BaguettesManager $baguettesManager
	 */
	public function __construct(BaguettesManager $baguettesManager)
	{
		$this->manager = $baguettesManager;
	}

	/**
	 * @param $type
	 * @param $name
	 * @param $price
	 * @param $allergens
	 * @throws CRUDException
	 */
	public function addResource($type, $name, $price, $allergens)
	{
		$this->manager->addResource($type, $name, $price, $allergens);
	}

	/**
	 * @param $resource
	 * @param $type
	 * @param $name
	 * @param $price
	 * @param $allergens
	 * @throws CRUDException
	 */
	public function updateResource(&$resource, $type, $name, $price, $allergens)
	{
		$this->manager->updateResource($resource, $type, $name, $price, $allergens);
	}

	/**
	 * @param $type
	 * @return array
	 */
	public function getAllResourcesByType($type)
	{
		return $this->manager->getAllResourcesByType($type);
	}

	/**
	 * @param BaguetteResource $resource
	 * @return bool
	 */
	public function removeResource($resource)
	{
		if (!$resource instanceof BaguetteResource) {
			return false;
		}

		$this->manager->removeResource($resource);

		return true;
	}

	/**
	 * @param array $criteria
	 * @return mixed|null|object
	 */
	public function getResource(array $criteria)
	{
		return $this->manager->getResource($criteria);
	}

	/**
	 * @return array
	 */
	public function getGroups()
	{
		return [
			'baked-goods' => 'Pečivo',
			'filling' => 'Náplň',
			'vegetable' => 'Zelenina',
			'dressing' => 'Dresink',
		];
	}

	/**
	 * @param $type
	 * @param bool $forceArray
	 * @return array
	 */
	public function getResourcesByType($type, $forceArray = false)
	{
		$resources = $this->manager->getEntityManager()->getRepository(BaguetteResource::getClassName())->findBy(['disabled' => false, 'type' => $type], ['name' => 'ASC']);
		if ($forceArray) {
			$resources_final = [];
			/** @var BaguetteResource $resource */
			foreach ($resources as $resource) {
				$resources_final[$resource->getId()] = $resource->getName();
			}

			return $resources_final;
		}
		return $resources;
	}

	/**
	 * @param $values
	 * @param $imageLeftName
	 * @param $imageRightName
	 * @return MenuBaguette
	 * @throws BaguettesFacadeException
	 */
	public function addMenuBaguette($values, $imageLeftName, $imageRightName)
	{
		try {
			return $this->manager->addMenuBaguette($values, $imageLeftName, $imageRightName);
		} catch (CRUDException $e) {
			Debugger::log($e->getMessage(), ILogger::ERROR);
			throw new BaguettesFacadeException('Bagetu se nepodařilo přidat do menu.');
		}
	}

	/**
	 * @param MenuBaguette $baguette
	 */
	public function removeBaguette(MenuBaguette $baguette)
	{

		$this->manager->removeBaguette($baguette);

	}

	/**
	 * @return array
	 */
	public function getAllBaguettes()
	{
		return $this->manager->getAllBaguettes();
	}

	/**
	 * @param $id
	 * @return null|object
	 */
	public function getBaguetteById($id)
	{
		return $this->manager->getBaguette($id);
	}

	/**
	 * @param BaguetteResource $baguetteResource
	 */
	public function disable(BaguetteResource $baguetteResource)
	{
		$this->manager->disable($baguetteResource);
	}
}

/**
 * Class BaguettesFacadeException
 * @package App\AdminModule\Model
 */
class BaguettesFacadeException extends \Exception
{
}

/**
 * Class ResourceException
 * @package App\AdminModule\Model
 */
class ResourceException extends BaguettesFacadeException
{
}