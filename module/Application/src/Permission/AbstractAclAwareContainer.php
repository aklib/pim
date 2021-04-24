<?php /** @noinspection PhpUnused */

    /**
     * Class AbstractAclContainer
     *
     * @since 21.07.2020
     * @author Alexej Kisselev <alexej.kisselev@gmail.com>
     * @TODO modify or delete the class. Used for TabModel
     */

    namespace Application\Permission;

    use Laminas\Permissions\Acl\AclInterface;

    abstract class AbstractAclAwareContainer implements AclInterface
    {
        /**
         *
         * @var bool
         */
        protected bool $useAcl = false;
        /**
         *
         * @var string null|module|controller
         */
        protected ?string $resource = null;
        /**
         *
         * @var string (action)
         */
        protected ?string $privilege = null;

        /**
         *
         * @var string (action)
         */
        protected ?string $role = null;
        /**
         *
         * @var AclInterface
         */
        protected AclInterface $acl;

        /**
         * @return bool
         */
        public function isUseAcl(): bool
        {
            return $this->useAcl;
        }

        /**
         * @param bool $useAcl
         */
        public function setUseAcl(bool $useAcl): void
        {
            $this->useAcl = $useAcl;
        }

        /**
         * @return string
         */
        public function getResource(): string
        {
            return $this->resource;
        }

        /**
         * @param string $resource
         */
        public function setResource(?string $resource): void
        {
            $this->resource = $resource;
        }

        /**
         * @return string
         */
        public function getPrivilege(): string
        {
            return $this->privilege;
        }

        /**
         * @param string $privilege
         */
        public function setPrivilege(?string $privilege): void
        {
            $this->privilege = $privilege;
        }

        /**
         * @return string
         */
        public function getRole(): string
        {
            return $this->role;
        }

        /**
         * @param string $role
         */
        public function setRole(?string $role): void
        {
            $this->role = $role;
        }

        /**
         * @return AclInterface
         */
        public function getAcl(): AclInterface
        {
            return $this->acl;
        }

        /**
         * @param AclInterface $acl
         */
        public function setAcl(AclInterface $acl): void
        {
            $this->acl = $acl;
        }

        /** @noinspection ReturnTypeCanBeDeclaredInspection */
        public function hasResource($resource)
        {
            if (!$this->useAcl) {
                return true;
            }
            if ($this->acl instanceof AclInterface) {
                return $this->acl->hasResource($this->getResource());
            }
            return false;
        }

        /** @noinspection ReturnTypeCanBeDeclaredInspection */
        public function isAllowed($role = null, $resource = null, $privilege = null)
        {
            if (!$this->useAcl) {
                return true;
            }
            if ($this->acl instanceof AclInterface) {
                return $this->acl->isAllowed($this->getRole(), $this->getResource(), $this->getPrivilege());
            }
            return false;
        }
    }
