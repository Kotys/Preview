<?php
/**
 * Copyright (c) 2016 Jan Kotrba https://jkotrba.net/
 */

namespace App\Model\Entities;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\MagicAccessors;
use Nette\Utils\DateTime;
use Tracy\Debugger;

/**
 * @ORM\Entity
 * @ORM\Table(name="distance_cache")
 */
class DistanceCache
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
	protected $fromStreet;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $fromCity;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $fromProvince;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $toStreet;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $toCity;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $toProvince;

	/**
	 * @ORM\Column(type="float")
	 */
	protected $distance;

	/**
	 * @ORM\Column(type="integer")
	 */
	protected $deliveryTime;

	/**
	 * @ORM\Column(type="datetime")
	 */
	protected $expire = null;


	/**
	 * DistanceCache constructor.
	 */
	public function __construct()
	{
		if ($this->expire === null) {
			$this->expire = new DateTime('+ 14day');
		}
	}

	/**
	 * @return mixed
	 */
	public function getDeliveryTime()
	{
		return $this->deliveryTime;
	}

	/**
	 * @param mixed $deliveryTime
	 */
	public function setDeliveryTime($deliveryTime)
	{
		$this->deliveryTime = $deliveryTime;
	}

	/**
	 * @return mixed
	 */
	public function getFromStreet()
	{
		return $this->fromStreet;
	}

	/**
	 * @param mixed $fromStreet
	 */
	public function setFromStreet($fromStreet)
	{
		$this->fromStreet = $fromStreet;
	}

	/**
	 * @return mixed
	 */
	public function getFromCity()
	{
		return $this->fromCity;
	}

	/**
	 * @param mixed $fromCity
	 */
	public function setFromCity($fromCity)
	{
		$this->fromCity = $fromCity;
	}

	/**
	 * @return mixed
	 */
	public function getFromProvince()
	{
		return $this->fromProvince;
	}

	/**
	 * @param mixed $fromProvince
	 */
	public function setFromProvince($fromProvince)
	{
		$this->fromProvince = $fromProvince;
	}

	/**
	 * @return mixed
	 */
	public function getToStreet()
	{
		return $this->toStreet;
	}

	/**
	 * @param mixed $toStreet
	 */
	public function setToStreet($toStreet)
	{
		$this->toStreet = $toStreet;
	}

	/**
	 * @return mixed
	 */
	public function getToCity()
	{
		return $this->toCity;
	}

	/**
	 * @param mixed $toCity
	 */
	public function setToCity($toCity)
	{
		$this->toCity = $toCity;
	}

	/**
	 * @return mixed
	 */
	public function getToProvince()
	{
		return $this->toProvince;
	}

	/**
	 * @param mixed $toProvince
	 */
	public function setToProvince($toProvince)
	{
		$this->toProvince = $toProvince;
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
		$this->distance = $distance;
	}

	/**
	 * @return mixed
	 */
	public function getExpire()
	{
		return $this->expire;
	}

	/**
	 * @return bool
	 */
	public function isExpired()
	{
		return ($this->getExpire()->getTimestamp() < time());
	}
}