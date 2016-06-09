<?php
/**
 * Copyright (c) 2016 Jan Kotrba https://jkotrba.net/
 */

namespace App\Model\Entities;

use App\Model\AddressDao;
use App\WebModule\Model\Entities\Orders;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\MagicAccessors;
use Nette\Security\Passwords;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User
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
	protected $email;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $password;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $firstName;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $lastName;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $town;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $street;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $province;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $psc;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $phone;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $role = 'customer';

	/**
	 * @ORM\OneToMany(targetEntity="App\WebModule\Model\Entities\Orders", mappedBy="user")
	 */
	protected $userOrders;

	/**
	 * User constructor.
	 */
	public function __construct()
	{
		$this->userOrders = new ArrayCollection();
	}

	/**
	 * @param Orders $orders
	 */
	public function addOrder(Orders $orders)
	{
		$this->userOrders->add($orders);
	}

	/**
	 * @return ArrayCollection
	 */
	public function getOrders()
	{
		return $this->userOrders;
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
	public function getEmail()
	{
		return $this->email;
	}

	/**
	 * @param mixed $email
	 */
	public function setEmail($email)
	{
		$this->email = $email;
	}

	/**
	 * @return mixed
	 */
	public function getPassword()
	{
		return $this->password;
	}

	/**
	 * @param mixed $password
	 */
	public function setPassword($password)
	{
		$this->password = Passwords::hash($password);
	}

	/**
	 * @return mixed
	 */
	public function getFirstName()
	{
		return $this->firstName;
	}

	/**
	 * @param mixed $firstName
	 */
	public function setFirstName($firstName)
	{
		$this->firstName = $firstName;
	}

	/**
	 * @return mixed
	 */
	public function getLastName()
	{
		return $this->lastName;
	}

	/**
	 * @param mixed $lastName
	 */
	public function setLastName($lastName)
	{
		$this->lastName = $lastName;
	}

	/**
	 * @return string
	 */
	public function getFullName()
	{
		return $this->lastName . " " . $this->firstName;
	}

	/**
	 * @return mixed
	 */
	public function getTown()
	{
		return $this->town;
	}

	/**
	 * @param mixed $town
	 */
	public function setTown($town)
	{
		$this->town = $town;
	}

	/**
	 * @return mixed
	 */
	public function getStreet()
	{
		return $this->street;
	}

	/**
	 * @param mixed $street
	 */
	public function setStreet($street)
	{
		$this->street = $street;
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

	/**
	 * @return mixed
	 */
	public function getPhone()
	{
		return $this->phone;
	}

	/**
	 * @param mixed $phone
	 */
	public function setPhone($phone)
	{
		$this->phone = (string)$phone;
	}

	/**
	 * @return string
	 */
	public function getRole()
	{
		return ($this->role == 'customer') ? [$this->role] : [$this->role, 'customer'];
	}

	/**
	 * @return string
	 */
	public function getSimpleRole()
	{
		return $this->role;
	}

	/**
	 * @param string $role
	 */
	public function setRole($role)
	{
		$this->role = $role;
	}

	/**
	 * @return mixed
	 */
	public function getProvince()
	{
		return $this->province;
	}

	/**
	 * @param mixed $province
	 */
	public function setProvince($province)
	{
		$this->province = $province;
	}

	/**
	 * @return AddressDao
	 */
	public function getAddressDao()
	{
		return new AddressDao($this->town, $this->psc, $this->street, $this->province);
	}
}