<?php /** @noinspection UnknownInspectionInspection */

namespace User\Controller;

use Application\Form\StatusEdit;
use Company\Entity\Advertiser;
use Company\Entity\Publisher;
use Exception;
use Laminas\Form\Form;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Laminas\View\Model\ViewModel;
use User\Entity\User;
use User\Form\UserCreate;
use User\Form\UserPasswordChange;
use User\Repository\UserDao;

/**
 * This controller is responsible for user management (adding, editing,
 * viewing users and changing user's own password).
 */
class UserController extends AbstractUserController
{

    public function editAction(?object $entity = null, ?Form $form = null, ?array $options = []): ViewModel
    {
        return parent::editAction($entity, $form);
    }

    /**
     * @return ViewModel
     * @throws Exception
     */
    public function createAction(): ViewModel
    {
        $user = new User();
        /** @var UserCreate $createForm */
        $createForm = $this->getServiceManager()->get(UserCreate::class);
        $createForm->createForm($user);
        return $this->editAction($user, $createForm);
    }

    /**
     * @return ViewModel
     * @throws Exception
     * @noinspection PhpUnused
     */
    public function changePasswordAction(): ViewModel
    {
        $user = $this->identity();
        /** @var UserPasswordChange $createForm */
        $form = $this->getServiceManager()->get(UserPasswordChange::class);
        $form->createForm($this->identity());
        return $this->editAction($user, $form);
    }

    /**
     * @param object|null $entity
     * @param Form|null $form
     * @param array|null $options
     * @return ViewModel
     * @throws Exception
     */
    public function statusAction(?object $entity = null, ?Form $form = null, ?array $options = []): ViewModel
    {
        $viewModel = parent::editAction($entity, $form);
        /** @var StatusEdit $form */
        $form = $viewModel->getVariable('form');
        $form->setAttribute('action', $this->urlx(['action' => 'status']));
        return $viewModel;
    }

    /**
     * @Override Don't redirect to edit user after updating own profile
     * @param string|null $actionName
     * @param string|null $controllerName
     * @param int|null $id
     * @return string
     */
    protected function getURL(string $actionName = null, string $controllerName = null, int $id = null): string
    {
        /** @noinspection DegradedSwitchInspection */
        switch ($this->getActionName()) {
            case 'profile':
                //  do not redirect to edit by id
                if ($actionName === 'edit') {
                    return $this->urlx();
                }
        }
        return parent::getURL($actionName, $controllerName, $id);
    }
}


