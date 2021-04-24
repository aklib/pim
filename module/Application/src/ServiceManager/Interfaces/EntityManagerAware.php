<?php

    /**
     * EntityManagerAware.php
     *
     * @since 04.05.2018
     * @author Alexej Kisselev <alexej.kisselev@gmail.com>
     */

    namespace Application\ServiceManager\Interfaces;

    use Doctrine\ORM\EntityManager;

    interface EntityManagerAware
    {
        public function getEntityManager(): EntityManager;

        public function setEntityManager(EntityManager $em): void;
    }

