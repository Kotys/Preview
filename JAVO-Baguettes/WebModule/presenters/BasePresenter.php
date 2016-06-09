<?php
/**
 * Copyright (c) 2016 Jan Kotrba https://jkotrba.net/
 */

namespace App\WebModule\Presenters;

use App\Presenters\SecuredPresenter;
use App\WebModule\Model\BaguetteBuilder;
use App\WebModule\Model\DistanceManager;
use App\WebModule\Model\CartManager;
use Nette;
use Tracy\Debugger;
use WhichBrowser\Parser;

/**
 * Class BasePresenter
 * @package App\WebModule\Presenters
 */
class BasePresenter extends Nette\Application\UI\Presenter
{

	/** @var \App\WebModule\Components\LoginFormFactory @inject */
	public $loginFormFactory;

	/** @var \App\WebModule\Components\RegisterFormFactory @inject */
	public $registerFormFactory;

	/** @var DistanceManager */
	private $distanceManager;

	/** @var CartManager  */
	public $cart;

	/** @var BaguetteBuilder */
	public $builder;

	/**
	 * BasePresenter constructor.
	 * @param DistanceManager $distanceManager
	 * @param CartManager $cart
	 * @param BaguetteBuilder $baguetteBuilder
	 */
	public function __construct(DistanceManager $distanceManager, CartManager $cart, BaguetteBuilder $baguetteBuilder)
	{
		parent::__construct();
		$this->distanceManager = $distanceManager;
		$this->cart = $cart;
		$this->builder = $baguetteBuilder;
	}

	/**
	 *
	 */
	public function startup()
	{
		parent::startup();

		$browserDetect = new Parser($_SERVER['HTTP_USER_AGENT']);
		if($browserDetect->isType('mobile', 'tablet', 'media', 'gaming:portable')) {
			$this->redirect('NotSupported:default');
		}

		$this->template->cart = $this->cart->getCart();
	}

	/**
	 * Logout
	 */
	public function handleLogOut()
	{
		$this->flashMessage('Odhlášeno.', 'success');
		$this->getUser()->logout(TRUE);

		/** If user is on secured page, like account etc, redirect them elsewhere */
		if ($this->getPresenter() instanceof SecuredPresenter) {
			$this->redirect('Homepage:default');
		}
		$this->redirect('this');
	}

	/**
	 * Set required data
	 */
	public function beforeRender()
	{
		$this->template->user = $this->getUser();
		$this->template->userData = ($this->getUser()->isLoggedIn()) ? $this->getUser()->getIdentity()->getData() : null;
		$this->template->isRelease = !Debugger::detectDebugMode();
	}

	/**
	 * @return Nette\Application\UI\Form
	 */
	public function createComponentLoginForm()
	{
		return $this->loginFormFactory->create();
	}

	/**
	 * @return Nette\Application\UI\Form
	 */
	public function createComponentRegisterForm()
	{
		return $this->registerFormFactory->create();
	}
}