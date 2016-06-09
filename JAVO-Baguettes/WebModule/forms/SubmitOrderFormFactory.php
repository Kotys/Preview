<?php
/**
 * Copyright (c) 2016 Jan Kotrba https://jkotrba.net/
 */

namespace App\WebModule\Components;

use App\Model\Entities\DistanceCache;
use App\Model\Entities\User;
use App\WebModule\Model\CartManager;
use App\WebModule\Model\DistanceManager;
use App\WebModule\Model\DistanceManagerException;
use App\WebModule\Model\Entities\Orders;
use App\WebModule\Model\UserManager;
use Doctrine\Common\Util\Debug;
use Nette;
use Nette\Application\UI\Form;
use App\AdminModule\Model\OrdersManager;
use Tracy\Debugger;

/**
 * Class SubmitOrderFormFactory
 * @package App\WebModule\Components
 */
class SubmitOrderFormFactory extends Nette\Object
{

	/**
	 * @var User
	 */
	private $user;

	/**
	 * @var CartManager
	 */
	private $cartManager;

	/**
	 * @var
	 */
	private $distanceManager;

	/**
	 * @var OrdersManager
	 */
	private $ordersManager;

	/**
	 * SubmitOrderFormFactory constructor.
	 * @param Nette\Security\User $user
	 * @param UserManager $userManager
	 * @param CartManager $cartManager
	 * @param DistanceManager $distanceManager
	 * @param OrdersManager $ordersManager
	 */
	public function __construct(Nette\Security\User $user, UserManager $userManager, CartManager $cartManager, DistanceManager $distanceManager, OrdersManager $ordersManager)
	{
		$this->cartManager = $cartManager;
		$this->distanceManager = $distanceManager;
		$this->ordersManager = $ordersManager;

		if ($user->isLoggedIn()) {
			$this->user = $userManager->getUser($user->getId());
		} else {
			$this->user = new User();
		}
	}

	/**
	 * @return Form
	 */
	public function create()
	{

		$form = new Form();

		$form->addText('billing_firstName')
			->setValue($this->user->getFirstName())
			->setRequired('Musíte zadat vaše jméno.');

		$form->addText('billing_lastName')
			->setValue($this->user->getLastName())
			->setRequired('Musíte zadat vaše příjmení.');

		$form->addText('billing_email')
			->setValue($this->user->getEmail())
			->addRule(Form::EMAIL, 'Email není ve správném formátu.')
			->setRequired('Musíte zadat svůj email.');

		$form->addText('billing_phone')
			->setValue($this->user->getPhone())
			->addRule(Form::NUMERIC, 'Telefon není ve správném formátu.')
			->setRequired('Musíte zadat své číslo.');

		$form->addText('billing_street')
			->setValue($this->user->getStreet())
			->setRequired('Musíte zadat ulici.');

		$form->addText('billing_town')
			->setValue($this->user->getTown())
			->setRequired('Musíte zadat město.');

		$form->addText('billing_psc')
			->setValue($this->user->getPsc())
			->setRequired('Musíte zadat PSČ.');

		$form->addText('billing_province')
			->setValue($this->user->getProvince())
			->setRequired('Musíte zadat stát, ve kterém žijete.');

		$form->addCheckbox('differentDeliveryAddress');

		$form->addText('delivery_firstName')
			->addConditionOn($form['differentDeliveryAddress'], Form::FILLED, true)
			->setRequired('Musíte zadat jméno.');

		$form->addText('delivery_lastName')
			->addConditionOn($form['differentDeliveryAddress'], Form::FILLED, true)
			->setRequired('Musíte zadat příjmení.');

		$form->addText('delivery_street')
			->addConditionOn($form['differentDeliveryAddress'], Form::FILLED, true)
			->setRequired('Musíte zadat ulici.');

		$form->addText('delivery_town')
			->addConditionOn($form['differentDeliveryAddress'], Form::FILLED, true)
			->setRequired('Musíte zadat město.');

		$form->addText('delivery_psc')
			->addConditionOn($form['differentDeliveryAddress'], Form::FILLED, true)
			->setRequired('Musíte zadat PSČ.');

		$form->addText('delivery_province')
			->setValue('Česká Republika')
			->addConditionOn($form['differentDeliveryAddress'], Form::FILLED, true)
			->setRequired('Musíte zadat stát.');

		/** @var Orders $cart */
		$cart = $this->cartManager->getCart();
		if ($cart->getDeliveryType()) {
			$form->addRadioList('deliveryType', [], [
				'self' => 'Nákup si vyzvednu u vás',
				'delivery' => 'JAVO Delivery',
			])
				->setValue($cart->getDeliveryType())
				->setRequired('Musíte vybrat způsob dopravy.');
		} else {
			$form->addRadioList('deliveryType', [], [
				'self' => 'Nákup si vyzvednu u vás',
				'delivery' => 'JAVO Delivery',
			])->setRequired('Musíte vybrat způsob dopravy.');
		}

		if ($cart->getPaymentMethod()) {
			$form->addRadioList('paymentMethod', [], [
				'cash' => 'Hotově',
				'credit' => 'Platba kartou',
			])
				->setValue($cart->getPaymentMethod())
				->setRequired('Musíte vybrat způsob platby.');
		} else {
			$form->addRadioList('paymentMethod', [], [
				'cash' => 'Hotově',
				'credit' => 'Platba kartou',
			])->setRequired('Musíte vybrat způsob platby.');
		}

		function dayInCz($day)
		{
			$translate = array('Neděle', 'Pondělí', 'Úterý', 'středa', 'Čtvrtek', 'Pátek', 'Sobota');
			if (isset($translate[$day])) {
				return ucfirst($translate[$day]);
			} else {
				return ucfirst($translate[$day - 7]);
			}
		}

		$days = [
			'0' => 'Dnes',
			'1' => dayInCz(date("w") + 1),
			'2' => dayInCz(date("w") + 2),
			'3' => dayInCz(date("w") + 3),
		];

		if ($cart->getExpectedDelivery()) {

			$now = new Nette\Utils\DateTime();

			/** @var Nette\Utils\DateTime $datetime */
			$datetime = $cart->getExpectedDelivery();

			$diff = $now->diff($datetime);

			$form->addSelect('delivery_day', null, $days)
				->setValue(intval($diff->format('%d')))
				->setRequired('Musíte vybrat den.');
		} else {
			$form->addSelect('delivery_day', null, $days)
				->setRequired('Musíte vybrat den.');
		}

		$hours = [10 => 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21];

		if ($cart->getExpectedDelivery()) {

			/** @var Nette\Utils\DateTime $datetime */
			$datetime = $cart->getExpectedDelivery();

			$form->addSelect('delivery_hour', null, $hours)
				->setValue($datetime->format('H'))
				->setRequired('Musíte vybrat čas.');
		} else {
			$form->addSelect('delivery_hour', null, $hours)
				->setRequired('Musíte vybrat čas.');
		}

		$minutes = [0 => '00', 15 => 15, 30 => 30, 45 => 45];

		if ($cart->getExpectedDelivery()) {

			/** @var Nette\Utils\DateTime $datetime */
			$datetime = $cart->getExpectedDelivery();

			$form->addSelect('delivery_minute', null, $minutes)
				->setValue(intval($datetime->format('i')))
				->setRequired('Musíte vybrat čas.');
		} else {
			$form->addSelect('delivery_minute', null, $minutes)
				->setRequired('Musíte vybrat čas.');
		}

		$form->addSubmit('refresh')->onClick[] = [$this, 'refresh'];

		$form->addSubmit('submit')->onClick[] = [$this, 'submitOrderFormSuccess'];

		return $form;
	}

	/**
	 * @param Nette\Forms\Controls\SubmitButton $submitButton
	 */
	public function refresh(Nette\Forms\Controls\SubmitButton $submitButton)
	{
		/** @var Form $form */
		$form = $submitButton->getForm();
		$values = $form->getValues();

		self::processForm($form, $values);

		$presenter = $form->getPresenter();

		$presenter->template->totalPrice = $this->cartManager->getPrice();
		$presenter->template->deliveryPrice = $this->cartManager->getDeliveryPrice();
		$presenter->template->baguettesPrice = $this->cartManager->getBaguettesPrice();

		$presenter->redrawControl('PricePanel');
	}

	/**
	 * @param Nette\Forms\Controls\SubmitButton $submitButton
	 */
	public function submitOrderFormSuccess(Nette\Forms\Controls\SubmitButton $submitButton)
	{
		/** @var Form $form */
		$form = $submitButton->getForm();
		$values = $form->getValues();
		$presenter = $form->getPresenter();

		$cart = self::processForm($form, $values);

		if ($cart) {
			$this->ordersManager->save($cart);
			$this->cartManager->truncate();

			$presenter->flashMessage('Objednávka přijata!', 'success');
			$presenter->redirect('this');
		}
	}

	/**
	 * @param Form $form
	 * @param $values
	 * @return Orders
	 */
	private function processForm(Form $form, $values)
	{

		$presenter = $form->getPresenter();

		/** @var Orders $cart */
		$cart = $this->cartManager->getCart();

		$cart->setBillingFirstName($values->billing_firstName);
		$cart->setBillingLastName($values->billing_lastName);
		$cart->setBillingEmail($values->billing_email);
		$cart->setBillingPhone($values->billing_phone);
		$cart->setBillingStreet($values->billing_street);
		$cart->setBillingTown($values->billing_town);
		$cart->setBillingPsc($values->billing_psc);
		$cart->setBillingProvince($values->billing_province);

		$addressDao = $cart->getBillingAddressDao();

		if ($values->differentDeliveryAddress) {
			$cart->setDifferentDelivery(true);

			$cart->setDeliveryFirstName($values->delivery_firstName);
			$cart->setDeliveryLastName($values->delivery_lastName);
			$cart->setDeliveryStreet($values->delivery_street);
			$cart->setDeliveryTown($values->delivery_town);
			$cart->setDeliveryPsc($values->delivery_psc);
			$cart->setDeliveryProvince($values->delivery_province);

			$addressDao = $cart->getDeliveryAddressDao();
		}

		$cart->setDeliveryType($values->deliveryType);
		$cart->setPaymentMethod($values->paymentMethod);
		$cart->setBaguettesPrice($this->cartManager->getBaguettesPrice());

		try {
			/** @var DistanceCache $distance */
			$distance = $this->distanceManager->getDistanceTo($addressDao);

			if ($distance->getDistance() > 100) {
				$presenter->flashMessage('Do vzdálenosti více jak 100km nerozvážíme.', 'danger');
				return false;
			}

			$cart->setDistance($distance->getDistance());

			$selectedTime = new Nette\Utils\DateTime();
			if ($values->delivery_day > 0) {
				$selectedTime->modify('+ ' . $values->delivery_day . 'day');
			}
			$selectedTime->setTime($values->delivery_hour, $values->delivery_minute);

			if (time() > $selectedTime->getTimestamp()) {
				$presenter->flashMessage('Vybrali jste čas z minulosti.', 'danger');
				return false;
			}

			$maxTime = new Nette\Utils\DateTime();
			$maxTime->setTime(21, 45, 1);
			$maxTime->modify('+ 3 days');

			if ($selectedTime->getTimestamp() < time() OR $selectedTime->getTimestamp() > $maxTime->getTimestamp()) {
				$presenter->flashMessage('Neplatné datum.', 'danger');
				return false;
			}

			if ($values->deliveryType == 'delivery') {
				$expected = $selectedTime->getTimestamp();
				if (time() > ($expected - 1200 - $distance->getDeliveryTime())) {
					$presenter->flashMessage('Na zadanou adresu nelze v tak krátkém čase doručit objednávku.', 'danger');
					return false;
				}
			} elseif ($values->deliveryType == 'self') {
				$expected = $selectedTime->getTimestamp();
				if (time() > ($expected - 1200)) {
					$presenter->flashMessage('V tak krátkém čase nedokážeme připravit objednávku. Příprava trva alespoň 20 minut.', 'danger');
					return false;
				}
				$cart->setDistance(0);
			}

			$cart->setExpectedDelivery($selectedTime);
		} catch (DistanceManagerException $e) {
			$presenter->flashMessage('Objednávku nelze dokončit: ' . $e->getMessage(), 'danger');
			return $cart;
		}

		return $cart;
	}
}