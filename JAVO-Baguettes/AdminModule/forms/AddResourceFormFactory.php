<?php
/**
 * Copyright (c) 2016 Jan Kotrba https://jkotrba.net/
 */

namespace App\AdminModule\Components;

use App\Admin\Model\Entities\Allergen;
use App\AdminModule\Model\AdminBaguettesFacade;
use App\AdminModule\Model\AllergensManager;
use App\AdminModule\Model\CRUDException;
use Nette;
use Nette\Application\UI\Form;

/**
 * Class AddResourceFormFactory
 * @package App\AdminModule\Components
 */
class AddResourceFormFactory extends Nette\Object
{
	/**
	 * @var AllergensManager
	 */
	private $allergensManager;

	/**
	 * @var AdminBaguettesFacade
	 */
	private $baguettesFacade;

	/**
	 * @var array
	 */
	private $allergens = [];

	/**
	 * @var array
	 */
	private $groups;

	/**
	 * AddResourceFormFactory constructor.
	 * @param AllergensManager $allergensManager
	 * @param AdminBaguettesFacade $baguettesFacade
	 */
	public function __construct(AllergensManager $allergensManager, AdminBaguettesFacade $baguettesFacade)
	{
		$this->allergensManager = $allergensManager;
		$this->baguettesFacade = $baguettesFacade;

		/** @var Allergen $allergen */
		foreach ($allergensManager->getAll() as $allergen) {
			$this->allergens[$allergen->getGroup()] = $allergen->getName();
		}

		$this->groups = [
			'baked-goods' => 'Pečivo',
			'filling' => 'Náplň',
			'vegetable' => 'Zelenina',
			'dressing' => 'Dresink',
		];
	}

	/**
	 * @return Form
	 */
	public function create()
	{
		$form = new Form();

		$form->addText('name')
			->setRequired('Musíte zadat název.');

		$form->addText('price')
			->setRequired('Musíte zadat cenu.');

		$form->addRadioList('group', null, $this->groups);

		ksort($this->allergens);

		$form->addCheckboxList('allergens', null, $this->allergens);

		$form->addSubmit('add');

		$form->onSuccess[] = [$this, 'addResourceFormSuccess'];
		return $form;
	}

	/**
	 * @param Form $form
	 * @param $values
	 */
	public function addResourceFormSuccess(Form $form, $values)
	{
		$presenter = $form->getPresenter();

		try {
			$this->baguettesFacade->addResource($values->group, $values->name, $values->price, $values->allergens);
			$presenter->flashMessage('Surovina přidána.', 'success');
			$presenter->redirect('this');
		} catch (CRUDException $e) {
			$presenter->flashMessage($e->getMessage(), 'danger');
		}

	}
}