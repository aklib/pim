<?php

namespace User\Service;

use Application\ServiceManager\Interfaces\EntityManagerAware;
use Application\ServiceManager\Interfaces\ServiceManagerAware;
use Doctrine\ORM\EntityManager;
use Laminas\Authentication\Adapter\AdapterInterface;
use Laminas\Authentication\Result;
use Laminas\Crypt\Password\Bcrypt;
use Psr\Container\ContainerInterface;
use User\Entity\User;

/**
 * Adapter used for authenticating user. It takes login and password on input
 * and checks the database if there is a user with such login (email) and password.
 * If such user exists, the service returns its identity (email). The identity
 * is saved to session and can be retrieved later with Identity view helper provided
 * by ZF3.
 */
class AuthAdapter implements AdapterInterface, EntityManagerAware, ServiceManagerAware
{
    /**
     * User email.
     * @var string
     */
    private string $email;

    /**
     * Password
     * @var string
     */
    private string $password;

    /**
     * Entity manager.
     * @var EntityManager
     */
    private EntityManager $em;

    /**
     * Entity manager.
     * @var ContainerInterface
     */
    private ContainerInterface $sm;

    /**
     * Performs an authentication attempt.
     */
    public function authenticate(): Result
    {
        // Check the database if there is a user with such email.
        /** @var User $user */
        $user = $this->getEntityManager()->getRepository(User::class)->getAuthenticationUser($this->email);

        // If there is no such user, return 'Identity Not Found' status.
        if ($user === null) {
            return new Result(
                Result::FAILURE_IDENTITY_NOT_FOUND,
                null,
                ['Invalid credentials.']);
        }

        // If the user with such email exists, we need to check if it is active or retired.
        // Do not allow retired users to log in.
        if ($user->getStatus()->getId() !== User::STATUS_ACTIVE) {
            return new Result(
                Result::FAILURE,
                null,
                ['User is retired.']);
        }

        // Now we need to calculate hash based on user-entered password and compare
        // it with the password hash stored in database.
        $bcrypt = new Bcrypt();
        $passwordHash = $user->getPassword();

        if ($bcrypt->verify($this->password, $passwordHash)) {
            // Great! The password hash matches. Return user identity (email) to be
            // saved in session for later use.
            return new Result(
                Result::SUCCESS,
                $this->email,
                ['Authenticated successfully.']);
        }
        // If password check didn't pass return 'Invalid Credential' failure status.
        return new Result(
            Result::FAILURE_CREDENTIAL_INVALID,
            null,
            ['Invalid credentials.']);
    }

    /**
     * Sets user email.
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * Sets password.
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getEntityManager(): EntityManager
    {
        return $this->em;
    }

    public function setEntityManager(EntityManager $em): void
    {
        $this->em = $em;
    }

    public function getServiceManager(): ContainerInterface
    {
        return $this->sm;
    }

    public function setServiceManager(ContainerInterface $sm): void
    {
        $this->sm = $sm;
    }
}


