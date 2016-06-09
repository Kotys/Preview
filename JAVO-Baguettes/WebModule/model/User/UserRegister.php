<?php
/**
 * Copyright (c) 2016 Jan Kotrba https://jkotrba.net/
 */

/**
 * Created by PhpStorm.
 * User: Koty
 * Date: 21.10.2015
 * Time: 19:49
 */

namespace App\WebModule\Model;

use App\Model\AddressDao;
use App\Model\Entities\User;
use Nette\Security as NS;
use Doctrine\ORM\EntityManager;
use Nette;

/**
 * Class UserRegister
 * @package App\WebModule\Model
 */
class UserRegister extends Nette\Object
{

	/**
	 * @var EntityManager
	 */
	private $entityManager;

	/**
	 * @var DistanceManager
	 */
	private $distanceManager;


	/**
	 * UserRegister constructor.
	 * @param EntityManager $entityManager
	 * @param DistanceManager $distanceManager
	 */
	function __construct(EntityManager $entityManager, DistanceManager $distanceManager)
	{
		$this->entityManager = $entityManager;
		$this->distanceManager = $distanceManager;
	}


	/**
	 * @param Nette\Utils\ArrayHash $credentials
	 * @return bool
	 * @throws RegisterFailedException
	 */
	public function register(Nette\Utils\ArrayHash $credentials)
	{
		$existingUser = $this->entityManager->getRepository(User::getClassName())->findOneBy([
			'email' => $credentials->email,
		]);

		if ($existingUser) {
			throw new RegisterFailedException('Tento email již je registrován.');
		}

		try {
			$addressDao = new AddressDao($credentials->town, $credentials->psc, $credentials->street, $credentials->province);
			$this->distanceManager->verifyAddress($addressDao);
		} catch (DistanceManagerException $e) {
			throw new RegisterFailedException($e->getMessage());
		}

		$newUser = new User();
		$newUser->setFirstName($credentials->firstName);
		$newUser->setLastName($credentials->lastName);
		$newUser->setEmail($credentials->email);
		$newUser->setTown($credentials->town);
		$newUser->setStreet($credentials->street);
		$newUser->setPsc($credentials->psc);
		$newUser->setProvince($credentials->province);
		$newUser->setPhone($credentials->phone);
		$newUser->setPassword($credentials->password);

		try {
			$this->entityManager->persist($newUser);
			$this->entityManager->flush();
		} catch (\Exception $e) {
			throw new RegisterFailedException('Registrace selhala');
		}

		return true;
	}

}

/**
 * Class RegisterException
 * @package App\WebModule\Model
 */
class RegisterException extends \Exception
{
}

/**
 * Class RegisterFailedException
 * @package App\WebModule\Model
 */
class RegisterFailedException extends RegisterException
{
}