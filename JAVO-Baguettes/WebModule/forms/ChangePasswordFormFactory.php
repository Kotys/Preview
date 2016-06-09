<?php
/**
 * Copyright (c) 2016 Jan Kotrba https://jkotrba.net/
 */

namespace App\WebModule\Components;

use App\WebModule\Model\UserManager;
use App\WebModule\Model\UserManagerException;
use Nette;
use Nette\Application\UI\Form;

/**
 * Class ChangePasswordFormFactory
 * @package App\WebModule\Components
 */
class ChangePasswordFormFactory extends Nette\Object
{

	/**
	 * @var UserManager
	 */
	private $userManager;

	/**
	 * @var Nette\Security\User
	 */
	private $user;

	/**
	 * ChangePasswordFormFactory constructor.
	 * @param UserManager $userManager
	 * @param Nette\Security\User $user
	 */
	public function __construct(UserManager $userManager, Nette\Security\User $user)
	{
		$this->userManager = $userManager;
		$this->user = $user;
	}

	/**
	 * @return Form
	 */
	public function create()
	{
		$form = new Form();

		$form->addPassword('currentPass')
			->setRequired('Musíte zadat vaše současné heslo.');

		$form->addPassword('newPass')
			->setRequired('Musíte zadat nové heslo')
			->addRule(Form::MIN_LENGTH, 'Heslo musí obsahovat alespoň %d znaků.', 8);

		$form->addPassword('newPassCheck')
			->setRequired('Musíte zadat heslo pro kontrolu.')
			->addRule(Form::EQUAL, 'Hesla se musejí schodovat.', $form['newPass']);

		$form->addSubmit('save');

		$form->onSuccess[] = [$this, 'changePasswordFormSuccess'];
		return $form;
	}

	/**
	 * @param Form $form
	 * @param $values
	 */
	public function changePasswordFormSuccess(Form $form, $values)
	{
		$presenter = $form->getPresenter();

		try {
			$this->userManager->changePassword($values->currentPass, $values->newPass, $values->newPassCheck, $this->user->getId());
			$presenter->flashMessage('Heslo bylo změněno.', 'success');
		} catch (UserManagerException $e) {
			$presenter->flashMessage($e->getMessage(), 'danger');
		}
	}
}