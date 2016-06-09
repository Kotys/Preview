<?php

namespace App\Admin\Model\Entities;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\MagicAccessors;

/**
 * @ORM\Entity
 * @ORM\Table(name="gallery_photos")
 */
class GalleryPhoto
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
	protected $src;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $srcThumb;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $srcAdminThumb;

	/**
	 * @ORM\ManyToOne(targetEntity="Gallery", inversedBy="photos")
	 * @ORM\JoinColumn(name="gallery_id", referencedColumnName="id")
	 */
	protected $gallery;

	/**
	 * @return mixed
	 */
	public function getGallery()
	{
		return $this->gallery;
	}

	/**
	 * @param Gallery $gallery
	 */
	public function setGallery(Gallery $gallery)
	{
		$this->gallery = $gallery;
	}

	/**
	 * @return mixed
	 */
	public function getSrc()
	{
		return $this->src;
	}

	/**
	 * @param mixed $src
	 */
	public function setSrc($src)
	{
		$this->src = $src;
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
	public function getSrcThumb()
	{
		return $this->srcThumb;
	}

	/**
	 * @param mixed $srcThumb
	 */
	public function setSrcThumb($srcThumb)
	{
		$this->srcThumb = $srcThumb;
	}

	/**
	 * @return mixed
	 */
	public function getSrcAdminThumb()
	{
		return $this->srcAdminThumb;
	}

	/**
	 * @param mixed $srcAdminThumb
	 */
	public function setSrcAdminThumb($srcAdminThumb)
	{
		$this->srcAdminThumb = $srcAdminThumb;
	}
}