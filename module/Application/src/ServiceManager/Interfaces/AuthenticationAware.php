<?php

    /**
     * AuthenticationAware.php
     *
     * @since 15.06.2020
     * @author Alexej Kisselev <alexej.kisselev@gmail.com>
     */

    namespace Application\ServiceManager\Interfaces;

    use User\Entity\User;

    interface AuthenticationAware
    {
        public function getCurrentUser(): User;

        public function setCurrentUser(User $user): void;
    }
