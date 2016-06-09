<?php
/**
 * Copyright (c) 2016 Jan Kotrba https://jkotrba.net/
 */

namespace App\AdminModule\Model\Entities;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\MagicAccessors;

/**
 * @ORM\Entity
 * @ORM\Table(name="resources")
 */
class BaguetteResource
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
	protected $name;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $type;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $allergens;

	/**
	 * @ORM\Column(type="integer")
	 */
	protected $price;

	/**
	 * @ORM\Column(type="boolean")
	 */
	protected $disabled = false;

	/**
	 *
	 */
	public function resetId()
	{
		$this->id = null;
	}

	/**
	 * @param $state
	 */
	public function setDisabled($state)
	{
		$this->disabled = $state;
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
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param mixed $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * @return mixed
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @param mixed $type
	 */
	public function setType($type)
	{
		$this->type = $type;
	}

	/**
	 * @return mixed
	 */
	public function getAllergens()
	{
		return $this->allergens;
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
	public function getPrice()
	{
		return $this->price;
	}

	/**
	 * @return float
	 */
	public function getCustomerPrice()
	{
		return floor(1.2 * $this->price);
	}

	/**
	 * @param mixed $price
	 */
	public function setPrice($price)
	{
		$this->price = $price;
	}

	/**
	 * @return array
	 */
	public function getAllergensArray()
	{
		if (strlen($this->allergens) > 0) {
			$allergens = explode(', ', $this->allergens);
			if (!$allergens) {
				return [$this->allergens];
			}
			return $allergens;
		}
		return [];
	}

	/**
	 * @param $allergen
	 */
	public function addAllergen($allergen)
	{
		$allergens = explode(', ', $this->allergens);
		$allergens[] = $allergen;

		$this->allergens = implode(", ", $allergens);
	}

	/**
	 * @param array $allergens
	 */
	public function setAllergens(array $allergens)
	{
		$this->allergens = implode(", ", $allergens);
	}
}