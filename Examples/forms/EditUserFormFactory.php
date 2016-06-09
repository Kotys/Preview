<?php
/**
 * @author Jan Kotrba <jan.kotrbaa@gmail.com>
 */

namespace App\Admin\Components;

use App\Admin\Model\Entities\User;
use App\Admin\Model\SaveUserException;
use App\Admin\Model\UserManager;
use Doctrine\ORM\ORMException;
use Nette;
use Nette\Application\UI\Form;
use Tracy\Debugger;

/**
 * Class EditUserFormFactory
 * @package App\Admin\Components
 */
class EditUserFormFactory extends Nette\Object
{

	/**
	 * @var User
	 */
	private $userDao;

	/**
	 * @var UserManager
	 */
	private $userManager;

	/**
	 * EditUserFormFactory constructor.
	 * @param UserManager $userManager
	 */
	public function __construct(UserManager $userManager)
	{
		$this->userManager = $userManager;

	}

	/**
	 * @param User $user
	 */
	public function setUserDao(User $user)
	{
		$this->userDao = $user;
	}

	/**
	 * @return Form
	 */
	public function create()
	{
		$form = new Form();

		$form->addText('firstname')
			->setValue($this->userDao->getFirstName())
			->setRequired();

		$form->addText('surename')
			->setValue($this->userDao->getSureName())
			->setRequired();

		$form->addText('email')
			->setValue($this->userDao->getEmail())
			->setRequired();

		$form->addSelect('role', null, ['user' => 'Uživatel', 'admin' => 'Správce'])->setDefaultValue($this->userDao->getRole());

		$form->addSubmit('save');

		$form->onSuccess[] = [$this, 'editUserFormSuccess'];

		return $form;
	}

	/**
	 * @param Form $form
	 * @param $values
	 */
	public function editUserFormSuccess(Form $form, $values)
	{
		$this->userDao->setFirstName($values->firstname);
		$this->userDao->setSureName($values->surename);
		$this->userDao->setEmail($values->email);
		$this->userDao->setRole($values->role);

		try {
			$this->userManager->save($this->userDao);
			$form->getPresenter()->flashMessage('Změny byly uloženy.', 'success');
		} catch (SaveUserException $e) {
			$form->getPresenter()->flashMessage($e->getMessage(), 'success');
		}
		$form->getPresenter()->redirect('this');
	}
}