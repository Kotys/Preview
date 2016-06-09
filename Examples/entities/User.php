<?php
/**
 * @author Jan Kotrba <jan.kotrbaa@gmail.com>
 */

namespace App\Admin\Model\Entities;


use Doctrine\ORM\Mapping as ORM;
use Nette\Security\Passwords;
use Kdyby\Doctrine\Entities\MagicAccessors;

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
	 * @ORM\Column(type="string", unique=true)
	 */
	protected $email;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 */
	protected $password = null;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $firstName;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $sureName;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $role;

	/**
	 * @ORM\Column(type="boolean", nullable=true)
	 */
	protected $activation = false;

	/**
	 * @ORM\OneToOne(targetEntity="UserActivationToken", mappedBy="user", cascade={"persist"})
	 * @ORM\JoinColumn(name="token_id", referencedColumnName="id", onDelete="SET NULL")
	 */
	protected $token;

	/**
	 * @ORM\Column(type="boolean")
	 */
	protected $disabled = false;

	/**
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 *
	 */
	public function disable()
	{
		$this->disabled = true;
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
		$this->email = trim($email);
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
		$this->firstName = ucfirst(trim($firstName));
	}

	/**
	 * @return mixed
	 */
	public function getSureName()
	{
		return $this->sureName;
	}

	/**
	 * @param mixed $sureName
	 */
	public function setSureName($sureName)
	{
		$this->sureName = ucfirst(trim($sureName));
	}

	/**
	 * @return mixed
	 */
	public function getRole()
	{
		return $this->role;
	}

	/**
	 * @return string
	 */
	public function getTranslatedRole()
	{
		switch ($this->role) {
			case 'admin':
				return 'Správce';
				break;
			default:
				return 'Uživatel';
				break;
		}
	}

	/**
	 * @param mixed $role
	 */
	public function setRole($role)
	{
		$this->role = $role;
	}

	/**
	 * @return mixed
	 */
	public function getActivation()
	{
		return $this->activation;
	}

	/**
	 * @param mixed $activation
	 */
	public function setActivation($activation)
	{
		$this->activation = $activation;
	}

	/**
	 * @return mixed
	 */
	public function getToken()
	{
		return $this->token;
	}

	/**
	 *
	 */
	public function addToken()
	{
		$this->token = new UserActivationToken();
		$this->token->setUser($this);
	}

	/**
	 * @return string
	 */
	public function getFullName()
	{
		return $this->getSureName() . " " . $this->getFirstName();
	}
}