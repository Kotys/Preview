<?php
/**
 * Copyright (c) 2016 Jan Kotrba https://jkotrba.net/
 */

namespace App\AdminModule\Presenters;

use App\AdminModule\Components\AddResourceFormFactory;
use App\AdminModule\Components\EditResourceFormFactory;
use App\AdminModule\Model\AdminBaguettesFacade;
use Nette;

/**
 * Class ResourcesPresenter
 * @package App\AdminModule\Presenters
 */
class ResourcesPresenter extends SecuredPresenter
{

	/** @var AddResourceFormFactory @inject */
	public $addResourceFormFactory;

	/** @var EditResourceFormFactory @inject */
	public $editResourceFormFactory;

	/**
	 * @var AdminBaguettesFacade
	 */
	private $baguettesFacade;

	/**
	 * BaguettesPresenter constructor.
	 * @param AdminBaguettesFacade $baguettesFacade
	 */
	public function __construct(AdminBaguettesFacade $baguettesFacade)
	{
		parent::__construct();
		$this->baguettesFacade = $baguettesFacade;
	}

	/**
	 * @param null $resource
	 */
	public function actionResource($resource = null)
	{
		if ($resource === null) {
			$this->redirect('this', 'pecivo');
		}

		$resourcesTable = [
			'pecivo' => [
				'type' => 'baked-goods',
				'text' => 'pečivo',
			],
			'napln' => [
				'type' => 'filling',
				'text' => 'náplň',
			],
			'zelenina' => [
				'type' => 'vegetable',
				'text' => 'zelenina',
			],
			'dresink' => [
				'type' => 'dressing',
				'text' => 'dresink',
			],
		];

		$this->template->resource = $resourcesTable[$resource];
		$this->template->resources = $this->baguettesFacade->getAllResourcesByType($resourcesTable[$resource]['type']);
	}

	/**
	 * @var
	 */
	public $resource;

	/**
	 * @param $resourceId
	 */
	public function actionEdit($resourceId)
	{
		$resource = $this->baguettesFacade->getResource(['id' => $resourceId]);
		if (!$resource) {
			$this->flashMessage('Tato surovina neexistuje.', 'danger');
			$this->redirect('Baguettes:resource');
		}
		$this->template->resource = $this->resource = $resource;
	}

	/**
	 * @param $resourceId
	 */
	public function handleRemove($resourceId)
	{
		$resource = $this->baguettesFacade->getResource(['id' => $resourceId]);

		if (!$resource) {
			$this->flashMessage('Surovina neexistuje.', 'danger');
			$this->redirect('Resources:resource', ['pecivo']);
		}

		$this->baguettesFacade->disable($resource);

		$this->flashMessage('Surovina <b>' . $resource->getName() . '</b> byla odstraněna.', 'success');
		$this->redirect('this');
	}

	/**
	 * @return mixed
	 */
	public function createComponentAddResourceForm()
	{
		return $this->addResourceFormFactory->create();
	}

	/**
	 * @return Nette\Application\UI\Form
	 */
	public function createComponentEditResourceForm()
	{
		$factory = $this->editResourceFormFactory;

		$form = $factory->setResource($this->resource);

		return $form->create();
	}
}