<?php /** @noinspection PhpUnused */
/** @noinspection TransitiveDependenciesUsageInspection */
/** @noinspection PhpUndefinedClassInspection */

/**
 * Class ApplicationAclService
 * @package Acl\Service * since: 19.07.2020
 * author: alexej@kisselev.de
 */

namespace Acl\Service;

use Acl\ModuleOptions;
use Acl\Role\AbstractAclRole;
use Application\Permission\AclViewInterface;
use Application\ServiceManager\AbstractAwareContainer;
use InvalidArgumentException;
use Laminas\Authentication\AuthenticationService;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use User\Entity\User;
use User\Entity\UserRole;
use User\Repository\UserRoleDao;


class AclService extends AbstractAwareContainer implements AclViewInterface
{
    private array $controllerMap = [];
    private array $roleMap = [];
    protected array $exclude = [];

    /**
     * AclAware implementation
     * @return AclService
     */
    public function getAcl(): AclService
    {
        return $this;
    }

    /**
     * AclAware implementation
     * @param AclService $acl
     */
    public function setAcl(AclService $acl): void
    {

    }

    public function hasAcl(): bool
    {
        return true;
    }

    /**
     * Set resources for current user.
     * Called in a factory
     */
    public function init(): void
    {
        /** @var ModuleOptions $options */
        $options = $this->getServiceManager()->get(ModuleOptions::class);
        $array = $options->getExclude();
        if (is_array($array)) {
            foreach ($array as $resource) {
                $this->exclude[] = $resource;
                $this->exclude[] = strtolower($resource);
            }
        }
        $this->createControllerMap();

        if (!$this->isAuthenticated()) {
            // not authenticated call
            return;
        }
        //$this->setAll();
        //$aclRole = $this->getAclRole($this->getCurrentUser()->getUserRole()->getName());
        /*Navigation::setDefaultAcl($aclRole);
        Navigation::setDefaultRole($this->getCurrentUser()->getUserRole()->getName());*/
    }

    public function toRoleString($role = null): string
    {
        $roleString = '';
        if ($role === null) {
            $roleString = $this->getCurrentUser()->getUserRole()->getName();
        } elseif ($role instanceof User) {
            $roleString = $role->getUserRole()->getName();
        } elseif ($role instanceof UserRole) {
            $roleString = $role->getName();
        } elseif (is_string($role)) {
            $roleString = $role;
        }
        return $roleString;
    }

    /**
     * AclInterface implementation
     * @param User|UserRole|string|null $role
     * @param string|null $resource
     * @param string|null $privilege
     * @return bool
     */
    public function isAllowed($role = null, $resource = null, $privilege = null): bool
    {
        $controller = $this->getRequestedControllerName();
        if ($this->isExcluded($controller)) {
            return true;
        }
        if (!$this->isAuthenticated()) {
            return false;
        }
        if ($resource === null) {
            // navigation, useAcl() set to false?!
            return true;
        }
        $real = $this->getRealResource($resource);

        if ($this->isExcluded($real)) {
            return true;
        }
        $roleString = $this->toRoleString($role);
        if (empty($roleString)) {
            return false;
        }
        $aclRole = $this->getAclRole($roleString);
        if (!$aclRole->hasResource($real, $privilege) && !$this->isDeveloper()) {
            return false;
        }
        /*$res = $aclRole->isAllowed($roleString, $real, $privilege) ? 'true' : 'false';
        dump($roleString, $real, $privilege, $roleString,$res,$aclRole);*/
        return $aclRole->isAllowed($real, $privilege);
    }

    /** @noinspection PhpParameterNameChangedDuringInheritanceInspection */
    public function isAllowedColumn($role = null, string $resource = null, string $privilege = null): bool
    {
        if (!$this->isAuthenticated()) {
            // not authenticated call eg. forward to login
            return false;
        }
        $roleString = $this->toRoleString($role);
        if (empty($roleString)) {
            return false;
        }
        if ($resource === null) {
            return false;
        }
        $real = $this->getRealResource($resource);
        return $this->getAclRole($roleString)->isAllowedColumn(strtolower($real), strtolower($privilege));
    }

    /**
     * @param ResourceInterface|string $resource
     * @param string|null $privilege
     * @return bool
     * @todo handle not-string resources
     */
    public function hasResource($resource, string $privilege = null): bool
    {
        $real = $this->getRealResource($resource);
        if ($real === null) {
            return false;
        }
        $role = $this->toRoleString();
        if (!array_key_exists($role, $this->roleMap)) {
            return false;
        }
        return $this->getAclRole($role)->hasResource($real, $privilege);
    }

    /**
     * @param $resource
     * @return bool
     */
    public function isExcluded($resource): bool
    {
        if (!str_contains($resource, "\\")) {
            $resource = $this->getRealResource($resource);
        }
        return in_array($resource, $this->exclude, true);
    }

    /**
     * Gets or creates role-allowed resources map
     * @param string $role
     * @return AbstractAclRole
     */
    private function getAclRole(string $role): AbstractAclRole
    {
        if (!empty($this->roleMap[$role])) {
            return $this->roleMap[$role];
        }
        if (!$this->getServiceManager()->has($role)) {
            throw new InvalidArgumentException(
                sprintf("%s: A role must match the 'user_role.name' field in the database. '%s' given",
                    __FUNCTION__, $role
                ));
        }
        /** @var AbstractAclRole $aclRole */
        $aclRole = $this->getServiceManager()->get($role);

        /** @var UserRole $userRole */
        $userRole = $this->getUserRoleByName($role);
        if ($userRole === null) {
            throw new InvalidArgumentException(
                sprintf("%s: A role must match the 'user_role.name' field in the database. '%s' given",
                    __FUNCTION__, $role
                ));
        }
        $resources = $userRole->getResources();
        $aclRole->setResources($resources);

        return $this->roleMap[$role] = $aclRole;
    }

    private function getUserRoleByName(string $role)
    {
        /** @var UserRoleDao $dao */
        $dao = $this->getEntityManager()->getRepository(UserRole::class);
        /** @var UserRole $userRole */
        return $dao->findOneByName($role);
    }

    /**
     * Gets real controller (resource) name
     * @param string $resource
     * @return mixed|null
     */
    protected function getRealResource(string $resource)
    {
        if (!empty($this->controllerMap[$resource])) {
            return $this->controllerMap[$resource];
        }
        return null;
    }

    protected function createControllerMap(): void
    {
        if (!empty($this->controllerMap)) {
            return;
        }
        // init controller map
        $config = $this->getServiceManager()->get('Config')['controllers'];
        if (!empty($config['controllers']['invokables'])) {
            $controllers = (array)$config['invokables'];
            foreach ($controllers as $alias => $name) {
                $this->controllerMap[$alias] = strtolower($name);
                $this->controllerMap[strtolower($alias)] = $this->controllerMap[$alias];
            }
        }
        if (!empty($config['factories'])) {
            $controllers = (array)$config['factories'];
            foreach ($controllers as $alias => $factory) {
                $this->controllerMap[$alias] = strtolower($alias);
                $this->controllerMap[strtolower($alias)] = $this->controllerMap[$alias];
            }
        }
        if (!empty($config['aliases'])) {
            $controllers = (array)$config['aliases'];
            foreach ($controllers as $alias => $name) {
                $this->controllerMap[$alias] = strtolower($name);
                $this->controllerMap[strtolower($alias)] = $this->controllerMap[$alias];
            }
        }
    }

    /**
     * @return array
     */
    public function getControllerMap(): array
    {
        if (empty($this->controllerMap)) {
            $this->createControllerMap();
        }
        return $this->controllerMap;
    }


    public function isDeveloper(User $user = null): bool
    {
        if ($user === null) {
            $user = $this->getCurrentUser();
        }
        return $user->getUserRole()->getName() === UserRole::USER_ROLE_DEVELOPER;
    }

    public function isAdvertiser(User $user = null): bool
    {
        if ($user === null) {
            $user = $this->getCurrentUser();
        }
        return $user->getUserRole()->getName() === UserRole::USER_ROLE_ADVERTISER;
    }

    public function isPublisher(User $user = null): bool
    {
        if ($user === null) {
            $user = $this->getCurrentUser();
        }
        return $user->getUserRole()->getName() === UserRole::USER_ROLE_PUBLISHER;
    }

    public function isAdmin(User $user = null): bool
    {
        if ($user === null) {
            $user = $this->getCurrentUser();
        }
        return $user->getUserRole()->getName() === UserRole::USER_ROLE_ADMIN;
    }

    public function isManager(User $user = null): bool
    {
        if ($user === null) {
            $user = $this->getCurrentUser();
        }
        return $user->getUserRole()->getName() === UserRole::USER_ROLE_MANAGER;
    }

    public function isRedactor(User $user = null): bool
    {
        if ($user === null) {
            $user = $this->getCurrentUser();
        }
        return $user->getUserRole()->getName() === UserRole::USER_ROLE_REDACTOR;
    }

    /**
     * @param User|null $user
     * @return bool
     * @deprecated
     * @noinspection PhpUnused
     */
    public function isRestricted(User $user = null): bool
    {
        if ($user === null) {
            return $this->getCurrentUser()->getUserRole()->isRestricted();
        }
        return $user->getUserRole()->isRestricted();
    }

    protected function getRequestedControllerName()
    {
        $controller = $this->getRouteMatch()->getParam('controller');
        return $this->getRealResource($controller);
    }

    protected function isAuthenticated(): bool
    {
        /** @var AuthenticationService $authenticationService */
        $authenticationService = $this->getServiceManager()->get(AuthenticationService::class);
        return $authenticationService->hasIdentity();
    }
}