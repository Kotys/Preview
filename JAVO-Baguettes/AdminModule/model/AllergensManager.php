<?php
/**
 * Copyright (c) 2016 Jan Kotrba https://jkotrba.net/
 */

namespace App\AdminModule\Model;

use App\Admin\Model\Entities\Allergen;
use Kdyby\Doctrine\EntityManager;

use Nette;

/**
 * Class AllergensManager
 * @package App\AdminModule\Model
 */
class AllergensManager extends Nette\Object
{

	/**
	 * @var EntityManager
	 */
	private $entityManager;

	/**
	 * @var Allergen
	 */
	private $all;

	/**
	 * AllergensManager constructor.
	 * @param EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
	}

	/**
	 * @return Allergen
	 */
	public function getAll()
	{
		/** If already all is stored */
		if ($this->all) {
			return $this->all;
		}

		return $this->all = $this->entityManager->getRepository(Allergen::getClassName())->findBy([], ['group' => "ASC"]);
	}
}