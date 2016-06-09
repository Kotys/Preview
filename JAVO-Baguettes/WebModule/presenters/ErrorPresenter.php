<?php

namespace App\Presenters;

use Nette;
use Tracy\ILogger;

/**
 * Class ErrorPresenter
 * @package App\WebModule\Presenters
 */
class ErrorPresenter extends Nette\Application\UI\Presenter
{
	/** @var ILogger */
	private $logger;

	/**
	 * ErrorPresenter constructor.
	 * @param ILogger $logger
	 */
	public function __construct(ILogger $logger)
	{
		$this->logger = $logger;
	}


	/**
	 * @param  \Exception
	 * @return void
	 */
	public function renderDefault($exception)
	{

		if ($exception instanceof Nette\Application\BadRequestException) {
			$this->template->code = $exception->getCode();
			$code = $exception->getCode();
			$this->setView(in_array($code, [403, 404, 405, 410, 500]) ? $code : '4xx');
		} else {
			$this->template->code = 500;
			$this->setView('500');
			$this->logger->log($exception, ILogger::EXCEPTION);
		}

		if ($this->isAjax()) {
			$this->payload->error = TRUE;
			$this->terminate();
		}
	}
}
