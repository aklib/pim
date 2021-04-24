<?php

    /**
     * ServiceManagerAware.php
     *
     * @since 04.05.2018
     * @author Alexej Kisselev <alexej.kisselev@gmail.com>
     */

    namespace Application\ServiceManager\Interfaces;

    use Psr\Container\ContainerInterface;

    interface ServiceManagerAware
    {
        public function getServiceManager(): ContainerInterface;
        public function setServiceManager(ContainerInterface $sm): void;
    }
