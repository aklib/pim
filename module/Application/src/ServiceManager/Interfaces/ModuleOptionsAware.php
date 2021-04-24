<?php
    /**
     * Class ModuleOptionsAware
     * @package Application\ServiceManager\Interfaces
     * since: 01.07.2020
     * author: alexej@kisselev.de
     */

    namespace Application\ServiceManager\Interfaces;


    interface ModuleOptionsAware
    {
        public function setFromArray($options);
    }