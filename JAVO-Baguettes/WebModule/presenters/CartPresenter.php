<?php
/**
 * Copyright (c) 2016 Jan Kotrba https://jkotrba.net/
 */

namespace App\WebModule\Presenters;

use App\WebModule\Components\SubmitOrderFormFactory;
use Nette;

/**
 * Class CartPresenter
 * @package App\WebModule\Presenters
 */
class CartPresenter extends BasePresenter
{

	/** @var SubmitOrderFormFactory @inject */
	public $submitOrderFormFactory;

	/**
	 *
	 */
	public function actionDefault()
	{
		$this->template->cart = $this->cart->getCart();

		$this->template->totalPrice = $this->cart->getPrice();
		$this->template->deliveryPrice = $this->cart->getDeliveryPrice();
		$this->template->baguettesPrice = $this->cart->getBaguettesPrice();
	}

	/**
	 * @param $row
	 * @param $type
	 */
	public function handleRemoveFromCart($row, $type)
	{
		$this->cart->removeRow($row, $type);
		$this->flashMessage('Smazáno z košíku.', 'success');
		$this->redirect('Cart:default');
	}

	/**
	 *
	 */
	public function handleTruncateCart()
	{
		$this->cart->truncate();
		$this->flashMessage('Váš košík je nyní prázdný.', 'success');
		$this->redirect('this');
	}

	/**
	 * @return Nette\Application\UI\Form
	 */
	public function createComponentSubmitOrderForm()
	{
		return $this->submitOrderFormFactory->create();
	}
}