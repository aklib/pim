<?php

    /**
     *
     * Identity.php
     *
     * @since 19.12.2019
     * @author Alexej Kisselev <alexej.kisselev@gmail.com>
     */

    namespace Application\View\Helper;

    use Application\ServiceManager\Interfaces\EntityManagerAware;
    use Doctrine\ORM\EntityManager;
    use Laminas\Authentication\AuthenticationService;
    use User\Entity\User;

    class Identity extends AbstractHelperAware implements EntityManagerAware
    {
        private EntityManager $em;
        private ?User $user = null;
        private bool $done = false;

        public function __invoke()
        {
            if ($this->user instanceof User || $this->done) {
                return $this->user;
            }
            $this->done = true;
            if (!$this->getServiceManager()->has('authentication')) {
                return null;
            }
            /** @var AuthenticationService $auth */
            $auth = $this->getServiceManager()->get('authentication');
            if (!$auth->hasIdentity()) {
                return null;
            }
            $this->user = $auth->getIdentity();
            if ($this->user === null) {
                // Oops.. the identity presents in session, but there is no such user in database.
                // We remove an identity, because this is a possible security problem.
                $auth->clearIdentity();
                return null;
            }
            // Return found User.
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
