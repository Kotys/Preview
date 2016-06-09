<?php
/**
 * Copyright (c) 2016 Jan Kotrba https://jkotrba.net/
 */

namespace App\WebModule\Model;

use App\AdminModule\Model\Entities\CustomBaguette;
use App\AdminModule\Model\Entities\MenuBaguette;
use App\WebModule\Model\Entities\Orders;
use Nette;

/**
 * Class CartManager
 * @package App\WebModule\Model
 */
class CartManager extends Nette\Object
{

	/**
	 * @var Nette\Http\SessionSection
	 */
	private $section;

	/**
	 * @var Nette\Security\User
	 */
	private $user;

	/**
	 * @var UserManager
	 */
	private $userManager;

	/**
	 * CartManager constructor.
	 * @param Nette\Http\Session $session
	 * @param UserManager $userManager
	 * @param Nette\Security\User $user
	 */
	public function __construct(Nette\Http\Session $session, UserManager $userManager, Nette\Security\User $user)
	{
		$this->section = $session->getSection('Cart');
		$this->user = $user;
		$this->userManager = $userManager;

		if (!$this->section['order'] instanceof Orders) {
			self::truncate();
		}
	}

	/**
	 *
	 */
	public function truncate()
	{
		$this->section['order'] = new Orders();
		if ($this->user->isLoggedIn()) {
			$this->section['order']->setUser($this->userManager->getUser($this->user->getId()));
		}
	}

	/**
	 * @param CustomBaguette $customBaguette
	 */
	public function addCustomBaguette(CustomBaguette $customBaguette)
	{
		$this->section['order']->addCustomBaguette($customBaguette);
	}

	/**
	 * @param MenuBaguette $menuBaguette
	 */
	public function addMenuBaguette(MenuBaguette $menuBaguette)
	{
		$menuBaguette->setDisabled(true);
		$menuBaguette->disableResources();

		$this->section['order']->addMenuBaguette($menuBaguette);
	}

	/**
	 * @return mixed
	 */
	public function getCart()
	{
		/** @return Orders */
		return $this->section['order'];
	}

	/**
	 * @param $row
	 * @param $type
	 */
	public function removeRow($row, $type)
	{
		switch ($type) {
			case 'custom-baguette':
				$this->section['order']->removeCustomBaguette($row);
				break;
			case 'menu-baguette':
				$this->section['order']->removeMenuBaguette($row);
				break;
		}
	}

	/**
	 * @return mixed
	 */
	public function getPrice()
	{
		return $this->getDeliveryPrice() + $this->getBaguettesPrice();
	}

	/**
	 * @return mixed
	 */
	public function getDeliveryPrice()
	{
		if ($this->getCart()->getDeliveryType() == 'self') {
			return 0;
		}

		return $this->getCart()->getDeliveryPrice();
	}

	/**
	 * @return int
	 */
	public function getBaguettesPrice()
	{
		$baguettesPrice = 0;

		/** @var Orders $cart */
		$cart = $this->getCart();
		foreach ($cart->getBaguettes() as $baguette) {
			$baguettesPrice += $baguette->getPrice();
		}

		return $baguettesPrice;
	}
}