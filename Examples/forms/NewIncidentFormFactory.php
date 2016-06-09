<?php
/**
 * @author Jan Kotrba <jan.kotrbaa@gmail.com>
 */

namespace App\Admin\Components;

use App\Admin\Model\GalleryManager;
use Nette;
use App\Admin\Model\Entities\Incident;
use App\Admin\Model\IncidentManager;
use App\Admin\Model\UserManager;
use App\Admin\Model\SaveIncidentException;
use App\Admin\Model\UploadCoverException;
use Nette\Application\UI\Form;
use Tracy\Debugger;

/**
 * Class NewIncidentFormFactory
 * @package App\Admin\Components
 */
class NewIncidentFormFactory extends Nette\Object
{

	/**
	 * @var IncidentManager
	 */
	private $incidentManager;

	/**
	 * @var UserManager
	 */
	private $userManager;

	/**
	 * @var Nette\Security\User
	 */
	private $user;

	/**
	 * @var GalleryManager
	 */
	private $galleryManager;

	/**
	 * NewIncidentFormFactory constructor.
	 * @param IncidentManager $incidentManager
	 * @param Nette\Security\User $user
	 * @param UserManager $userManager
	 * @param GalleryManager $galleryManager
	 */
	public function __construct(IncidentManager $incidentManager, Nette\Security\User $user, UserManager $userManager, GalleryManager $galleryManager)
	{
		$this->incidentManager = $incidentManager;
		$this->userManager = $userManager;
		$this->user = $user;
		$this->galleryManager = $galleryManager;
	}

	/**
	 * @return Form
	 */
	public function create()
	{
		$form = new Form();

		$form->addText('title')
			->setRequired('Musíte zadat titulek události.');

		$form->addTextArea('text')
			->setRequired('Musíte zadat text události.');

		$form->addText('mapsLink');

		$form->addCheckbox('hookThisGallery');
		$form->addSelect('hookGallery', null, $this->galleryManager->getGalleriesArray())
			->setPrompt('Vyberte..')
			->addConditionOn($form['hookThisGallery'], Form::FILLED)
			->setRequired('Pokud chcete galerii přiřadit, musíte ji vybrat.');

		$form->addUpload('cover');

		$form->addSubmit('add');

		$form->getElementPrototype()->onsubmit('CKEDITOR.instances["js-ckeditor"].updateElement()');

		$form->onSuccess[] = [$this, 'newIncidentFormSuccess'];

		return $form;
	}

	/**
	 * @param Form $form
	 * @param $values
	 */
	public function newIncidentFormSuccess(Form $form, $values)
	{
		$presenter = $form->getPresenter();

		/**
		 * If any image
		 */
		if ($values->cover && $values->cover->isOk()) {
			try {
				$coverSrc = $this->incidentManager->uploadCover($values->cover);
			} catch (UploadCoverException $e) {
				$presenter->flashMessage($e->getMessage(), 'danger');
				return;
			}
		}

		$userDao = $this->userManager->findOneBy(['id' => $this->user->getId()]);

		$incident = new Incident();
		$incident->setTitle($values->title);
		$incident->setText($values->text);
		$incident->setMapsLink($values->mapsLink);
		$incident->setAuthor($userDao);

		if ($values->hookThisGallery) {
			if (!$values->hookGallery) {
				$presenter->flashMessage('Pokud chcete galerii přiřadit, musíte ji vybrat.', 'danger');
				return;
			}
			$gallery = $this->galleryManager->getGalleryById($values->hookGallery);
			$gallery->setHookIncident($incident);
			$incident->setGalleryHook($gallery);
		}

		if (isset($coverSrc)) {
			$incident->setCover($coverSrc);
		}

		try {
			$this->incidentManager->save($incident);

			$presenter->flashMessage('Událost byla úspěšně přidána.', 'success');
			$presenter->redirect('Incident:all');
		} catch (SaveIncidentException $e) {
			$presenter->flashMessage($e->getMessage(), 'danger');
		}
	}
}