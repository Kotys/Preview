<?php
/**
 * Copyright (c) 2016 Jan Kotrba https://jkotrba.net/
 */

namespace App\Model;

use App\Model\Entities\User;
use App\WebModule\Model\DistanceManager;
use Nette\Security as NS;
use Doctrine\ORM\EntityManager;
use Nette;
use Tracy\Debugger;
use App\WebModule\Model\UserManager;
use Tracy\ILogger;

/**
 * Class UserAuth
 * @package App\WebModule\Model
 */
class Authenticator extends Nette\Object implements Nette\Security\IAuthenticator
{

	/**
	 * @var EntityManager
	 */
	private $distanceManager;


	/**
	 * @var UserManager
	 */
	private $userManager;

	/**
	 * Authenticator constructor.
	 * @param UserManager $userManager
	 * @param DistanceManager $distanceCache
	 */
	public function __construct(UserManager $userManager, DistanceManager $distanceCache)
	{
		$this->userManager = $userManager;
		$this->distanceManager = $distanceCache;
	}

	/**
	 * @param array $credentials
	 * @return NS\Identity
	 * @throws NS\AuthenticationException
	 */
	public function authenticate(array $credentials)
	{
		list($role, $email, $password) = $credentials;

		try {
			/** @var User $user */
			$user = $this->userManager->findOneBy(['email' => $email]);
		} catch (\Exception $e) {
			Debugger::log($e, ILogger::ERROR);
			throw new NS\AuthenticationException('Přihlašování selhalo.');
		}

		if (!$user OR !NS\Passwords::verify($password, $user->getPassword())) {
			throw new NS\AuthenticationException('Přihlašovácí údaje nejsou správné.');
		}

		try {
			$this->distanceManager->getDistanceTo($user->getAddressDao());
		} catch (\Exception $e) {
		}


		if (!in_array($role, $user->getRole())) {
			throw new NS\AuthenticationException('Nemáte oprávnění.');
		}

		return new NS\Identity($user->getId(), $user->getRole(), [
			'email' => $user->getEmail(),
			'fullName' => $user->getFullName(),
			'addressDao' => $user->getAddressDao(),
		]);
	}
}