<?php
    /**
     * Class EntityAwareInterface
     * @package Application\View\Manager\Interfaces
     *
     * since: 30.07.2020
     * author: alexej@kisselev.de
     */

    namespace Application\View\Manager\Interfaces;


    interface EntityAwareInterface
    {
        /**
         * Current entity model
         * @return string
         */
        public function getEntityName(): string;
    }