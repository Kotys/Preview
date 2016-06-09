<?php
/**
 * Copyright (c) 2016 Jan Kotrba https://jkotrba.net/
 */

namespace App\AdminModule\Components;

use App\Model\Authenticator;
use Nette;
use Nette\Application\UI\Form;
use Nette\Security as NS;

/**
 * Class AdminLoginFormFactory
 * @package App\AdminModule\Components
 */
class AdminLoginFormFactory extends Nette\Object
{

	/**
	 * @var Authenticator
	 */
	private $authenticator;

	/**
	 * AdminLoginFormFactory constructor.
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
			->setRequired('Musíte zadat email..');

		$form->addPassword('password')
			->setRequired('Musíte zadat heslo..');

		$form->addSubmit('sign');

		$form->onSuccess[] = [$this, 'adminLoginFormSuccess'];
		return $form;
	}

	/**
	 * @param Form $form
	 * @param $values
	 */
	public function adminLoginFormSuccess(Form $form, $values)
	{
		$presenter = $form->getPresenter();

		try {
			$presenter->getUser()->login('admin', $values->email, $values->password);
			$presenter->flashMessage('Vítejte ve správě eshopu JAVO Baguettes.', 'success');
			$presenter->redirect('Dashboard:default');
		} catch (NS\AuthenticationException $e) {
			$presenter->flashMessage($e->getMessage(), 'danger');
		}
	}
}