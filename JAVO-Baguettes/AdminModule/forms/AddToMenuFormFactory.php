<?php
/**
 * Copyright (c) 2016 Jan Kotrba https://jkotrba.net/
 */

namespace App\AdminModule\Components;

use App\AdminModule\Model\AdminBaguettesFacade;
use App\AdminModule\Model\BaguettesFacadeException;
use App\AdminModule\Model\ImageSaveException;
use Nette;
use Nette\Application\UI\Form;
use App\AdminModule\Model\Tinify;

/**
 * Class AddToMenuFormFactory
 * @package App\AdminModule\Components
 */
class AddToMenuFormFactory extends Nette\Object
{

	/**
	 * @var AdminBaguettesFacade
	 */
	private $baguettesFacade;

	/**
	 * @var \App\AdminModule\Model\Tinify
	 */
	private $tinifyApi;

	/**
	 * AddToMenuFormFactory constructor.
	 * @param AdminBaguettesFacade $baguettesFacade
	 * @param Tinify $tinifyApi
	 */
	public function __construct(AdminBaguettesFacade $baguettesFacade, Tinify $tinifyApi)
	{
		$this->baguettesFacade = $baguettesFacade;
		$this->tinifyApi = $tinifyApi;
	}

	/**
	 * @return Form
	 */
	public function create()
	{
		$form = new Form();

		$form->addText('name')
			->setRequired('Musíte zadat název bagety.');

		$form->addTextArea('temptation')
			->setRequired('Musíte zadat lákací popisek.');

		$form->addTextArea('desc')
			->setRequired('Musíte zadat popis.');

		$form->addText('price')
			->addCondition(Form::FILLED, true)
			->addRule(Form::NUMERIC, 'Cena musí být celočíselná.');

		$form->addSelect('priority', null, [
			'3' => 'Nejvyšší',
			'2' => 'Normální',
			'1' => 'Nízká'
		])->setDefaultValue(3);

		$form->addSelect('bakedGoods', null, $this->baguettesFacade->getResourcesByType('baked-goods', true))
			->setRequired('Musíte zvolit pečivo.');

		$form->addCheckboxList('fillings', null, $this->baguettesFacade->getResourcesByType('filling', true));

		$form->addCheckboxList('vegetable', null, $this->baguettesFacade->getResourcesByType('vegetable', true));

		$form->addCheckboxList('dressing', null, $this->baguettesFacade->getResourcesByType('dressing', true));

		$form->addUpload('imageLeft')
			->setRequired('Musíte zvolit náhled pro zobrazení na levé straně menu.');

		$form->addUpload('imageRight')
			->setRequired('Musíte zvolit náhled pro zobrazení na pravé straně menu.');

		$form->addSubmit('add');

		$form->onSuccess[] = [$this, 'addToMenuFormSuccess'];

		return $form;
	}

	/**
	 * @param Form $form
	 * @param $values
	 * @return bool
	 */
	public function addToMenuFormSuccess(Form $form, $values)
	{
		$presenter = $form->getPresenter();

		/** @var Nette\Http\FileUpload $imageLeft */
		$imageLeft = $values->imageLeft;

		/** @var Nette\Http\FileUpload $imageRight */
		$imageRight = $values->imageRight;

		if ($imageLeft->getContentType() != "image/png" OR $imageRight->getContentType() != "image/png") {
			$presenter->flashMessage('Obrázky musejí být ve formátu PNG.', 'danger');
			return;
		}

		if ($imageLeft->isOk() AND $imageRight->isOk()) {
			$imageName = 'bageta-' . Nette\Utils\Strings::webalize($values->name) . '-' . Nette\Utils\Random::generate();
			$imageLeftName = $imageName . '-left.png';
			$imageRightName = $imageName . '-right.png';

			try {
				$baguette = $this->baguettesFacade->addMenuBaguette($values, $imageLeftName, $imageRightName);
			} catch (BaguettesFacadeException $e) {
				$presenter->flashMessage($e->getMessage(), 'danger');
				return;
			}

			try {
				if (!$this->tinifyApi->compress($imageLeft->getTemporaryFile(), 'upload/our-menu/' . $imageLeftName)) {
					$presenter->flashMessage('Komprese levého obrázku selhala.', 'info');
				}

				if (!$this->tinifyApi->compress($imageRight->getTemporaryFile(), 'upload/our-menu/' . $imageRightName)) {
					$presenter->flashMessage('Komprese pravého obrázku selhala.', 'info');
				}
			} catch (ImageSaveException $e) {
				$this->baguettesFacade->removeBaguette($baguette);

				@unlink('upload/our-menu/' . $imageLeftName);
				@unlink('upload/our-menu/' . $imageRightName);

				$presenter->flashMessage($e->getMessage(), 'danger');
				return;
			}

			$presenter->flashMessage('Bageta úspěšně přidána do menu.', 'success');
			$presenter->redirect('OurMenu:all');
		} else {
			$presenter->flashMessage('Obrázky se nepodařilo nahrát.', 'danger');
			return;
		}
	}
}