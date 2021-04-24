<?php
/**
 * Class ApplivationAuthenticationService
 * @package User\Service * since: 12.07.2020
 * author: alexej@kisselev.de
 */

namespace User\Service;


use Application\ServiceManager\Interfaces\EntityManagerAware;
use Doctrine\ORM\EntityManager;
use Laminas\Authentication\AuthenticationService;
use User\Entity\User;
use User\Repository\UserDao;

class AuthService extends AuthenticationService implements EntityManagerAware
{
    /**
     * Entity manager.
     * @var EntityManager
     */
    private EntityManager $em;

    /**
     * @var User
     */
    private User $user;
    private bool $initialized = false;

    public function getIdentity()
    {
        if (!$this->hasIdentity()) {
            return null;
        }
        $email = parent::getIdentity();
        if (!$this->initialized) {
            /** @var UserDao $dao */
            $dao = $this->getEntityManager()->getRepository(User::class);
            $this->user = $dao->getAuthenticationUser($email);
        }
        $this->initialized = true;

        return $this->user;
    }

    public function getEntityManager(): EntityManager
    {
        return $this->em;
    }

    public function setEntityManager(EntityManager $em): void
    {
        $this->em = $em;
    }
}