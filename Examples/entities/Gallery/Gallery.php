<?php

namespace App\Admin\Model\Entities;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\MagicAccessors;
use Nette\Utils\DateTime;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="gallery")
 */
class Gallery
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
	protected $directory;

	/**
	 * @ORM\Column(type="datetime")
	 */
	protected $created;

	/**
	 * @ORM\Column(type="datetime")
	 */
	protected $lastChange;

	/**
	 * @ORM\ManyToOne(targetEntity="User")
	 * @ORM\JoinColumn(name="author_id", referencedColumnName="id", onDelete="SET NULL")
	 */
	protected $author;

	/**
	 * @ORM\ManyToOne(targetEntity="GallerySection", inversedBy="galleries", cascade={"persist"})
	 * @ORM\JoinColumn(name="section_id", referencedColumnName="id")
	 */
	protected $section;

	/**
	 * @ORM\OneToMany(targetEntity="GalleryPhoto", mappedBy="gallery", cascade={"persist", "remove"})
	 */
	protected $photos;

	/**
	 * @ORM\OneToMany(targetEntity="Incident", mappedBy="galleryHook", cascade={"persist", "remove"})
	 */
	protected $hookIncident;

	/**
	 * @ORM\OneToMany(targetEntity="Equipment", mappedBy="galleryHook", cascade={"persist", "remove"})
	 */
	protected $hookEquipment;

	/**
	 * Gallery constructor.
	 */
	public function __construct()
	{
		$this->photos = new ArrayCollection();
		$this->hookIncident = new ArrayCollection();
		$this->hookEquipment = new ArrayCollection();
		$this->created = new DateTime();

		if (empty($this->lastChange)) {
			$this->lastChange = new DateTime();
		}
	}

	/**
	 * @return mixed
	 */
	public function getHookIncident()
	{
		return $this->hookIncident;
	}

	/**
	 * @param mixed $hookIncident
	 */
	public function setHookIncident(Incident $hookIncident)
	{
		$this->hookIncident->add($hookIncident);
	}

	/**
	 * @return mixed
	 */
	public function getHookEquipment()
	{
		return $this->hookEquipment;
	}

	/**
	 * @param mixed $hookEquipment
	 */
	public function setHookEquipment(Equipment $hookEquipment)
	{
		$this->hookEquipment->add($hookEquipment);
	}

	/**
	 * @param GallerySection $gallerySection
	 */
	public function setSection(GallerySection $gallerySection)
	{
		$this->section = $gallerySection;
	}

	/**
	 *
	 */
	public function updateLastChange()
	{
		$this->lastChange = new DateTime();
	}

	/**
	 * @return mixed
	 */
	public function getSection()
	{
		return $this->section;
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
	public function getLastChange()
	{
		return $this->lastChange;
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
	public function getPhotos()
	{
		return $this->photos;
	}

	/**
	 * @param GalleryPhoto $galleryPhoto
	 */
	public function addPhoto(GalleryPhoto $galleryPhoto)
	{
		$this->photos->add($galleryPhoto);
	}

	/**
	 * @return mixed
	 */
	public function getDirectory()
	{
		return $this->directory;
	}

	/**
	 * @param mixed $directory
	 */
	public function setDirectory($directory)
	{
		$this->directory = $directory;
	}
}