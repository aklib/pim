<?php

/**
 * Class ProfileController
 * @package User\Controller
 *
 * since: 03.08.2020
 * author: alexej@kisselev.de
 */

namespace User\Controller;


use Application\Controller\AbstractModuleController;
use Exception;
use Laminas\Form\Form;
use Laminas\View\Model\ViewModel;
use User\Form\UserEdit;
use User\Form\UserPasswordChange;
use User\Service\UserManager;

class ProfileController extends AbstractUserController
{
    /**
     * @param object|null $entity
     * @param Form|null $form
     * @param array|null $options
     * @return ViewModel
     * @noinspection PhpUnused
     * @noinspection PhpUnhandledExceptionInspection
     * @throws Exception
     */
    public function editAction(?object $entity = null, ?Form $form = null, ?array $options = []): ViewModel
    {
        $user = $this->identity();
        /** @var UserPasswordChange $createForm */
        $formE = $this->getServiceManager()->get(UserEdit::class);
        $formE->createForm($this->identity());
        return parent::editAction($user, $formE);
    }

    /**
     * Change own password
     * @return ViewModel
     * @throws Exception
     * @noinspection PhpUnhandledExceptionInspection
     */
    public function passwordAction(): ViewModel
    {
        $user = $this->identity();
        /** @var UserPasswordChange $createForm */
        $formP = $this->getServiceManager()->get(UserPasswordChange::class);
        $formP->createForm($this->identity());
        return parent::editAction($user, $formP);
    }
}