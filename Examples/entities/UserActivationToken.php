<?php
/**
 * @author Jan Kotrba <jan.kotrbaa@gmail.com>
 */

namespace App\Admin\Model\Entities;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\MagicAccessors;
use Nette\Utils\DateTime;
use Nette\Utils\Random;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_activation_token")
 */
class UserActivationToken
{
	use MagicAccessors;

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 */
	protected $id;

	/**
	 * @ORM\OneToOne(targetEntity="User", inversedBy="token", cascade={"persist"})
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
	 */
	protected $user;

	/**
	 * @ORM\Column(type="text")
	 */
	protected $token;

	/**
	 * @ORM\Column(type="datetime")
	 */
	protected $expire;

	/**
	 * UserActivationToken constructor.
	 */
	public function __construct()
	{
		if (empty($this->token)) {
			$this->token = Random::generate(40);
		}

		if (empty($this->expire)) {
			$this->expire = new DateTime();
			$this->expire->modify('+1 day');
		}
	}

	/**
	 * @return bool
	 */
	public function isExpired()
	{
		if ($this->expire->getTimestamp() < time()) {
			return true;
		}
		return false;
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
	public function getUser()
	{
		return $this->user;
	}

	/**
	 * @return mixed
	 */
	public function getToken()
	{
		return $this->token;
	}

	/**
	 * @return mixed
	 */
	public function getExpire()
	{
		return $this->expire;
	}

	/**
	 * @param User $user
	 */
	public function setUser(User $user)
	{
		$this->user = $user;
	}
}
