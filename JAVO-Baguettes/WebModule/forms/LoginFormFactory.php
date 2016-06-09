<?php
/**
 * Copyright (c) 2016 Jan Kotrba https://jkotrba.net/
 */

namespace App\WebModule\Components;

use Nette;
use Nette\Application\UI\Form;
use App\Model\Authenticator;

/**
 * Class LoginFormFactory
 * @package App\WebModule\Components
 */
class LoginFormFactory extends Nette\Object
{

	/**
	 * @var Authenticator
	 */
	private $authenticator;

	/**
	 * LoginFormFactory constructor.
	 * @param Authenticator $authenticator
	 */
	public function __construct(Authenticator $authenticator)
	{
		$this->authenticator = $authenticator;
	}

	/**
	 * @return Form
	 */
	public function create()
	{

		$form = new Form();

		$form->addText('email')
			->addRule(Form::EMAIL, 'Email není ve správném formátu.')
			->setRequired('Musíte zadat svůj email.');

		$form->addPassword('password')
			->setRequired('Musíte zadat své heslo.');

		$form->addSubmit('submit');

		$form->onSuccess[] = [$this, 'loginFormSuccess'];

		return $form;
	}

	/**
	 * @param Form $form
	 * @param $values
	 */
	public function loginFormSuccess(Form $form, $values)
	{
		$presenter = $form->getPresenter();

		try {
			$presenter->getUser()->login('customer', $values->email, $values->password);
		} catch (Nette\Security\AuthenticationException $e) {
			$presenter->template->loginStatus = (object)['type' => 'danger', 'message' => $e->getMessage()];
			$presenter->redrawControl('loginStatus');
			return;
		}

		$presenter->flashMessage('Přihlášeno.', 'success');
		$presenter->redirect('this');
	}
}