<?php
/**
 * Copyright (c) 2016 Jan Kotrba https://jkotrba.net/
 */

namespace App\WebModule\Presenters;

use App\AdminModule\Model\OrdersManager;
use App\Presenters\SecuredPresenter;
use App\WebModule\Components\ChangePasswordFormFactory;
use App\WebModule\Components\EditMyAccountFormFactory;
use App\WebModule\Model\BaguetteBuilder;
use App\WebModule\Model\CartManager;
use App\WebModule\Model\DistanceManager;
use Nette;

/**
 * Class MyAccountPresenter
 * @package App\WebModule\Presenters
 */
class MyAccountPresenter extends SecuredPresenter
{
	/** @var EditMyAccountFormFactory @inject */
	public $editMyAccountFormFactory;

	/** @var  ChangePasswordFormFactory @inject */
	public $changePasswordFormFactory;

	/**
	 * @var OrdersManager
	 */
	private $ordersManager;

	/**
	 * MyAccountPresenter constructor.
	 * @param DistanceManager $distanceManager
	 * @param CartManager $cart
	 * @param BaguetteBuilder $baguetteBuilder
	 * @param OrdersManager $ordersManager
	 */
	public function __construct(DistanceManager $distanceManager, CartManager $cart, BaguetteBuilder $baguetteBuilder, OrdersManager $ordersManager)
	{
		parent::__construct($distanceManager, $cart, $baguetteBuilder);
		$this->ordersManager = $ordersManager;
	}

	/**
	 *
	 */
	public function actionDefault()
	{
		$this->template->orders = $this->ordersManager->getUserOrders();
	}

	/**
	 * @param $orderId
	 */
	public function actionOrder($orderId)
	{
		$order = $this->ordersManager->getOrder($orderId);

		if (!$order) {
			$this->flashMessage('Objednávka neexistuje.', 'danger');
			$this->redirect('MyAccount:default');
		}
		$this->template->order = $order;
	}

	/**
	 * @return Nette\Application\UI\Form
	 */
	public function createComponentEditMyAccountForm()
	{
		return $this->editMyAccountFormFactory->create();
	}

	/**
	 * @return Nette\Application\UI\Form
	 */
	public function createComponentChangePasswordForm()
	{
		return $this->changePasswordFormFactory->create();
	}
}