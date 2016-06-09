<?php

namespace App\Admin\Model\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\MagicAccessors;

/**
 * @ORM\Entity
 * @ORM\Table(name="gallery_sections")
 */
class GallerySection
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
	 * @ORM\OneToMany(targetEntity="Gallery", mappedBy="section", cascade={"persist"})
	 */
	protected $galleries;

	/**
	 * GallerySection constructor.
	 */
	public function __construct()
	{
		$this->galleries = new ArrayCollection();
	}

	/**
	 * @return mixed
	 */
	public function getTitle()
	{
		return $this->title;
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
	public function getCoverImage()
	{
		return $this->coverImage;
	}

	/**
	 * @param Gallery $gallery
	 */
	public function addGallery(Gallery $gallery)
	{
		$this->galleries->add($gallery);
	}

	/**
	 * @return mixed
	 */
	public function getGalleries()
	{
		return $this->galleries;
	}
}