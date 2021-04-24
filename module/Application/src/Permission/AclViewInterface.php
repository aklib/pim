<?php
    /**
     * Class AclViewInterface
     * @package Application\ServiceManager\Interfaces
     *
     * since: 30.07.2020
     * author: alexej@kisselev.de
     */

    namespace Application\Permission;


    use Laminas\Permissions\Acl\AclInterface;

    interface AclViewInterface extends AclInterface
    {
        public function isAllowedColumn($role = null, string $resource = null, string $column = null): bool;
    }