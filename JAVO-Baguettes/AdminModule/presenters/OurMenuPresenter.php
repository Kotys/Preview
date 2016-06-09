<?php
/**
 * Copyright (c) 2016 Jan Kotrba https://jkotrba.net/
 */

namespace App\AdminModule\Presenters;

use App\AdminModule\Components\AddToMenuFormFactory;
use App\AdminModule\Model\AdminBaguettesFacade;
use App\AdminModule\Model\CRUDException;
use Nette;

/**
 * Class BaguettesPresenter
 * @package App\AdminModule\Presenters
 */
class OurMenuPresenter extends SecuredPresenter
{
	/** @var AddToMenuFormFactory @inject */
	public $addToMenuFormFactory;

	/**
	 * @var AdminBaguettesFacade
	 */
	private $baguettesFacade;

	/**
	 * OurMenuPresenter constructor.
	 * @param AdminBaguettesFacade $baguettesFacade
	 */
	public function __construct(AdminBaguettesFacade $baguettesFacade)
	{
		parent::__construct();
		$this->baguettesFacade = $baguettesFacade;
	}

	/**
	 *
	 */
	public function actionAll()
	{
		$this->template->baguettes = $this->baguettesFacade->getAllBaguettes();
	}


	/**
	 * @param $id
	 */
	public function handleRemove($id)
	{
		$baguette = $this->baguettesFacade->getBaguetteById($id);
		if (!$baguette) {
			$this->flashMessage('Bageta neexistuje.', 'danger');
			$this->redirect('this');
		}

		try {
			$this->baguettesFacade->removeBaguette($baguette);
		} catch (CRUDException $e) {
			$this->flashMessage('Bageta smazána.', 'success');
		}
		
		$this->redirect('this');
	}

	/**
	 * @return Nette\Application\UI\Form
	 */
	public function createComponentAddToMenuForm()
	{
		return $this->addToMenuFormFactory->create();
	}
}