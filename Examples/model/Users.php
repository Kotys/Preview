<?php
/**
 * @author Jan Kotrba <kotrba@kotyslab.cz>
 */

namespace Mionet\Admin\Model;

use Doctrine\ORM\ORMException;
use Kdyby\Doctrine\EntityManager;
use Kdyby\Translation\Translator;
use Mionet\Entities\User;
use Nette;
use Tracy\Debugger;

/**
 * Class Users
 * @package Mionet\Admin\Model
 */
class Users extends BaseModel
{

	/**
	 * @var Groups
	 */
	private $groups;

	/**
	 * Users constructor.
	 * @param Translator $translator
	 * @param EntityManager $entityManager
	 * @param Groups $groups
	 */
	public function __construct(Translator $translator, EntityManager $entityManager, Groups $groups)
	{
		parent::__construct($translator, $entityManager);
		$this->repository = $this->em->getRepository(User::class);
		$this->groups = $groups;
	}

	/**
	 * @return array
	 */
	public function getAllUsers()
	{
		return $this->repository->findAll();
	}

	/**
	 * @param $email
	 * @param $password
	 * @param $firstName
	 * @param $lastName
	 * @throws UsersException
	 * @throws \Exception
	 */
	public function createRoot($email, $password, $firstName, $lastName)
	{
		$user = new User();
		$user->setEmail($email);
		$user->setPassword($password);
		$user->setFirstName($firstName);
		$user->setLastName($lastName);
		$user->setActivation(true);

		$role = $this->groups->findOneBy(['role' => 'root']);
		if (!$role) {
			Debugger::log('Cant find role ROOT');
			throw new UsersException($this->translator->translate('forms.create_root.failed'));
		}

		$user->setUserGroup($role);

		try {
			$this->em->persist($user);
			$this->em->flush();
		} catch (ORMException $e) {
			throw new UsersException($this->translator->translate('forms.create_root.failed'));
		}
	}


	/**
	 * @param $email
	 * @param $firstName
	 * @param $lastName
	 * @param $role
	 * @return User|mixed|null|object
	 * @throws UsersException
	 * @throws \Exception
	 */
	public function createUser($email, $firstName, $lastName, $role)
	{
		$user = $this->repository->findOneBy(['email' => $email]);
		if ($user) {
			throw new UsersException($this->translator->translate('forms.create_user.email_already_in_use'));
		}

		$user = new User();
		$user->setEmail($email);
		$user->setFirstName($firstName);
		$user->setLastName($lastName);
		$user->setActivation(false);
		$user->addToken();
		$user->setUserGroup($this->groups->find($role));

		try {
			$this->em->persist($user);
			$this->em->flush();
		} catch (ORMException $e) {
			throw new UsersException($this->translator->translate('forms.create_user.failed'));
		}

		return $user;
	}
}

/**
 * Class UsersException
 * @package Mionet\Admin\Model
 */
class UsersException extends \Exception
{
}