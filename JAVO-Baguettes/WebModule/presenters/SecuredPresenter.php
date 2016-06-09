<?php
/**
 * Copyright (c) 2016 Jan Kotrba https://jkotrba.net/
 */

namespace App\Presenters;

use App\WebModule\Presenters\BasePresenter;
use Nette;

/**
 * Class SecuredPresenter
 * @package App\Presenters
 */
class SecuredPresenter extends BasePresenter {

	/**
	 *
	 */
	public function startup()
	{
		parent::startup();
		if(!$this->getUser()->isLoggedIn()) {
			$this->flashMessage('Pro vstup na tuto stránku se musíte přihlásit.', 'info');
			$this->redirect('Homepage:default');
		}
	}
}