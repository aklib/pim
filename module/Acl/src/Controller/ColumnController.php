<?php
/**
 * Class ColumnController
 * @package Acl\Controller
 *
 * since: 31.07.2020
 * author: alexej@kisselev.de
 */

namespace Acl\Controller;


use Acl\Entity\AclResource;
use Application\View\Manager\ViewManager;

class ColumnController extends MVCController
{
    public function getResourceType(): string
    {
        return AclResource::RESOURCE_TYPE_COLUMN;
    }

    protected function getPrivileges(string $controllerClass): array
    {
        /*switch ($controllerClass) {
            case __CLASS__:
            case MVCController::class:
                return [];
        }*/

        /** @var ViewManager $manager */
        $manager = $this->getServiceManager()->get(ViewManager::class);
        $adapterName = $manager->getAdapterName($controllerClass);
        if (!$this->getServiceManager()->has($adapterName)) {
            return [];
        }
        $manager->setAdapter($this->getServiceManager()->get($adapterName));
        $columns = $manager->getColumns();
        $privileges = [];
        if (!empty($columns)) {
            $privileges = array_map('strtolower', array_keys($columns));
        }
        return $privileges;
    }
}