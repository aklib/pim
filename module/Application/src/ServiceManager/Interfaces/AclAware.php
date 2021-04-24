<?php
    /**
     * Class AclAware
     * @package Application\ServiceManager\Interfaces
     *
     * since: 30.07.2020
     * author: alexej@kisselev.de
     */

    namespace Application\ServiceManager\Interfaces;


    use Acl\Service\AclService;

    interface AclAware
    {
        public function getAcl(): AclService;
        public function setAcl(AclService $acl): void;
        public function hasAcl(): bool;
    }