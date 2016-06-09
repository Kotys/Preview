<?php
/**
 * Copyright (c) 2016 Jan Kotrba https://jkotrba.net/
 */

namespace App\WebModule\Presenters;

use App\WebModule\Model\DistanceManager;
use App\WebModule\Model\BaguetteBuilder;
use App\WebModule\Model\BaguetteFacade;
use App\WebModule\Model\CartManager;
use Nette\Security as NS;
use Nette;

/**
 * Class HomepagePresenter
 * @package App\WebModule\Presenters
 */
class HomepagePresenter extends BasePresenter
{

	/**
	 * @var BaguetteFacade
	 */
	private $baguetteFacade;

	/**
	 * HomepagePresenter constructor.
	 * @param DistanceManager $distanceManager
	 * @param CartManager $cartManager
	 * @param BaguetteBuilder $baguetteBuilder
	 * @param BaguetteFacade $baguetteFacade
	 */
	public function __construct(DistanceManager $distanceManager, CartManager $cartManager, BaguetteBuilder $baguetteBuilder, BaguetteFacade $baguetteFacade)
	{
		parent::__construct($distanceManager, $cartManager, $baguetteBuilder);
		$this->baguetteFacade = $baguetteFacade;
	}

	/**
	 *
	 */
	public function actionOurMenu()
	{
		$this->template->baguettes = $this->baguetteFacade->getAll();
	}

	/**
	 * @param $id
	 * @param $count
	 */
	public function handleAddToCart($id, $count)
	{
		$menu = $this->baguetteFacade->getMenuBaguette($id);

		do {
			$this->cart->addMenuBaguette($menu);
			$count--;
 		} while($count > 0);

		$this->flashMessage('Přidáno do košíku.', 'success');
		if(!$this->isAjax()) {
			$this->redirect('this');
		}
	}
}
