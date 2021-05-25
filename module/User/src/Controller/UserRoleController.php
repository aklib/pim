<?php
/**
 * Class UserRoleController
 * @package User\Controller
 *
 * since: 18.05.2021
 * author: alexej@kisselev.de
 */

namespace User\Controller;


use Application\Controller\AbstractModuleController;
use Laminas\Form\Form;
use Laminas\View\Model\ViewModel;
use User\Entity\User;
use User\Form\UserCreate;

class UserRoleController extends AbstractModuleController
{
    public function editAction(?object $entity = null, ?Form $form = null, ?array $options = []): ViewModel
    {
        return parent::editAction($entity, $form);
    }

    /**
     * @return ViewModel
     * @throws \Exception
     */
    public function createAction(): ViewModel
    {
        $user = new User();
        /** @var UserCreate $createForm */
        $createForm = $this->getServiceManager()->get(UserCreate::class);
        $createForm->createForm($user);
        return $this->editAction($user, $createForm);
    }
}