<?php
/**
 * Copyright (c) 2016 Jan Kotrba https://jkotrba.net/
 */

namespace App\AdminModule\Model;

use App\AdminModule\Model\Entities\BaguetteResource;
use App\AdminModule\Model\Entities\MenuBaguette;
use Doctrine\ORM\ORMException;
use Kdyby\Doctrine\EntityManager;
use Nette;
use Tracy\Debugger;

/**
 * Class BaguettesManager
 * @package App\AdminModule\Model
 */
class BaguettesManager extends Nette\Object
{

	/**
	 * @var EntityManager
	 */
	private $entityManager;

	/**
	 * BaguettesManager constructor.
	 * @param EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
	}

	/**
	 * @return EntityManager
	 */
	public function getEntityManager()
	{
		return $this->entityManager;
	}

	/**
	 * @param $type
	 * @param $name
	 * @param $price
	 * @param $allergens
	 * @throws CRUDException
	 * @throws \Exception
	 */
	public function addResource($type, $name, $price, $allergens)
	{
		if ($this->entityManager->getRepository(BaguetteResource::getClassName())->findOneBy(['name' => $name])) {
			throw new CRUDException('Surovina <b>' . $name . '</b> tohoto druhu již existuje.');
		}

		$resource = new BaguetteResource();
		$resource->setType($type);
		$resource->setName($name);
		$resource->setPrice($price);
		$resource->setAllergens($allergens);

		try {
			$this->entityManager->persist($resource);
			$this->entityManager->flush();
		} catch (ORMException $e) {
			throw new CRUDException('Přidávání suroviny selhalo.');
		}
	}

	/**
	 * @param BaguetteResource $resource
	 * @param $type
	 * @param $name
	 * @param $price
	 * @param $allergens
	 * @throws CRUDException
	 * @throws \Exception
	 */
	public function updateResource(BaguetteResource &$resource, $type, $name, $price, $allergens)
	{
		/** @var BaguetteResource $resource */

		try {
			$resource->setDisabled(true);
			$this->entityManager->persist($resource);
			$this->entityManager->flush();

			$resource = new BaguetteResource();
			$resource->setType($type);
			$resource->setName($name);
			$resource->setPrice($price);
			$resource->setAllergens($allergens);

			$this->entityManager->persist($resource);
			$this->entityManager->flush();
		} catch (ORMException $e) {
			throw new CRUDException('Ukládání změn suroviny selhalo.');
		}
	}

	/**
	 * @param $type
	 * @return array
	 */
	public function getAllResourcesByType($type)
	{
		return $this->entityManager->getRepository(BaguetteResource::getClassName())->findBy(['type' => $type, 'disabled' => false], ['name' => 'ASC']);
	}


	/**
	 * @param BaguetteResource $resource
	 * @throws CRUDException
	 * @throws \Exception
	 */
	public function removeResource(BaguetteResource $resource)
	{
		try {
			$this->entityManager->remove($resource);
			$this->entityManager->flush();
		} catch (ORMException $e) {
			throw new CRUDException('Odstraňování suroviny selhalo.');
		}
	}

	/**
	 * @param array $criteria
	 * @return mixed|null|object
	 */
	public function getResource(array $criteria)
	{
		return $this->entityManager->getRepository(BaguetteResource::getClassName())->findOneBy($criteria);
	}

	/**
	 * @param BaguetteResource $baguetteResource
	 * @throws \Exception
	 */
	public function disable(BaguetteResource $baguetteResource)
	{
		$baguetteResource->setDisabled(true);
		$this->entityManager->persist($baguetteResource);
		$this->entityManager->flush();
	}


	/**
	 * @param $values
	 * @param $imageLeftName
	 * @param $imageRightName
	 * @return MenuBaguette
	 * @throws CRUDException
	 * @throws \Exception
	 */
	public function addMenuBaguette($values, $imageLeftName, $imageRightName)
	{
		$menuBaguette = new MenuBaguette();
		$menuBaguette->setBaguetteName($values->name);
		$menuBaguette->setTemptation(nl2br($values->temptation));
		$menuBaguette->setBaguetteDesc(nl2br($values->desc));
		$menuBaguette->setPriority($values->priority);
		$menuBaguette->setImageLeft($imageLeftName);
		$menuBaguette->setImageRight($imageRightName);

		$bakedGoods = $this->entityManager->getRepository(BaguetteResource::getClassName())->findOneBy(['id' => $values->bakedGoods]);
		$fillings = $this->entityManager->getRepository(BaguetteResource::getClassName())->findBy(['id' => array_values($values->fillings)]);
		$vegetables = $this->entityManager->getRepository(BaguetteResource::getClassName())->findBy(['id' => array_values($values->vegetable)]);
		$dressings = $this->entityManager->getRepository(BaguetteResource::getClassName())->findBy(['id' => array_values($values->dressing)]);

		$menuBaguette->setBakedGoods($bakedGoods);

		foreach ($fillings as $filling) {
			$menuBaguette->addFilling($filling);
		}

		foreach ($vegetables as $vegetable) {
			$menuBaguette->addVegetables($vegetable);
		}

		foreach ($dressings as $dressing) {
			$menuBaguette->addDressing($dressing);
		}

		$price = (empty($values->price)) ? $menuBaguette->getPrice() : $values->price;
		$menuBaguette->setPrice($price);

		try {
			$this->entityManager->persist($menuBaguette);
			$this->entityManager->flush();
			return $menuBaguette;
		} catch (ORMException $e) {
			throw new CRUDException('Odstraňování suroviny selhalo.');
		}
	}

	/**
	 * @param MenuBaguette $baguette
	 * @throws \Exception
	 */
	public function removeBaguette(MenuBaguette $baguette)
	{
		$baguette->setDisabled(true);
		$this->entityManager->persist($baguette);
		$this->entityManager->flush();
	}

	/**
	 * @return array
	 */
	public function getAllBaguettes()
	{
		return $this->entityManager->getRepository(MenuBaguette::getClassName())->findBy(['disabled' => false], ['priority' => 'DESC']);
	}

	/**
	 * @param $id
	 * @return mixed|null|object
	 */
	public function getBaguette($id)
	{
		return $this->entityManager->getRepository(MenuBaguette::getClassName())->findOneBy(['id' => $id]);
	}

	/**
	 * @param array $resourcesId
	 * @return array
	 */
	public function getResourcesById(array $resourcesId)
	{
		return $this->entityManager->getRepository(BaguetteResource::getClassName())->findBy(['id' => $resourcesId]);
	}
}

/**
 * Class AddResourceException
 * @package App\AdminModule\Model
 */
class CRUDException extends \Exception
{
}