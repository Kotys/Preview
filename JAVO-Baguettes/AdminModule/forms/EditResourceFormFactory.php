<?php
/**
 * Copyright (c) 2016 Jan Kotrba https://jkotrba.net/
 */

namespace App\AdminModule\Components;

use App\Admin\Model\Entities\Allergen;
use App\AdminModule\Model\AdminBaguettesFacade;
use App\AdminModule\Model\AllergensManager;
use App\AdminModule\Model\CRUDException;
use App\AdminModule\Model\Entities\BaguetteResource;
use Nette;
use Nette\Application\UI\Form;

/**
 * Class AddResourceFormFactory
 * @package App\AdminModule\Components
 */
class EditResourceFormFactory extends Nette\Object
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
	 * @var BaguetteResource
	 */
	protected $resource;


	/**
	 * EditResourceFormFactory constructor.
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
	}


	/**
	 * @param BaguetteResource $resource
	 * @return $this
	 */
	public function setResource(BaguetteResource $resource)
	{
		$this->resource = $resource;
		return $this;
	}

	/**
	 * @return Form
	 */
	public function create()
	{

		$form = new Form();

		$form->addText('name')
			->setValue($this->resource->getName())
			->setRequired('Musíte zadat název.');

		$form->addText('price')
			->setValue($this->resource->getPrice())
			->setRequired('Musíte zadat cenu.');

		$form->addRadioList('group', null, $this->baguettesFacade->getGroups())
			->setDefaultValue($this->resource->getType());

		ksort($this->allergens);

		if (count($this->resource->getAllergensArray()) >= 1) {
			$form->addCheckboxList('allergens', null, $this->allergens)->setValue($this->resource->getAllergensArray());
		} else {
			$form->addCheckboxList('allergens', null, $this->allergens);
		}

		$form->addSubmit('save');

		$form->onSuccess[] = [$this, 'editResourceFormSuccess'];
		return $form;
	}

	/**
	 * @param Form $form
	 * @param $values
	 */
	public function editResourceFormSuccess(Form $form, $values)
	{
		$presenter = $form->getPresenter();

		try {
			$this->baguettesFacade->updateResource($this->resource, $values->group, $values->name, $values->price, $values->allergens);
			$presenter->flashMessage('Surovina <b>' . $this->resource->getName() . '</b> upravena.', 'success');
			$presenter->redirect('Resources:resource', Nette\Utils\Strings::webalize($this->baguettesFacade->getGroups()[$this->resource->getType()]));
		} catch (CRUDException $e) {
			$presenter->flashMessage($e->getMessage(), 'danger');
		}

	}
}