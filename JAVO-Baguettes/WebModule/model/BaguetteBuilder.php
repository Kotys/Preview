<?php
/**
 * Copyright (c) 2016 Jan Kotrba https://jkotrba.net/
 */

namespace App\WebModule\Model;

use App\AdminModule\Model\Entities\CustomBaguette;
use Nette;
use Tracy\Debugger;

/**
 * Class BaguetteBuilder
 * @package App\WebModule\Model
 */
class BaguetteBuilder extends Nette\Object
{

	/**
	 * @var Nette\Http\SessionSection
	 */
	private $section;

	/**
	 * @var BaguetteFacade
	 */
	private $baguetteFacade;

	/**
	 * BaguetteBuilder constructor.
	 * @param Nette\Http\Session $session
	 * @param BaguetteFacade $baguetteFacade
	 */
	public function __construct(Nette\Http\Session $session, BaguetteFacade $baguetteFacade)
	{
		$this->baguetteFacade = $baguetteFacade;
		$this->section = $session->getSection('BaguetteBuilder');
	}

	/**
	 * Create new CustomBaguette in builder
	 */
	public function initBuilder()
	{
		$this->section->setExpiration(new Nette\Utils\DateTime('+ 24hour'));
		$this->section['customBaguette'] = new CustomBaguette();
	}

	/**
	 * If session is cleaned
	 * @return bool
	 */
	public function isClean()
	{
		return ($this->section['customBaguette'] instanceof CustomBaguette) ? false : true;
	}

	/**
	 * Clean the session
	 */
	public function clean()
	{
		$this->section['customBaguette'] = null;
	}

	/**
	 * @return mixed
	 */
	public function getCustomBaguette()
	{
		return $this->section['customBaguette'];
	}


	/**
	 * @param $group
	 * @param array $resourcesId
	 */
	public function addResource($group, array $resourcesId)
	{
		$resources = $this->baguetteFacade->getResourcesById($resourcesId);

		if (!$resources) {
			switch ($group) {
				case 'baked-goods':
					$this->section['customBaguette']->emptyBakedGoods();
					break;
				case 'filling':
					$this->section['customBaguette']->emptyFillings();
					break;
				case 'dressing':
					$this->section['customBaguette']->emptyDressing();
					break;
				case 'vegetable':
					$this->section['customBaguette']->emptyVegetables();
					break;
			}
			return;
		}

		switch ($resources[0]->getType()) {
			case 'baked-goods':
				$this->section['customBaguette']->setBakedGoods($resources[0]);
				break;
			case 'filling':
				$this->section['customBaguette']->setFillings($resources);
				break;
			case 'dressing':
				$this->section['customBaguette']->setDressing($resources);
				break;
			case 'vegetable':
				$this->section['customBaguette']->setVegetables($resources);
				break;
		}
	}
}