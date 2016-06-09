<?php

namespace App\Admin\Model;

use Nette;
use Kdyby\Doctrine\EntityManager;
use App\Admin\Model\Entities\Event;
use Nette\Utils\Paginator;
use Doctrine\ORM\ORMException;

/**
 * Class EventManager
 * @package App\Admin\Model
 */
class EventManager extends Nette\Object
{

	/**
	 * @var EntityManager
	 */
	private $entityManager;

	/**
	 * @var \Kdyby\Doctrine\EntityRepository
	 */
	private $repository;

	/**
	 * eventManager constructor.
	 * @param EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
		$this->repository = $this->entityManager->getRepository(Event::class);
	}

	/**
	 * @param $eventId
	 * @return mixed|null|object
	 */
	public function get($eventId)
	{
		return $this->repository->findOneBy(['id' => $eventId]);
	}


	/**
	 * @param Paginator|null $paginator
	 * @return array
	 * @throws EventPaginatorException
	 */
	public function getAll(Paginator &$paginator = null)
	{
		if ($paginator instanceof Paginator) {
			$itemCount = $this->repository->countBy();

			$page = $paginator->getPage();
			$paginator->setItemCount($itemCount);

			if ($paginator->getItemCount() > 0 AND $page > $paginator->getPageCount()) {
				throw new EventPaginatorException;
			}

			if ($itemCount == 0) {
				return [];
			}
			return $this->repository->findBy([], ['created' => 'DESC'], $paginator->getLength(), $paginator->getOffset());
		}

		return $this->repository->findBy([], ['created' => 'DESC']);
	}

	/**
	 * @param Event $event
	 * @throws SaveEventException
	 * @throws \Exception
	 */
	public function save(event $event)
	{
		try {
			$this->entityManager->persist($event);
			$this->entityManager->flush();
		} catch (ORMException $e) {
			throw new SaveEventException('Ukládání události selhalo.');
		}
	}


	/**
	 * @param Event $event
	 * @throws RemoveEventException
	 * @throws \Exception
	 */
	public function delete(event $event)
	{
		try {
			$this->entityManager->remove($event);
			$this->entityManager->flush();
		} catch (ORMException $e) {
			throw new RemoveEventException('Událost nelze smazat: pokus o smazání z databáze selhal.');
		}
	}
}

/**
 * Class SaveeventException
 * @package App\Admin\Model
 */
class SaveEventException extends EventException
{
}

/**
 * Class PaginatorException
 * @package App\Admin\Model
 */
class EventPaginatorException extends EventException
{
}

/**
 * Class RemoveeventExfeption
 * @package App\Admin\Model
 */
class RemoveEventException extends EventException
{
}

/**
 * Class EventException
 * @package App\Admin\Model
 */
class EventException extends \Exception
{
}