<?php
/**
 * Copyright (c) 2016 Jan Kotrba https://jkotrba.net/
 */

namespace App\WebModule\Model;

use App\Model\AddressDao;
use Kdyby\Doctrine\EntityManager;
use App\Model\Entities\DistanceCache;
use Nette;

/**
 * Class DistanceManager
 * @package App\WebModule\Model
 */
class DistanceManager extends Nette\Object
{

	/**
	 * @var AddressDao
	 */
	private $javoAddressDao;

	/**
	 * @var EntityManager
	 */
	private $entityManager;

	/**
	 * DistanceManager constructor.
	 * @param EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
		$this->javoAddressDao = new AddressDao('Rožnov pod Radhoštěm', '75661', 'Zemědělská 1077', 'Česká Republika');
	}


	private function sendRequest($url, $jsonAssoc = false)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$response = curl_exec($ch);

		if (!$response) {
			curl_close($ch);
			throw new NoResponseException('Nebylo možné navázat spojení s Google Maps API. Zkuste to později.');
		}

		curl_close($ch);
		return json_decode($response, $jsonAssoc);
	}

	/**
	 * @param $lat1
	 * @param $lat2
	 * @param $long1
	 * @param $long2
	 * @return mixed
	 * @throws NoResponseException
	 */
	public function getDrivingDistance($lat1, $lat2, $long1, $long2)
	{
		$url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $lat1 . "," . $long1 . "&destinations=" . $lat2 . "," . $long2 . "&mode=driving&language=cs-CZ";
		$response = $this->sendRequest($url, true);

		if (!$response) {
			throw new NoResponseException('Nebylo možné navázat spojení s Google Maps API. Zkuste to později.');
		}

		return (object)[
			'distance' => $response['rows'][0]['elements'][0]['distance']['value'],
			'duration' => $response['rows'][0]['elements'][0]['duration']['value']
		];
	}

	/**
	 * @param AddressDao $addressDao
	 * @return array
	 * @throws DistanceManagerException
	 * @throws NoResponseException
	 */
	private function getCoordinates(AddressDao $addressDao)
	{

		$address = urlencode($addressDao->getCity() . ' ' . $addressDao->getPsc() . ',' . $addressDao->getStreet() . ',' . $addressDao->getProvince());
		$url = "http://maps.google.com/maps/api/geocode/json?address=$address&sensor=false&region=Czech+Republic";

		$response = $this->sendRequest($url);
		$status = $response->status;

		if ($status == 'ZERO_RESULTS') {
			throw new DistanceManagerException('Adresu <b>' . $addressDao->getStreet() . ', ' . $addressDao->getCity() . ', ' . $addressDao->getProvince() . '</b> nelze vyhledat v Google Maps.');
		} else {
			$return = array('lat' => $response->results[0]->geometry->location->lat, 'long' => $long = $response->results[0]->geometry->location->lng);
			return $return;
		}
	}

	/**
	 * @param AddressDao $addressDao
	 * @return bool
	 * @throws DistanceManagerException
	 */
	public function verifyAddress(AddressDao $addressDao)
	{
		if ($this->getCoordinates($addressDao)) {
			return true;
		}
		return false;
	}

	/**
	 * @param AddressDao $to
	 * @return float
	 * @throws DistanceManagerException
	 * @throws \Exception
	 */
	public function getDistanceTo(AddressDao $to)
	{
		$cache = $this->entityManager->getRepository(DistanceCache::getClassName())->findOneBy([
			'toCity' => $to->getCity(),
			'toStreet' => $to->getStreet(),
			'toProvince' => $to->getProvince()
		]);

		if (!$cache OR $cache->isExpired()) {
			$javoCoordinates = $this->getCoordinates($this->javoAddressDao);
			$customerCoordinates = $this->getCoordinates($to);
			$distanceResult = $this->getDrivingDistance($javoCoordinates['lat'], $customerCoordinates['lat'], $javoCoordinates['long'], $customerCoordinates['long']);

			$distance = round($distanceResult->distance / 1000, 1, PHP_ROUND_HALF_UP);

			$distanceCache = new DistanceCache();

			$distanceCache->setFromCity($this->javoAddressDao->getCity());
			$distanceCache->setFromStreet($this->javoAddressDao->getStreet());
			$distanceCache->setFromProvince($this->javoAddressDao->getProvince());

			$distanceCache->setToCity($to->getCity());
			$distanceCache->setToStreet($to->getStreet());
			$distanceCache->setToProvince($to->getProvince());

			$distanceCache->setDistance($distance);
			$distanceCache->setDeliveryTime($distanceResult->duration);

			if ($cache AND $cache->isExpired()) {
				$this->entityManager->remove($cache);
				$this->entityManager->flush();
			}

			$this->entityManager->persist($distanceCache);
			$this->entityManager->flush();

			return $distanceCache;
		}

		return $cache;
	}
}

/**
 * Class DistanceManagerException
 * @package App\WebModule\Model
 */
class DistanceManagerException extends \Exception
{
}

/**
 * Class NoResponseException
 * @package App\WebModule\Model
 */
class NoResponseException extends DistanceManagerException
{
}