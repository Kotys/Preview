<?php
/**
 * Copyright (c) 2016 Jan Kotrba https://jkotrba.net/
 */

namespace App\AdminModule\Model\Entities;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\MagicAccessors;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="baguettes")
 */
class MenuBaguette
{

	use MagicAccessors;

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 */
	protected $id;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $baguetteName;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $baguetteDesc;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $temptation;

	/**
	 * @ORM\Column(type="integer")
	 */
	protected $price = 0;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $imageLeft;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $imageRight;

	/**
	 * @ORM\Column(type="integer")
	 */
	protected $priority = 1;

	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $count = 1;

	/**
	 * @ORM\Column(type="boolean")
	 */
	protected $disabled = false;

	/**
	 * @ORM\ManyToOne(targetEntity="BaguetteResource", cascade={"persist", "remove"}, fetch="EAGER")
	 * @ORM\JoinTable(name="menu_baguette_baked_goods",
	 *      joinColumns={@ORM\JoinColumn(name="menu_baguette_id", referencedColumnName="id")},
	 *      inverseJoinColumns={@ORM\JoinColumn(name="resource_id", referencedColumnName="id")}
	 *      )
	 */
	protected $bakedGoods;

	/**
	 * @ORM\ManyToMany(targetEntity="BaguetteResource", cascade={"persist"}, fetch="EAGER")
	 * @ORM\JoinTable(name="menu_baguette_fillings",
	 *      joinColumns={@ORM\JoinColumn(name="menu_baguette_id", referencedColumnName="id")},
	 *      inverseJoinColumns={@ORM\JoinColumn(name="resource_id", referencedColumnName="id")}
	 *      )
	 */
	protected $fillings;

	/**
	 * @ORM\ManyToMany(targetEntity="BaguetteResource", cascade={"persist"}, fetch="EAGER")
	 * @ORM\JoinTable(name="menu_baguette_vegetables",
	 *      joinColumns={@ORM\JoinColumn(name="menu_baguette_id", referencedColumnName="id")},
	 *      inverseJoinColumns={@ORM\JoinColumn(name="resource_id", referencedColumnName="id")}
	 *      )
	 */
	protected $vegetables;

	/**
	 * @ORM\ManyToMany(targetEntity="BaguetteResource", cascade={"persist"}, fetch="EAGER")
	 * @ORM\JoinTable(name="menu_baguette_dressing",
	 *      joinColumns={@ORM\JoinColumn(name="menu_baguette_id", referencedColumnName="id")},
	 *      inverseJoinColumns={@ORM\JoinColumn(name="resource_id", referencedColumnName="id")}
	 *      )
	 */
	protected $dressing;


	/**
	 * Baguette constructor.
	 */
	public function __construct()
	{
		$this->fillings = new ArrayCollection();
		$this->vegetables = new ArrayCollection();
		$this->dressing = new ArrayCollection();
	}

	/**
	 *
	 */
	public function incrementCount()
	{
		$this->count++;
	}

	/**
	 * @return mixed
	 */
	public function getCount()
	{
		return $this->count;
	}

	/**
	 * @param BaguetteResource $resource
	 */
	public function addFilling(BaguetteResource $resource)
	{
		$this->fillings->add($resource);
	}

	/**
	 * @param BaguetteResource $resource
	 */
	public function addVegetables(BaguetteResource $resource)
	{
		$this->vegetables->add($resource);
	}


	/**
	 * @param BaguetteResource $resource
	 */
	public function setBakedGoods(BaguetteResource $resource)
	{
		$this->bakedGoods = $resource;
	}

	/**
	 * @param $state
	 */
	public function setDisabled($state)
	{
		$this->disabled = $state;
	}

	/**
	 *
	 */
	public function disableResources()
	{
		$this->bakedGoods->setDisabled(true);

		foreach ($this->dressing as $dressing) {
			$dressing->setDisabled(true);
		}

		foreach ($this->vegetables as $vegetable) {
			$vegetable->setDisabled(true);
		}

		foreach ($this->fillings as $filling) {
			$filling->setDisabled(true);
		}
	}

	/**
	 * @return bool
	 */
	public function isDisabled()
	{
		return !$this->disabled;
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
	public function getBaguetteName()
	{
		return $this->baguetteName;
	}

	/**
	 * @param mixed $baguetteName
	 */
	public function setBaguetteName($baguetteName)
	{
		$this->baguetteName = $baguetteName;
	}

	/**
	 * @return mixed
	 */
	public function getBakedGoods()
	{
		return $this->bakedGoods;
	}

	/**
	 * @return mixed
	 */
	public function getFillings()
	{
		return $this->fillings;
	}

	/**
	 * @return mixed
	 */
	public function getVegetables()
	{
		return $this->vegetables;
	}

	/**
	 * @return mixed
	 */
	public function getDressing()
	{
		return $this->dressing;
	}

	/**
	 * @return array
	 */
	public function getDressingArray()
	{
		$result = [];
		/** @var BaguetteResource $dressing */
		foreach ($this->dressing as $dressing) {
			$result[$dressing->getId()] = $dressing->getName();
		}

		return $result;
	}

	/**
	 * @param mixed $dressing
	 */
	public function addDressing(BaguetteResource $dressing)
	{
		$this->dressing->add($dressing);
	}

	/**
	 * @return mixed
	 */
	public function getImageLeft()
	{
		return $this->imageLeft;
	}

	/**
	 * @param mixed $imageLeft
	 */
	public function setImageLeft($imageLeft)
	{
		$this->imageLeft = $imageLeft;
	}

	/**
	 * @return mixed
	 */
	public function getImageRight()
	{
		return $this->imageRight;
	}

	/**
	 * @param mixed $imageRight
	 */
	public function setImageRight($imageRight)
	{
		$this->imageRight = $imageRight;
	}

	/**
	 * @return mixed
	 */
	public function getBaguetteDesc()
	{
		return $this->baguetteDesc;
	}

	/**
	 * @param mixed $baguetteDesc
	 */
	public function setBaguetteDesc($baguetteDesc)
	{
		$this->baguetteDesc = $baguetteDesc;
	}

	/**
	 * @return mixed
	 */
	public function getTemptation()
	{
		return $this->temptation;
	}

	/**
	 * @param mixed $temptation
	 */
	public function setTemptation($temptation)
	{
		$this->temptation = $temptation;
	}

	/**
	 * @param mixed $price
	 */
	public function setPrice($price)
	{
		$this->price = $price;
	}

	/**
	 * @return mixed
	 */
	public function getPriority()
	{
		return $this->priority;
	}

	/**
	 * @param mixed $priority
	 */
	public function setPriority($priority)
	{
		$this->priority = $priority;
	}

	/**
	 * @return mixed
	 */
	public function getPrice()
	{
		if ($this->price) {
			return $this->price;
		}

		$totalPrice = 0;

		/** @var BaguetteResource $filling */
		foreach ($this->fillings->getIterator() as $filling) {
			$totalPrice += $filling->getPrice();
		}

		/** @var BaguetteResource $vegetables */
		foreach ($this->vegetables->getIterator() as $vegetables) {
			$totalPrice += $vegetables->getPrice();
		}

		/** @var BaguetteResource $dressing */
		foreach ($this->dressing->getIterator() as $dressing) {
			$totalPrice += $dressing->getPrice();
		}

		return ceil(1.2 * $totalPrice);
	}

	/**
	 * @return array
	 */
	public function getAllergens()
	{
		$allergens = [];

		foreach ($this->bakedGoods->getAllergensArray() as $allergen) {
			array_push($allergens, $allergen);
		}

		/** @var BaguetteResource $filling */
		foreach ($this->fillings->getIterator() as $filling) {
			foreach ($filling->getAllergensArray() as $allergen) {
				array_push($allergens, $allergen);
			}
		}

		/** @var BaguetteResource $vegetables */
		foreach ($this->vegetables->getIterator() as $vegetables) {
			foreach ($vegetables->getAllergensArray() as $allergen) {
				array_push($allergens, $allergen);
			}
		}

		/** @var BaguetteResource $dressing */
		foreach ($this->dressing->getIterator() as $dressing) {
			foreach ($dressing->getAllergensArray() as $allergen) {
				array_push($allergens, $allergen);
			}
		}

		sort($allergens);

		return array_unique($allergens);
	}

	/**
	 * @return array
	 */
	public function getFillingsArray()
	{

		$fillings = [];

		/** @var BaguetteResource $filling */
		foreach ($this->fillings->getValues() as $filling) {
			$fillings[] = $filling->getName();
		}

		sort($fillings);

		$vegetables = [];

		/** @var BaguetteResource $vegetable */
		foreach ($this->vegetables->getValues() as $vegetable) {
			$vegetables[] = $vegetable->getName();
		}

		sort($vegetables);

		return array_merge($fillings, $vegetables);
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return 'menu-baguette';
	}
}