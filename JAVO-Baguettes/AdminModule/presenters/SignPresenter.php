<?php
/**
 * Copyright (c) 2016 Jan Kotrba https://jkotrba.net/
 */

namespace App\AdminModule\Presenters;

use App\AdminModule\Components\AdminLoginFormFactory;
use Nette;

/**
 * Class SignPresenter
 * @package App\AdminModule\Presenters
 */
class SignPresenter extends BasePresenter
{

	/** @var AdminLoginFormFactory @inject */
	public $adminLoginFormFactory;

	/**
	 * @return Nette\Application\UI\Form
	 */
	public function createComponentAdminLoginForm()
	{
		return $this->adminLoginFormFactory->create();
	}
}