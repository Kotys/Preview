<?php
/**
 * Copyright (c) 2016 Jan Kotrba https://jkotrba.net/
 */

namespace App\WebModule\Presenters;

use App\AdminModule\Model\Entities\BaguetteResource;
use App\WebModule\Model\BaguetteBuilder;
use App\WebModule\Model\BaguetteFacade;
use App\WebModule\Model\CartManager;
use App\WebModule\Model\DistanceManager;
use Nette\Application\UI\Form;
use Nette;
use Nette\Utils\Paginator;
use Tracy\Debugger;

/**
 * Class BuilderPresenter
 * @package App\WebModule\Presenters
 */
class BuilderPresenter extends BasePresenter
{

	/**
	 * @var Paginator
	 */
	private $nav;

	/**
	 * @var BaguetteFacade
	 */
	private $baguetteFacade;

	/**
	 * BuilderPresenter constructor.
	 * @param DistanceManager $distanceManager
	 * @param CartManager $cart
	 * @param BaguetteBuilder $baguetteBuilder
	 * @param BaguetteFacade $baguetteFacade
	 */
	public function __construct(DistanceManager $distanceManager, CartManager $cart, BaguetteBuilder $baguetteBuilder, BaguetteFacade $baguetteFacade)
	{
		parent::__construct($distanceManager, $cart, $baguetteBuilder);
		$this->baguetteFacade = $baguetteFacade;
		Debugger::barDump($this->builder->getCustomBaguette());
	}

	/**
	 * Redirect, or create new builder
	 */
	public function actionDefault()
	{
		if (!$this->builder->isClean()) {
			if ($this->builder->getCustomBaguette()->getBakedGoods()) {
				$this->redirect('Builder:compilation', 5);
			} else {
				$this->redirect('Builder:compilation', 1);
			}
		} else {
			$this->builder->initBuilder();
		}
	}

	/**
	 * @param $step
	 */
	public function actionCompilation($step)
	{
		if ($this->builder->isClean()) {
			$this->redirect('Builder:default');
		}

		if (!$this->builder->getCustomBaguette()->getBakedGoods() AND $step != 1) {
			$this->flashMessage('Nejprve musíte vybrat pečivo.');
			$this->redirect('Builder:compilation', 1);
		}

		if ($step == 5) {
			$this->template->totalPrice = $this->builder->getCustomBaguette()->getPrice();
		}

		$nav = new Paginator();
		$nav->setBase(1);
		$nav->setItemCount(5);
		$nav->setItemsPerPage(1);
		$nav->setPage($step);

		$this->nav = $this->template->nav = $nav;
		$this->presenter->setView('step-' . $step);

		if ($step == 5) {
			$this->template->customBaguette = $this->builder->getCustomBaguette();
		}
	}

	/**
	 *
	 */
	public function handleAddToCart()
	{
		$custom = $this->builder->getCustomBaguette();
		$this->cart->addCustomBaguette($custom);
		$this->builder->clean();

		$this->flashMessage('Přidáno do košíku.', 'success');
		$this->redirect('Cart:default');
	}

	/**
	 * Reset builder
	 */
	public function handleResetBuilder()
	{
		$this->builder->clean();
		$this->redirect('Builder:default');
	}

	/**
	 * Skip to prev step
	 */
	public function handlePrevStep()
	{
		$prev = $this->nav->getPage() - 1;
		($prev == 0) ? $this->redirect('Builder:default') : $this->redirect('Builder:compilation', $prev);
	}

	/**
	 * Skip to next step
	 */
	public function handleNextStep()
	{
		$next = $this->nav->getPage() + 1;
		($next <= 5) ? $this->redirect('Builder:compilation', $next) : null;
	}

	/**
	 * @return Nette\Application\UI\Form
	 */
	public function createComponentSelectBakedGoods()
	{
		/** @var BaguetteResource $default */
		$default = $this->builder->getCustomBaguette()->getBakedGoods();
		$options = $this->baguetteFacade->getResourcesArrayByType('baked-goods');

		$form = new Form();

		if ($default) {
			$form->addRadioList("bakedGoods", null, $options)
				->setValue($default->getId())
				->setRequired('Musíte si vybrat pečivo.');
		} else {
			$form->addRadioList("bakedGoods", null, $options)
				->setRequired('Musíte si vybrat pečivo.');
		}

		$form->onSubmit[] = [$this, 'selectBakedGoodsComplete'];

		return $form;
	}

	/**
	 * @param Form $form
	 */
	public function selectBakedGoodsComplete(Form $form)
	{
		$values = $form->getValues();
		$this->builder->addResource('baked-goods', [$values->bakedGoods]);
	}

	/**
	 * @return Nette\Application\UI\Form
	 */
	public function createComponentSelectFilling()
	{

		$default = $this->builder->getCustomBaguette()->getFillingsIdArray();
		$options = $this->baguetteFacade->getResourcesArrayByType('filling');

		$form = new Form();
		if ($default) {
			$form->addCheckboxList("filling", null, $options)->setValue($default);
		} else {
			$form->addCheckboxList("filling", null, $options);
		}

		$form->onSubmit[] = [$this, 'selectFillingComplete'];

		return $form;
	}

	/**
	 * @param Form $form
	 */
	public function selectFillingComplete(Form $form)
	{
		$values = $form->getValues();
		$this->builder->addResource('filling', $values->filling);
	}

	/**
	 * @return Nette\Application\UI\Form
	 */
	public function createComponentSelectVegetable()
	{
		$default = $this->builder->getCustomBaguette()->getVegetablesIdArray();
		$options = $this->baguetteFacade->getResourcesArrayByType('vegetable');

		$form = new Form();
		if ($default) {
			$form->addCheckboxList("vegetable", null, $options)->setValue($default);
		} else {
			$form->addCheckboxList("vegetable", null, $options);
		}

		$form->onSubmit[] = [$this, 'selectVegetableComplete'];

		return $form;
	}

	/**
	 * @param Form $form
	 */
	public function selectVegetableComplete(Form $form)
	{
		$values = $form->getValues();
		$this->builder->addResource('veegtable', $values->vegetable);
	}

	/**
	 * @return Nette\Application\UI\Form
	 */
	public function createComponentSelectDressing()
	{
		$default = $this->builder->getCustomBaguette()->getDressingIdArray();
		$options = $this->baguetteFacade->getResourcesArrayByType('dressing');

		$form = new Form();
		if ($default) {
			$form->addCheckboxList("dressing", null, $options)->setValue($default);
		} else {
			$form->addCheckboxList("dressing", null, $options);
		}

		$form->onSubmit[] = [$this, 'selectDressingComplete'];

		return $form;
	}

	/**
	 * @param Form $form
	 */
	public function selectDressingComplete(Form $form)
	{
		$values = $form->getValues();
		$this->builder->addResource('dressing', $values->dressing);
	}
}