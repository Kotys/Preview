<?php
/**
 * Copyright (c) 2016 Jan Kotrba https://jkotrba.net/
 */

namespace App\AdminModule\Presenters;

use App\AdminModule\Model\CRUDException;
use App\AdminModule\Model\OrdersManager;
use Nette;

/**
 * Class OrdersPresenter
 * @package App\AdminModule\Presenters
 */
class OrdersPresenter extends SecuredPresenter
{

	/**
	 * @var OrdersManager
	 */
	private $ordersManager;

	/**
	 * OrdersPresenter constructor.
	 * @param OrdersManager $ordersManager
	 */
	public function __construct(OrdersManager $ordersManager)
	{
		parent::__construct();
		$this->ordersManager = $ordersManager;
	}

	/**
	 *
	 */
	public function actionAll()
	{
		$this->template->orders = $this->ordersManager->getOrders();
	}

	/**
	 * @param $id
	 */
	public function actionDetail($id)
	{
		$order = $this->ordersManager->getOrder($id, false);
		if (!$order) {
			$this->flashMessage('Tato objednávka neexistuje.', 'danger');
			$this->redirect('Orders:all');
		}
		$this->template->order = $order;
	}

	/**
	 * @param $id
	 */
	public function handleExecuteOrder($id)
	{
		try {
			$this->ordersManager->executeOrder($id);
			$this->flashMessage('Objednávka vyřízena.', 'success');
		} catch (CRUDException $e) {
			$this->flashMessage($e->getMessage(), 'danger');
		}
		$this->redirect('Orders:all');
	}
}