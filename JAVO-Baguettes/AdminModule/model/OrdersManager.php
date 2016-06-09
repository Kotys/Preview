<?php
/**
 * Copyright (c) 2016 Jan Kotrba https://jkotrba.net/
 */

namespace App\AdminModule\Model;

use App\Model\Entities\User;
use App\WebModule\Model\Entities\Orders;
use App\WebModule\Model\UserManager;
use Nette;
use Doctrine\ORM\EntityManager;
use Tracy\Debugger;

/**
 * Class OrdersManager
 * @package App\AdminModule\Model
 */
class OrdersManager extends Nette\Object
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
	 * OrdersManager constructor.
	 * @param EntityManager $entityManager
	 * @param UserManager $userManager
	 * @param Nette\Security\User $user
	 */
	public function __construct(EntityManager $entityManager, UserManager $userManager, Nette\Security\User $user)
	{
		$this->entityManager = $entityManager;
		$this->userManager = $userManager;
		$this->user = $user;
	}

	/**
	 * @param Orders $orders
	 */
	public function save(Orders $orders)
	{
		$orders->beforeSave();
		$orders->setDateOfPurchase(new Nette\Utils\DateTime());

		if ($this->user->isLoggedIn()) {
			/** @var User $user */
			$user = $this->userManager->getUser($this->user->getId());

			$orders->setUser($user);
			$user->addOrder($orders);

			$this->entityManager->persist($orders);
			$this->entityManager->flush();

			$this->entityManager->persist($user);
			$this->entityManager->flush();
		} else {
			$orders->removeUser();
			$this->entityManager->persist($orders);
			$this->entityManager->flush();
		}
	}

	/**
	 * @return array
	 */
	public function getUserOrders()
	{
		return $this->entityManager->getRepository(Orders::getClassName())->findBy(['user' => $this->user->getId()], ['dateOfPurchase' => 'DESC']);
	}

	/**
	 * @param $orderId
	 * @param bool $verifyUser
	 * @return null|object
	 */
	public function getOrder($orderId, $verifyUser = true)
	{
		if ($verifyUser) {
			return $this->entityManager->getRepository(Orders::getClassName())->findOneBy(['user' => $this->user->getId(), 'id' => $orderId]);
		} else {
			return $this->entityManager->getRepository(Orders::getClassName())->findOneBy(['id' => $orderId]);
		}
	}

	/**
	 * @return array
	 */
	public function getOrders()
	{
		return $this->entityManager->getRepository(Orders::getClassName())->findBy([], ['processed' => 'ASC', 'dateOfPurchase' => 'ASC']);
	}

	/**
	 * @param $id
	 * @throws CRUDException
	 */
	public function executeOrder($id)
	{
		$order = self::getOrder($id, false);
		$order->setProcessed(true);
		try {
			$this->entityManager->persist($order);
			$this->entityManager->flush();
		} catch (\Exception $e) {
			throw new CRUDException('Objednávku nelze vyřídit.');
		}
	}
}