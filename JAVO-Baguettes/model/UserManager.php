<?php
/**
 * Copyright (c) 2016 Jan Kotrba https://jkotrba.net/
 */

namespace App\WebModule\Model;

use Doctrine\ORM\EntityManager;
use App\Model\Entities\User;
use Doctrine\ORM\ORMException;
use Nette;

/**
 * Class UserManager
 * @package App\Model
 */
class UserManager extends Nette\Object
{
	/**
	 * @var EntityManager
	 */
	private $entityManager;

	/**
	 * @var \Doctrine\ORM\EntityRepository
	 */
	private $repository;

	/**
	 * UserManager constructor.
	 * @param EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
		$this->repository = $entityManager->getRepository(User::getClassName());
	}

	/**
	 * @param $id
	 * @return null|object
	 */
	public function getUser($id)
	{
		return $this->repository->find($id);
	}

	/**
	 * @param array $criteria
	 * @return null|object
	 */
	public function findOneBy(array $criteria)
	{
		return $this->repository->findOneBy($criteria);
	}

	/**
	 * @param $group
	 * @return array
	 */
	public function getGroupMembers($group)
	{
		return $this->entityManager->getRepository(User::getClassName())->findBy(['role' => $group]);
	}

	/**
	 * @param User $user
	 * @param $newGroup
	 * @throws UserManagerException
	 */
	public function switchGroup(User $user, $newGroup)
	{
		$user->setRole($newGroup);
		try {
			$this->entityManager->persist($user);
			$this->entityManager->flush();
		} catch (ORMException $e) {
			throw new UserManagerException('Změna uživatelské skupiny selhala.');
		}
	}

	/**
	 * @param $old
	 * @param $new
	 * @param $new2
	 * @param $userId
	 * @throws UserManagerException
	 */
	public function changePassword($old, $new, $new2, $userId)
	{
		/** @var User $user */
		$user = $this->getUser($userId);

		if (!Nette\Security\Passwords::verify($old, $user->getPassword())) {
			throw new UserManagerException('Zadali jste špatné současné heslo.');
		}

		if ($new == $new2) {
			$user->setPassword($new);
			try {
				$this->entityManager->persist($user);
				$this->entityManager->flush();
			} catch (ORMException $e) {
				throw new UserManagerException('Nepodařilo se změnit heslo.');
			}
		} else {
			throw new UserManagerException('Nové hesla se musí schodovat!');
		}
	}
}

/**
 * Class UserManagerException
 * @package App\WebModule\Model
 */
class UserManagerException extends \Exception
{
}