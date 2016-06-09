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
 * @ORM\Table(name="custom_baguettes")
 */
class CustomBaguette
{

	use MagicAccessors;

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 */
	protected $id;

	protected $baguetteName = 'Vlastní bageta';

	/**
	 * @ORM\Column(type="integer")
	 */
	protected $created;

	/**
	 * @ORM\OneToOne(targetEntity="BaguetteResource", cascade={"persist", "remove"})
	 * @ORM\JoinTable(name="custom_baguette_baked_goods",
	 *      joinColumns={@ORM\JoinColumn(name="custom_baguette_id", referencedColumnName="id")},
	 *      inverseJoinColumns={@ORM\JoinColumn(name="resource_id", referencedColumnName="id")}
	 *      )
	 */
	protected $bakedGoods;

	/**
	 * @ORM\ManyToMany(targetEntity="BaguetteResource", cascade={"persist", "remove"})
	 * @ORM\JoinTable(name="custom_baguette_fillings",
	 *      joinColumns={@ORM\JoinColumn(name="custom_baguette_id", referencedColumnName="id")},
	 *      inverseJoinColumns={@ORM\JoinColumn(name="resource_id", referencedColumnName="id")}
	 *      )
	 */
	protected $fillings;

	/**
	 * @ORM\ManyToMany(targetEntity="BaguetteResource", cascade={"persist", "remove"})
	 * @ORM\JoinTable(name="custom_baguette_vegetables",
	 *      joinColumns={@ORM\JoinColumn(name="custom_baguette_id", referencedColumnName="id")},
	 *      inverseJoinColumns={@ORM\JoinColumn(name="resource_id", referencedColumnName="id")}
	 *      )
	 */
	protected $vegetables;

	/**
	 * @ORM\ManyToMany(targetEntity="BaguetteResource", cascade={"persist", "remove"})
	 * @ORM\JoinTable(name="custom_baguette_dressing",
	 *      joinColumns={@ORM\JoinColumn(name="custom_baguette_id", referencedColumnName="id")},
	 *      inverseJoinColumns={@ORM\JoinColumn(name="resource_id", referencedColumnName="id")}
	 *      )
	 */
	protected $dressing;

	protected $count = 1;


	/**
	 * Baguette constructor.
	 */
	public function __construct()
	{
		$this->fillings = new ArrayCollection();
		$this->vegetables = new ArrayCollection();
		$this->dressing = new ArrayCollection();
		$this->created = time();
	}

	/**
	 * @return int
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
		$resource->setDisabled(true);
		$this->fillings->add($resource);
	}

	/**
	 * @param BaguetteResource $resource
	 */
	public function addVegetables(BaguetteResource $resource)
	{
		$resource->setDisabled(true);
		$this->vegetables->add($resource);
	}


	/**
	 * @param BaguetteResource $resource
	 */
	public function setBakedGoods(BaguetteResource $resource)
	{
		$resource->setDisabled(true);
		$this->bakedGoods = $resource;
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
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
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
	 * @param mixed $dressing
	 */
	public function addDressing(BaguetteResource $dressing)
	{
		$this->dressing->add($dressing);
	}

	/**
	 * @return mixed
	 */
	public function getPrice()
	{
		$totalPrice = 0;

		if ($this->bakedGoods) {
			$totalPrice += $this->bakedGoods->getCustomerPrice();
		}

		/** @var BaguetteResource $filling */
		foreach ($this->fillings->getIterator() as $filling) {
			$totalPrice += $filling->getCustomerPrice();
		}

		/** @var BaguetteResource $vegetables */
		foreach ($this->vegetables->getIterator() as $vegetables) {
			$totalPrice += $vegetables->getCustomerPrice();
		}

		/** @var BaguetteResource $dressing */
		foreach ($this->dressing->getIterator() as $dressing) {
			$totalPrice += $dressing->getCustomerPrice();
		}

		return $totalPrice;
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
	 * @param $dressings
	 */
	public function setDressing($dressings)
	{
		$this->dressing = new ArrayCollection();
		/** @var BaguetteResource $dressing */
		foreach ($dressings as $dressing) {
			$dressing->setDisabled(true);
			$this->dressing->add($dressing);
		}
	}

	/**
	 * @param mixed $vegetables
	 */
	public function setVegetables($vegetables)
	{
		$this->vegetables = new ArrayCollection();
		/** @var BaguetteResource $vegetable */
		foreach ($vegetables as $vegetable) {
			$vegetable->setDisabled(true);
			$this->vegetables->add($vegetable);
		}

	}

	/**
	 * @param mixed $fillings
	 */
	public function setFillings($fillings)
	{
		$this->fillings = new ArrayCollection();
		/** @var BaguetteResource $filling */
		foreach ($fillings as $filling) {
			$filling->setDisabled(true);
			$this->fillings->add($filling);
		}
	}

	/**
	 *
	 */
	public function emptyFillings()
	{
		$this->fillings = new ArrayCollection();
	}

	/**
	 *
	 */
	public function emptyVegetables()
	{
		$this->vegetables = new ArrayCollection();
	}

	/**
	 *
	 */
	public function emptyDressing()
	{
		$this->dressing = new ArrayCollection();
	}

	/**
	 *
	 */
	public function emptyBakedGoods()
	{
		$this->bakedGoods = new ArrayCollection();
	}

	/**
	 * @return array
	 */
	public function getFillingsIdArray()
	{
		$ids = [];

		foreach ($this->fillings as $filling) {
			$ids[] = $filling->getId();
		}

		return $ids;
	}

	/**
	 * @return array
	 */
	public function getVegetablesIdArray()
	{
		$ids = [];

		foreach ($this->vegetables as $vegetable) {
			$ids[] = $vegetable->getId();
		}

		return $ids;
	}

	/**
	 * @return array
	 */
	public function getDressingIdArray()
	{
		$ids = [];

		foreach ($this->dressing as $dressing) {
			$ids[] = $dressing->getId();
		}

		return $ids;
	}

	/**
	 * @return string
	 */
	public function getBaguetteName()
	{
		return $this->baguetteName;
	}


	public function getType()
	{
		return 'custom-baguette';
	}
}