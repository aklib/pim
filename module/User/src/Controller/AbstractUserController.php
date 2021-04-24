<?php
/**
 * Class AbstractUserController
 * @package User\Controller
 *
 * since: 20.01.2021
 * author: alexej@kisselev.de
 */

namespace User\Controller;


use Application\Controller\AbstractModuleController;
use User\Service\UserManager;

class AbstractUserController extends AbstractModuleController
{
    /**
     * Encrypt password before save
     * @param array $data
     * @param object|null $entity
     * @return array
     */
    protected function postValidateFormData(array $data, object $entity = null): array
    {
        /** @var UserManager $userManager */
        $userManager = $this->getServiceManager()->get(UserManager::class);
        $dataCopy = parent::postValidateFormData($data);
        if (isset($dataCopy['new_password']) && !$userManager->isEncrypted($dataCopy['new_password'])) {
            //change password action
            $dataCopy['password'] = $userManager->createPasswordHash($dataCopy['new_password']);
        } elseif (isset($dataCopy['password']) && !$userManager->isEncrypted($dataCopy['password'])) {
            //create user action
            $dataCopy['password'] = $userManager->createPasswordHash($dataCopy['password']);
        }
        return $dataCopy;
    }
}