<?php
/**
 * Copyright (c) 2016 Jan Kotrba https://jkotrba.net/
 */

namespace App\AdminModule\Presenters;

use App\WebModule\Model\UserManager;
use App\WebModule\Model\UserManagerException;
use Nette;

/**
 * Class UsersPresenter
 * @package App\AdminModule\Presenters
 */
class UsersPresenter extends SecuredPresenter
{
	/**
	 * @var UserManager
	 */
	private $userManager;

	/**
	 * UsersPresenter constructor.
	 * @param UserManager $userManager
	 */
	public function __construct(UserManager $userManager)
	{
		parent::__construct();
		$this->userManager = $userManager;
	}

	/**
	 * @param null $role
	 */
	public function actionAll($role = null)
	{
		if ($role === null) {
			$this->redirect('this', 'zakaznici');
		}
		switch ($role) {
			case 'zakaznici':
				$this->template->role = "Zákaznící";
				$this->template->users = $this->userManager->getGroupMembers('customer');
				break;
			case 'spravci':
				$this->template->role = "Správci";
				$this->template->users = $this->userManager->getGroupMembers('admin');
				break;
		}
	}

	/**
	 * @param $userId
	 * @param $newGroup
	 */
	public function handleSwitchGroup($userId, $newGroup)
	{
		$user = $this->userManager->getUser($userId);
		if (!$user) {
			$this->flashMessage('Tento uživatel neexistuje.', 'danger');
			$this->redirect('this');
		}

		try {
			$this->userManager->switchGroup($user, $newGroup);
			$this->flashMessage('Změna proběhla úspěšně.', 'success');
			$this->redirect('this');
		} catch (UserManagerException $e) {
			$this->flashMessage($e->getMessage(), 'danger');
			$this->redirect('this');
		}
	}
}