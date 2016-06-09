<?php
/**
 * Copyright (c) 2016 Jan Kotrba https://jkotrba.net/
 */

namespace App\Model;

use Nette;

/**
 * Class AddressDao
 * @package App\Model
 */
class AddressDao extends Nette\Object
{
	/**
	 * @var null
	 */
	protected $city;
	/**
	 * @var null
	 */
	protected $street;
	/**
	 * @var null
	 */
	protected $province;

	/**
	 * @var
	 */
	protected $psc;


	/**
	 * AddressDao constructor.
	 * @param null $city
	 * @param null $psc
	 * @param null $street
	 * @param null $province
	 */
	public function __construct($city = null, $psc = null, $street = null, $province = null)
	{
		$this->city = $city;
		$this->psc = $psc;
		$this->street = $street;
		$this->province = $province;
	}

	/**
	 * @return null
	 */
	public function getCity()
	{
		return $this->city . ' ' . $this->getPsc();
	}

	/**
	 * @param null $city
	 */
	public function setCity($city)
	{
		$this->city = $city;
	}

	/**
	 * @return null
	 */
	public function getStreet()
	{
		return $this->street;
	}

	/**
	 * @param null $street
	 */
	public function setStreet($street)
	{
		$this->street = $street;
	}

	/**
	 * @return null
	 */
	public function getProvince()
	{
		return $this->province;
	}

	/**
	 * @param null $province
	 */
	public function setProvince($province)
	{
		$this->province = $province;
	}

	/**
	 * @return mixed
	 */
	public function getPsc()
	{
		return $this->psc;
	}

	/**
	 * @param mixed $psc
	 */
	public function setPsc($psc)
	{
		$this->psc = $psc;
	}
}