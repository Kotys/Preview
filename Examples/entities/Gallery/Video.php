<?php

namespace App\Admin\Model\Entities;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\MagicAccessors;
use Nette\Utils\DateTime;

/**
 * @ORM\Entity
 * @ORM\Table(name="videos")
 */
class Video
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
	protected $title;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $link;

	/**
	 * @ORM\Column(type="datetime")
	 */
	protected $created;

	/**
	 * @ORM\ManyToOne(targetEntity="User")
	 * @ORM\JoinColumn(name="author_id", referencedColumnName="id", onDelete="SET NULL")
	 */
	protected $author;

	public function __construct()
	{
		if (!$this->created instanceof DateTime) {
			$this->created = new DateTime();
		}
	}


	/**
	 * @param mixed $author
	 */
	public function setAuthor(User $author)
	{
		$this->author = $author;
	}

	/**
	 * @return mixed
	 */
	public function getAuthor()
	{
		return $this->author;
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
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * @param mixed $title
	 */
	public function setTitle($title)
	{
		$this->title = trim($title);
	}

	/**
	 * @return mixed
	 */
	public function getLink()
	{
		return $this->link;
	}

	/**
	 * @return mixed
	 */
	public function getCreated()
	{
		return $this->created;
	}

	/**
	 * @param mixed $link
	 */
	public function setLink($link)
	{
		$this->link = trim($link);
	}

}