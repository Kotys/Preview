<?php

namespace App\Admin\Model;

use App\Admin\Model\Entities\Equipment;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Nette;
use Tracy\Debugger;

/**
 * Class EquipmentManager
 * @package App\Admin\Model
 */
class EquipmentManager extends Nette\Object
{

	/**
	 * @var EntityManager
	 */
	private $entityManager;

	/**
	 * @var UserManager
	 */
	private $userManager;

	/**
	 * @var Nette\Security\User
	 */
	private $user;

	/**
	 * @var GalleryManager
	 */
	private $galleryManager;

	/**
	 * EquipmentManager constructor.
	 * @param EntityManager $entityManager
	 * @param UserManager $userManager
	 * @param Nette\Security\User $user
	 * @param GalleryManager $galleryManager
	 */
	public function __construct(EntityManager $entityManager, UserManager $userManager, Nette\Security\User $user, GalleryManager $galleryManager)
	{
		$this->entityManager = $entityManager;
		$this->userManager = $userManager;
		$this->galleryManager = $galleryManager;
		$this->user = $user;
	}

	/**
	 * @param $values
	 * @throws EquipmentException
	 */
	public function addEquipment($values)
	{
		$equipment = new Equipment();

		$equipment->setName($values->name);
		$equipment->setProductionYear($values->productYear);
		$equipment->setEngineCapacity($values->engineCapacity);
		$equipment->setPower($values->power);
		$equipment->setShortDescription($values->shortDescription);
		$equipment->setGear($values->gear);
		$equipment->setSeats($values->seats);
		$equipment->setLongDescription($values->longDescription);
		$equipment->setAuthor($this->userManager->findOneBy(['id' => $this->user->getIdentity()->getId()]));

		if ($values->hookThisGallery) {
			$gallery = $this->galleryManager->getGalleryById($values->hookGallery);
			if (!$gallery) {
				throw new EquipmentException('Galerie, kterou se pokoušíte přiřadit neexistuje.');
			}
			$equipment->setGalleryHook($gallery);
		}

		try {
			$this->entityManager->persist($equipment);
			$this->entityManager->flush();
		} catch (ORMException $e) {
			throw new EquipmentException('Přidávání techniky selhalo.');
		}
	}

	/**
	 * @return array
	 */
	public function getAllEquipment()
	{
		return $this->entityManager->getRepository(Equipment::getClassName())->findAll();
	}

	/**
	 * @param $equipmentId
	 * @return null|object
	 */
	public function getEquipmentById($equipmentId)
	{
		return $this->entityManager->getRepository(Equipment::getClassName())->findOneBy(['id' => $equipmentId]);
	}

	/**
	 * @param $equipmentId
	 * @param $values
	 * @throws EquipmentException
	 */
	public function saveEquipment($equipmentId, $values)
	{

		/** @var Equipment $equipment */
		$equipment = self::getEquipmentById($equipmentId);

		$equipment->setName($values->name);
		$equipment->setProductionYear($values->productYear);
		$equipment->setEngineCapacity($values->engineCapacity);
		$equipment->setPower($values->power);
		$equipment->setShortDescription($values->shortDescription);
		$equipment->setGear($values->gear);
		$equipment->setLongDescription($values->longDescription);

		if ($values->hookThisGallery) {
			$gallery = $this->galleryManager->getGalleryById($values->hookGallery);
			if (!$gallery) {
				throw new EquipmentException('Galerie, kterou se pokoušíte přiřadit neexistuje.');
			}
			$equipment->setGalleryHook($gallery);
		} else {
			$equipment->removeGalleryHook();
		}

		$equipment->updateLastChanged();

		try {
			$this->entityManager->persist($equipment);
			$this->entityManager->flush();
		} catch (ORMException $e) {
			throw new EquipmentException('Ukládání techniky selhalo.');
		}
	}

	/**
	 * @param $equipmentId
	 * @throws EquipmentException
	 * @throws GalleryException
	 */
	public function removeEquipment($equipmentId)
	{
		$equipment = self::getEquipmentById($equipmentId);

		if (!$equipment) {
			throw new GalleryException('Tato technika neexistuje.');
		}

		try {
			$this->entityManager->remove($equipment);
			$this->entityManager->flush();
		} catch (ORMException $e) {
			throw new EquipmentException('Odstraňování techniky selhalo.');
		}
	}
}

/**
 * Class EquipmentException
 * @package App\Admin\Model
 */
class EquipmentException extends \Exception
{
}