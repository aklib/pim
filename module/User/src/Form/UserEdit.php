<?php
/**
 * Class UserEdit
 * @package User\Form
 * since: 05.07.2020
 * author: alexej@kisselev.de
 */

namespace User\Form;

use Application\Form\AbstractEntityForm;
use Application\View\Manager\ViewManager;
use User\Adapter\UserAdapter;

class UserEdit extends AbstractEntityForm
{
    protected function isPropertyShown($name): bool
    {
        $yes = parent::isPropertyShown($name);
        if (!$yes) {
            return false;
        }
        switch ($name) {
            case 'email':
            case 'password':
            case 'passwordResetToken':
            case 'passwordResetTokenCreationDate':
            case 'publishers':
            case 'advertisers':
                return false;
        }
        /** @var ViewManager $viewManager */
        $viewManager = $this->getServiceManager()->get(ViewManager::class);

        /** @var UserAdapter $adapter */
        $adapter = $this->getServiceManager()->get($viewManager->getAdapterName());
        return $adapter->isColumnVisible($name);
    }

    protected function postCreateForm(object $entity): void
    {
        parent::postCreateForm($entity);

    }
}