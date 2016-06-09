<?php
/**
 * Copyright (c) 2016 Jan Kotrba https://jkotrba.net/
 */

namespace App\WebModule\Components;

use App\Model\AddressDao;
use App\WebModule\Model\DistanceManager;
use App\WebModule\Model\DistanceManagerException;
use App\WebModule\Model\RegisterFailedException;
use App\WebModule\Model\UserRegister;
use Nette;
use Nette\Application\UI\Form;

/**
 * Class RegisterFormFactory
 * @package App\WebModule\Components
 */
class RegisterFormFactory extends Nette\Object
{
	/**
	 * @var UserRegister
	 */
	private $userRegister;

	/**
	 * @var DistanceManager
	 */
	private $distanceManager;

	/**
	 * RegisterFormFactory constructor.
	 * @param UserRegister $userRegister
	 * @param DistanceManager $distanceManager
	 */
	public function __construct(UserRegister $userRegister, DistanceManager $distanceManager)
	{
		$this->userRegister = $userRegister;
		$this->distanceManager = $distanceManager;
	}

	/**
	 * @return Form
	 */
	public function create()
	{
		$form = new Form();

		$form->addText('firstName')
			->setRequired('Musíte zadat vaše jméno.');

		$form->addText('lastName')
			->setRequired('Musíte zadat vaše příjmení.');

		$form->addText('email')
			->addRule(Form::EMAIL, 'Email není ve správném formátu.')
			->setRequired('Musíte zadat svůj email.');

		$form->addText('phone')
			->addRule(Form::NUMERIC, 'Telefon není ve správném formátu.')
			->setRequired('Musíte zadat své číslo.');

		$form->addPassword('password')
			->addRule(Form::MIN_LENGTH, 'Heslo musí obsahovat alespoň %d znaků.', 8);

		$form->addPassword('password_check')
			->addRule(Form::EQUAL, 'Hesla se musejí schodovat.', $form['password']);

		$form->addText('town')
			->setRequired('Musíte zadat město.');

		$form->addText('street')
			->setRequired('Musíte zadat ulici.');

		$form->addText('psc')
			->setRequired('Musíte zadat PSČ.');

		$form->addText('province')
			->setValue('Česká Republika')
			->setRequired('Musíte zadat stát, ve kterém žijete.');

		$form->addSubmit('submit');

		$form->onSuccess[] = [$this, 'registerFormSuccess'];

		return $form;
	}

	/**
	 * @param Form $form
	 * @param Nette\Utils\ArrayHash $values
	 */
	public function registerFormSuccess(Form $form, Nette\Utils\ArrayHash $values)
	{
		$presenter = $form->getPresenter();

		/**
		 * Verify address
		 */
		try {
			$this->distanceManager->verifyAddress(new AddressDao($values->town, $values->psc, $values->street, $values->province));
		} catch (DistanceManagerException $e) {
			$presenter->template->registerStatus = (object)['type' => 'danger', 'message' => $e->getMessage()];
			$presenter->redrawControl('registerStatus');
			return;
		}

		/**
		 * Register new user
		 */
		try {
			$this->userRegister->register($values);
		} catch (RegisterFailedException $e) {
			$presenter->template->registerStatus = (object)['type' => 'danger', 'message' => $e->getMessage()];
			$presenter->redrawControl('registerStatus');
			return;
		}

		/**
		 * Auto-login after registration success
		 */
		try {
			$presenter->getUser()->login('customer', $values->email, $values->password);
		} catch (Nette\Security\AuthenticationException $e) {
			$presenter->template->loginStatus = (object)['type' => 'danger', 'message' => $e->getMessage()];
			$presenter->redrawControl('loginStatus');
			return;
		}

		$presenter->flashMessage('Vaše registrace proběhla úspěšně, nyní jste přihlášení na vašem novém účtu.', 'success');
		$presenter->redirect('this');
	}
}