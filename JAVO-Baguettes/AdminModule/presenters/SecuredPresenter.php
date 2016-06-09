<?php
/**
 * Copyright (c) 2016 Jan Kotrba https://jkotrba.net/
 */

namespace App\AdminModule\Presenters;

use Nette;

/**
 * Class SecuredPresenter
 * @package App\AdminModule\Presenters
 */
class SecuredPresenter extends BasePresenter
{
	/**
	 *
	 */
	public function startup()
	{
		parent::startup();
		if (!$this->getUser()->isLoggedIn()) {
			$this->flashMessage('Vyžadováno přihlášení.', 'danger');
			$this->redirect('Sign:in');
		} else {
			if (!$this->getUser()->isInRole('admin')) {
				$this->flashMessage('Zákázník nemá právo do této sekce vstoupit!', 'danger');
				$this->redirect(':Web:Homepage:default');
			}
		}

		$this->template->userData = $this->getUser()->getIdentity()->getData();
	}

	/**
	 *
	 */
	public function handleLogOut()
	{
		$this->getUser()->logout(TRUE);
		$this->flashMessage('Odhlášeno.', 'success');
		$this->redirect('Sign:in');
	}
}