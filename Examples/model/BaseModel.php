<?php
/**
 * @author Jan Kotrba <kotrba@kotyslab.cz>
 */

namespace Mionet\Admin\Model;

use Kdyby\Doctrine\EntityManager;
use Kdyby\Translation\Translator;
use Nette;

/**
 * Class BaseModel
 * @package Mionet\Admin\Model
 */
abstract class BaseModel extends Nette\Object
{
	/**
	 * @var Translator
	 */
	protected $translator;

	/**
	 * @var \Kdyby\Doctrine\EntityRepository
	 */
	protected $repository;

	/**
	 * @var EntityManager
	 */
	protected $em;

	/**
	 * BaseModel constructor.
	 * @param Translator $translator
	 * @param EntityManager $entityManager
	 */
	public function __construct(Translator $translator, EntityManager $entityManager)
	{
		$this->translator = $translator;
		$this->em = $entityManager;
	}

	/**
	 * @param array $criteria
	 * @param array|null $orderBy
	 * @param null $limit
	 * @param null $offset
	 * @return array
	 */
	public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
	{
		return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
	}

	/**
	 * @param array $criteria
	 * @param array|null $orderBy
	 * @return mixed|null|object
	 */
	public function findOneBy(array $criteria, array $orderBy = null)
	{
		return $this->repository->findOneBy($criteria, $orderBy);
	}

	/**
	 * @param array $criteria
	 * @return int
	 */
	public function countBy(array $criteria = [])
	{
		return $this->repository->countBy($criteria);
	}

	/**
	 * @return array
	 */
	public function findAll()
	{
		return $this->repository->findAll();
	}

	/**
	 * @param $criteria
	 * @param null $value
	 * @param array $orderBy
	 * @param null $key
	 * @return array
	 */
	public function findPairs($criteria, $value = NULL, $orderBy = [], $key = NULL)
	{
		return $this->repository->findPairs($criteria, $value, $orderBy, $key);
	}

	/**
	 * @param $id
	 * @param null $lockMode
	 * @param null $lockVersion
	 * @return null|object
	 */
	public function find($id, $lockMode = null, $lockVersion = null)
	{
		return $this->repository->find($id, $lockMode, $lockVersion);
	}
}