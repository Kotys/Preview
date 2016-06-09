<?php
/**
 * Copyright (c) 2016 Jan Kotrba https://jkotrba.net/
 */

namespace App\WebModule\Model\Entities;

use App\AdminModule\Model\Entities\CustomBaguette;
use App\AdminModule\Model\Entities\MenuBaguette;
use App\Model\AddressDao;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\MagicAccessors;
use App\Model\Entities\User;
use Nette\Utils\DateTime;
use Nette\Utils\Random;

/**
 * @ORM\Entity
 * @ORM\Table(name="orders")
 */
class Orders
{

	use MagicAccessors;

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 */
	protected $id;

	/**
	 * @ORM\Column(type="integer")
	 */
	protected $created;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $billing_firstName;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $billing_lastName;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $billing_email;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $billing_phone;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $billing_street;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $billing_town;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $billing_psc;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $billing_province;

	/**
	 * @ORM\Column(type="boolean")
	 */
	protected $different_delivery = false;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 */
	protected $delivery_firstName;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 */
	protected $delivery_lastName;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 */
	protected $delivery_street;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 */
	protected $delivery_town;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 */
	protected $delivery_psc;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 */
	protected $delivery_province;

	/**
	 * @ORM\Column(type="float", nullable=true)
	 */
	protected $distance;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $deliveryType;

	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $deliveryPrice;

	/**
	 * @ORM\Column(type="integer")
	 */
	protected $baguettesPrice;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $paymentMethod;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	protected $expectedDelivery;

	/**
	 * @ORM\ManyToMany(targetEntity="App\AdminModule\Model\Entities\CustomBaguette", cascade={"persist", "remove"})
	 * @ORM\JoinTable(name="orders_custom_baguette",
	 *      joinColumns={@ORM\JoinColumn(name="order_id", referencedColumnName="id", onDelete="CASCADE")},
	 *      inverseJoinColumns={@ORM\JoinColumn(name="custom_baguette_id", referencedColumnName="id", onDelete="CASCADE")}
	 *      )
	 */
	protected $customBaguettes;

	/**
	 * @ORM\ManyToMany(targetEntity="App\AdminModule\Model\Entities\MenuBaguette", cascade={"persist", "remove"})
	 * @ORM\JoinTable(name="orders_menu_baguette",
	 *      joinColumns={@ORM\JoinColumn(name="order_id", referencedColumnName="id", onDelete="CASCADE")},
	 *      inverseJoinColumns={@ORM\JoinColumn(name="menu_baguette_id", referencedColumnName="id", onDelete="CASCADE")}
	 *      )
	 */
	protected $menuBaguettes;

	/**
	 * @ORM\ManyToOne(targetEntity="App\Model\Entities\User", inversedBy="userOrders")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 */
	protected $user;

	/**
	 * @ORM\Column(type="datetime")
	 */
	protected $dateOfPurchase;

	/**
	 * @ORM\Column(type="boolean")
	 */
	protected $processed = false;

	/**
	 * Orders constructor.
	 */
	public function __construct()
	{
		$this->customBaguettes = new ArrayCollection();
		$this->menuBaguettes = new ArrayCollection();
		$this->created = time();
	}

	/**
	 * @param DateTime $dateTime
	 */
	public function setDateOfPurchase(DateTime $dateTime)
	{
		$this->dateOfPurchase = $dateTime;
	}

	/**
	 * @return mixed
	 */
	public function getDateOfPurchase()
	{
		return $this->dateOfPurchase;
	}

	/**
	 *
	 */
	public function removeUser()
	{
		$this->user = null;
	}

	/**
	 *
	 */
	public function beforeSave()
	{
		$newMenuBaguettes = new ArrayCollection();

		/** @var MenuBaguette $menuBaguette */
		foreach ($this->menuBaguettes as $menuBaguette) {
			if (!$newMenuBaguettes->offsetExists($menuBaguette->getId())) {
				$newMenuBaguettes->offsetSet($menuBaguette->getId(), $menuBaguette);
			} else {
				$newMenuBaguettes[$menuBaguette->getId()]->incrementCount();
			}
		}

		$this->menuBaguettes = $newMenuBaguettes;
	}

	/**
	 * @return mixed
	 */
	public function getDeliveryType()
	{
		return $this->deliveryType;
	}

	/**
	 * @param mixed $deliveryType
	 */
	public function setDeliveryType($deliveryType)
	{
		$this->deliveryType = $deliveryType;
	}

	/**
	 * @param User $user
	 */
	public function setUser(User $user)
	{
		$this->user = $user;
	}

	/**
	 * @return mixed
	 */
	public function getUser()
	{
		return $this->user;
	}

	/**
	 * @return int
	 */
	public function getCount()
	{
		return count($this->getBaguettes());
	}

	/**
	 * @param CustomBaguette $customBaguette
	 */
	public function addCustomBaguette(CustomBaguette $customBaguette)
	{
		do {
			$key = Random::generate();
		} while ($this->customBaguettes->offsetExists($key));

		$this->customBaguettes->offsetSet($key, $customBaguette);
	}

	/**
	 * @param MenuBaguette $menuBaguette
	 */
	public function addMenuBaguette(MenuBaguette $menuBaguette)
	{
		do {
			$key = Random::generate();
		} while ($this->menuBaguettes->offsetExists($key));

		$this->menuBaguettes->offsetSet($key, $menuBaguette);
	}

	/**
	 * @return ArrayCollection
	 */
	public function getBaguettes()
	{
		return new ArrayCollection(
			array_merge($this->menuBaguettes->toArray(), $this->customBaguettes->toArray())
		);
	}

	/**
	 * @param $row
	 */
	public function removeCustomBaguette($row)
	{
		$this->customBaguettes->remove($row);
	}

	/**
	 * @param $row
	 */
	public function removeMenuBaguette($row)
	{
		$this->menuBaguettes->remove($row);
	}

	/**
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return mixed
	 */
	public function getCreated()
	{
		return $this->created;
	}

	/**
	 * @return mixed
	 */
	public function getBillingFirstName()
	{
		return $this->billing_firstName;
	}

	/**
	 * @return mixed
	 */
	public function getBillingLastName()
	{
		return $this->billing_lastName;
	}

	/**
	 * @return mixed
	 */
	public function getBillingEmail()
	{
		return $this->billing_email;
	}

	/**
	 * @return mixed
	 */
	public function getBillingPhone()
	{
		return $this->billing_phone;
	}

	/**
	 * @return mixed
	 */
	public function getBillingStreet()
	{
		return $this->billing_street;
	}

	/**
	 * @return mixed
	 */
	public function getBillingTown()
	{
		return $this->billing_town;
	}

	/**
	 * @return mixed
	 */
	public function getBillingPsc()
	{
		return $this->billing_psc;
	}

	/**
	 * @return mixed
	 */
	public function getBillingProvince()
	{
		return $this->billing_province;
	}

	/**
	 * @return mixed
	 */
	public function getDeliveryFirstName()
	{
		return $this->delivery_firstName;
	}

	/**
	 * @return mixed
	 */
	public function getDeliveryLastName()
	{
		return $this->delivery_lastName;
	}

	/**
	 * @return mixed
	 */
	public function getDeliveryStreet()
	{
		return $this->delivery_street;
	}

	/**
	 * @return mixed
	 */
	public function getDeliveryTown()
	{
		return $this->delivery_town;
	}

	/**
	 * @return mixed
	 */
	public function getDeliveryPsc()
	{
		return $this->delivery_psc;
	}

	/**
	 * @return mixed
	 */
	public function getDeliveryProvince()
	{
		return $this->delivery_province;
	}

	/**
	 * @param mixed $billing_firstName
	 */
	public function setBillingFirstName($billing_firstName)
	{
		$this->billing_firstName = trim($billing_firstName);
	}

	/**
	 * @param mixed $billing_lastName
	 */
	public function setBillingLastName($billing_lastName)
	{
		$this->billing_lastName = trim($billing_lastName);
	}

	/**
	 * @param mixed $billing_email
	 */
	public function setBillingEmail($billing_email)
	{
		$this->billing_email = trim($billing_email);
	}

	/**
	 * @param mixed $billing_phone
	 */
	public function setBillingPhone($billing_phone)
	{
		$this->billing_phone = trim($billing_phone);
	}

	/**
	 * @param mixed $billing_street
	 */
	public function setBillingStreet($billing_street)
	{
		$this->billing_street = trim($billing_street);
	}

	/**
	 * @param mixed $billing_town
	 */
	public function setBillingTown($billing_town)
	{
		$this->billing_town = trim($billing_town);
	}

	/**
	 * @param mixed $billing_psc
	 */
	public function setBillingPsc($billing_psc)
	{
		$this->billing_psc = trim($billing_psc);
	}

	/**
	 * @param mixed $billing_province
	 */
	public function setBillingProvince($billing_province)
	{
		$this->billing_province = trim($billing_province);
	}

	/**
	 * @param mixed $delivery_firstName
	 */
	public function setDeliveryFirstName($delivery_firstName)
	{
		$this->delivery_firstName = trim($delivery_firstName);
	}

	/**
	 * @param mixed $delivery_lastName
	 */
	public function setDeliveryLastName($delivery_lastName)
	{
		$this->delivery_lastName = trim($delivery_lastName);
	}

	/**
	 * @param mixed $delivery_street
	 */
	public function setDeliveryStreet($delivery_street)
	{
		$this->delivery_street = trim($delivery_street);
	}

	/**
	 * @param mixed $delivery_town
	 */
	public function setDeliveryTown($delivery_town)
	{
		$this->delivery_town = trim($delivery_town);
	}

	/**
	 * @param mixed $delivery_psc
	 */
	public function setDeliveryPsc($delivery_psc)
	{
		$this->delivery_psc = trim($delivery_psc);
	}

	/**
	 * @param mixed $delivery_province
	 */
	public function setDeliveryProvince($delivery_province)
	{
		$this->delivery_province = trim($delivery_province);
	}

	/**
	 * @return mixed
	 */
	public function getDifferentDelivery()
	{
		return $this->different_delivery;
	}

	/**
	 * @param mixed $different_delivery
	 */
	public function setDifferentDelivery($different_delivery)
	{
		$this->different_delivery = trim($different_delivery);
	}

	/**
	 * @return mixed
	 */
	public function getDistance()
	{
		return $this->distance;
	}

	/**
	 * @param mixed $distance
	 */
	public function setDistance($distance)
	{
		if ($distance == 0) {
			$this->deliveryPrice = 0;
		}

		$this->distance = $distance;

		$distance -= 10;
		if ($distance >= 1) {
			$this->deliveryPrice = ceil($distance * 2.5);
		}
	}

	/**
	 * @return AddressDao
	 */
	public function getBillingAddressDao()
	{
		return new AddressDao($this->billing_town, $this->billing_psc, $this->billing_street, $this->billing_province);
	}

	/**
	 * @return AddressDao
	 */
	public function getDeliveryAddressDao()
	{
		return new AddressDao($this->delivery_town, $this->delivery_psc, $this->delivery_street, $this->delivery_province);
	}

	/**
	 * @return mixed
	 */
	public function getPaymentMethod()
	{
		return $this->paymentMethod;
	}

	/**
	 * @param mixed $paymentMethod
	 */
	public function setPaymentMethod($paymentMethod)
	{
		$this->paymentMethod = $paymentMethod;
	}

	/**
	 * @return mixed
	 */
	public function getDeliveryPrice()
	{
		return $this->deliveryPrice;
	}

	/**
	 * @return mixed
	 */
	public function getBaguettesPrice()
	{
		return $this->baguettesPrice;
	}

	/**
	 * @param mixed $baguettesPrice
	 */
	public function setBaguettesPrice($baguettesPrice)
	{
		$this->baguettesPrice = $baguettesPrice;
	}

	/**
	 * @return mixed
	 */
	public function getTotalPrice()
	{
		return $this->getDeliveryPrice() + $this->getBaguettesPrice();
	}

	/**
	 * @param DateTime $dateTime
	 */
	public function setExpectedDelivery(DateTime $dateTime)
	{
		$this->expectedDelivery = $dateTime;
	}

	/**
	 * @return mixed
	 */
	public function getExpectedDelivery()
	{
		return $this->expectedDelivery;
	}

	/**
	 * @return mixed
	 */
	public function getProcessed()
	{
		return $this->processed;
	}

	/**
	 * @param mixed $processed
	 */
	public function setProcessed($processed)
	{
		$this->processed = $processed;
	}
}