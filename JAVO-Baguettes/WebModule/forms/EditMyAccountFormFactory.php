<?php
/**
 * Copyright (c) 2016 Jan Kotrba https://jkotrba.net/
 */

namespace App\WebModule\Components;

use App\WebModule\Model\DistanceManager;
use App\WebModule\Model\NoResponseException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Nette;
use Nette\Application\UI\Form;
use App\Model\Entities\User;
use App\Model\AddressDao;
use App\WebModule\Model\DistanceManagerException;

/**
 * Class EditMyAccountFormFactory
 * @package App\WebModule\Components
 */
class EditMyAccountFormFactory extends Nette\Object
{

	/** @var User */
	private $userDao;

	/**
	 * @var EntityManager
	 */
	private $entityManager;

	/**
	 * @var DistanceManager
	 */
	private $distanceManager;

	/**
	 * EditMyAccountFormFactory constructor.
	 * @param EntityManager $entityManager
	 * @param Nette\Security\User $user
	 * @param DistanceManager $distanceManager
	 */
	public function __construct(EntityManager $entityManager, Nette\Security\User $user, DistanceManager $distanceManager)
	{
		$this->entityManager = $entityManager;
		$this->distanceManager = $distanceManager;

		$this->userDao = $this->entityManager->getRepository(User::getClassName())->findOneBy(['id' => $user->getId()]);
	}

	/**
	 * @return Form
	 */
	public function create()
	{

		$form = new Form();

		$form->addText('firstName')
			->setValue($this->userDao->getFirstName())
			->setRequired('Musíte zadat vaše jméno.');

		$form->addText('lastName')
			->setValue($this->userDao->getLastName())
			->setRequired('Musíte zadat vaše příjmení.');

		$form->addText('email')
			->setValue($this->userDao->getEmail())
			->addRule(Form::EMAIL, 'Email není ve správném formátu.')
			->setRequired('Musíte zadat svůj email.');

		$form->addText('phone')
			->setValue($this->userDao->getPhone())
			->addRule(Form::NUMERIC, 'Email není ve správném formátu.')
			->setRequired('Musíte zadat své číslo.');

		$form->addText('town')
			->setValue($this->userDao->getTown())
			->setRequired('Musíte zadat město.');

		$form->addText('street')
			->setValue($this->userDao->getStreet())
			->setRequired('Musíte zadat ulici.');

		$form->addText('psc')
			->setValue($this->userDao->getPsc())
			->setRequired('Musíte zadat PSČ.');

		$form->addText('province')
			->setValue($this->userDao->getProvince())
			->setRequired('Musíte zadat stát, ve kterém žijete.');

		$form->addSubmit('save');

		$form->onSuccess[] = [$this, 'editMyAccountFormSuccess'];

		return $form;
	}

	/**
	 * @param Form $form
	 * @param $values
	 */
	public function editMyAccountFormSuccess(Form $form, $values)
	{
		$this->userDao->setFirstName($values->firstName);
		$this->userDao->setLastName($values->lastName);
		$this->userDao->setEmail($values->email);
		$this->userDao->setPhone($values->phone);
		$this->userDao->setTown($values->town);
		$this->userDao->setStreet($values->street);
		$this->userDao->setPSC($values->psc);
		$this->userDao->setProvince($values->province);

		$presenter = $form->getPresenter();

		try {
			$addressDao = new AddressDao($values->town, $values->psc, $values->street, $values->province);
			$this->distanceManager->verifyAddress($addressDao);
		} catch (NoResponseException $e) {
			$presenter->flashMessage($e->getMessage(), 'info');
		} catch (DistanceManagerException $e) {
			$presenter->flashMessage($e->getMessage() . ' Doporučujeme zadat takovou, se kterou si poradíme.', 'info');
		}

		try {
			$this->entityManager->persist($this->userDao);
			$this->entityManager->flush();

			$presenter->getUser()->getStorage()->setIdentity(new Nette\Security\Identity($this->userDao->getId(), $this->userDao->getRole(), [
				'email' => $this->userDao->getEmail(),
				'fullName' => $this->userDao->getFullName(),
				'addressDao' => $this->userDao->getAddressDao()
			]));

			$presenter->flashMessage('Změny vašeho účtu uloženy.', 'success');
		} catch (ORMException $e) {
			$presenter->flashMessage('Selhalo ukládání změn.', 'danger');
		}

		$presenter->redirect('this');
	}
}

/**
 * Class EditMyAccountException
 * @package App\WebModule\Components
 */
class EditMyAccountException extends \Exception
{
}