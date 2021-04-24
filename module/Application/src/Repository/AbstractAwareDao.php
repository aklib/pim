<?php

    /**
     *
     * AbstractDAO.php
     *
     * @since  14.05.2017
     * @author Alexej Kisselev <alexej.kisselev@gmail.com>
     */

    namespace Application\Repository;

    use Acl\Service\AclService;
    use Application\ServiceManager\Interfaces\AuthenticationAware;
    use Application\ServiceManager\Interfaces\ServiceManagerAware;
    use Doctrine\ORM\EntityRepository;
    use Psr\Container\ContainerInterface;
    use User\Entity\User;

    abstract class AbstractAwareDao extends EntityRepository implements ServiceManagerAware, AuthenticationAware
    {
        private ContainerInterface $sm;
        private User $user;

        /**
         * AuthenticationAware implementation
         * @return User|null
         */
        public function getCurrentUser(): User
        {
            return $this->user;
        }

        /**
         * AuthenticationAware implementation
         * @param User $user
         * @return void
         */
        public function setCurrentUser(User $user): void
        {
            $this->user = $user;
        }

        /**
         * ServiceManagerAware implementation
         * @return ContainerInterface
         */
        public function getServiceManager(): ContainerInterface
        {
            return $this->sm;
        }

        /**
         * ServiceManagerAware implementation
         * @param ContainerInterface $sm
         * @return void
         */
        public function setServiceManager(ContainerInterface $sm): void
        {
            $this->sm = $sm;
        }

        /**
         *
         * @return AclService
         */
        public function getAcl(): ?AclService
        {
            if ($this->getServiceManager()->has('acl')) {
                return $this->getServiceManager()->get('acl');
            }
            return null;
        }
    }
