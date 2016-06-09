<?php
/**
 * @author Jan Kotrba <kotrba@kotyslab.cz>
 */

namespace Mionet\Admin\Presenters;

use Kdyby\Translation\Translator;
use Mionet\Admin\Components\CreateAdminAccountFormFactory;
use Mionet\Admin\Model\Users;
use Nette;

/**
 * Class InstallPresenter
 * @package Mionet\Admin\Presenters
 */
class InstallPresenter extends Nette\Application\UI\Presenter
{
	/** @var CreateAdminAccountFormFactory @inject */
	public $createAdminAccountFormFactory;

	/**
	 * @var Users
	 */
	private $users;

	/**
	 * @var Translator
	 */
	private $translator;

	/**
	 * InstallPresenter constructor.
	 * @param Users $users
	 * @param Translator $translator
	 */
	function __construct(Users $users, Translator $translator)
	{
		parent::__construct();
		$this->users = $users;
		$this->translator = $translator;
	}

	/**
	 * If any user exists
	 */
	public function actionDefault()
	{
		if ($this->users->getAllUsers()) {
			$this->redirect('Sign:in');
		}
	}

	/**
	 * @return Nette\Application\UI\Form
	 */
	public function createComponentCreateAdminAccountForm()
	{
		$form = $this->createAdminAccountFormFactory->create();

		$form->onSuccess[] = function () {
			$this->flashMessage($this->translator->translate('forms.create_root.success'), 'success');
			$this->redirect('Sign:in');
		};

		$form->onError[] = function ($form) {
			foreach ($form->getErrors() as $error) {
				$this->flashMessage($error, 'danger');
			}
		};

		return $form;
	}
}
