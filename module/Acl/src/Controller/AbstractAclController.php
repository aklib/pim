<?php

/**
 *
 * AbstractAclController.php
 *
 * @since 12.06.2017
 * @author Alexej Kisselev <alexej.kisselev@gmail.com>
 */

namespace Acl\Controller;

use Acl\Entity\AclResource;
use Acl\Repository\AclResourceDao;
use Acl\Service\AclService;
use Application\Controller\AbstractModuleController;
use Doctrine\Common\Collections\ArrayCollection;
use FlorianWolters\Component\Core\StringUtils;
use Laminas\Form\Element\Collection;
use ReflectionClass;
use ReflectionException;
use User\Entity\UserRole;
use User\Repository\UserRoleDao;

abstract class AbstractAclController extends AbstractModuleController
{
    private array $roles = [];


    /**
     * Gets roles to set access
     * @param bool $byPriority
     * @return UserRole[] of Application\Entity\UserRole
     */
    public function getRoleIdMap(bool $byPriority = false): array
    {
        if (count($this->roles) === 0) {
            /** @var UserRoleDao $roleDao */
            $roleDao = $this->getEntityManager()->getRepository(UserRole::class);
            $roleList = $roleDao->findAll();

            foreach ($roleList as $role) {
                $this->roles[$role->getId()] = $role;
            }
        }
        if ($byPriority) {
            $rolesByPriority = [];
            /** @noinspection NullPointerExceptionInspection */
            $currentRole = $this->identity()->getUserRole();
            /** @var UserRole $role */
            foreach ($this->roles as $role) {
                if ($role->getPriority() < $currentRole->getPriority()) {
                    $rolesByPriority[] = $role;
                }
            }
            return $rolesByPriority;
        }
        return $this->roles;
    }

    /**
     * @return UserRole
     */
    public function getDeveloperRole(): UserRole
    {
        return $this->getRoleIdMap(false)[1];
    }

    /**
     * Gets resources to set access
     * @return ArrayCollection
     */
    protected function getSavedResourcesByType(): ArrayCollection
    {
        /** @var AclResourceDao $dao */
        $dao = $this->getEntityManager()->getRepository(AclResource::class);
        return new ArrayCollection($dao->findBy(['type' => $this->getResourceType()]));
        //return new ArrayCollection($dao->findAll());
    }


    /**
     * Creates new AclResource entity
     * @param string|null $resource
     * @param string|null $privilege
     * @return AclResource
     */
    public function createResource(string $resource, string $privilege): AclResource
    {
        $aclResource = new AclResource();
        $aclResource->setType($this->getResourceType());
        $aclResource->setResource($resource);
        $aclResource->setPrivilege($privilege);
        return $aclResource;
    }

    protected function getSavedResourceRoleMap(): array
    {
        $savedResources = $this->getSavedResourcesByType();
        $map = [];
        /** @var AclResource $AclResource */
        foreach ($savedResources as $AclResource) {
            /** @var Collection $userRoles */
            $userRoles = $AclResource->getUserRoles();
            /** @var UserRole $UserRole */
            foreach ($userRoles as $UserRole) {
                $map[$AclResource->getResource()][$UserRole->getId()][] = $AclResource->getPrivilege();
            }
        }
        return $map;
    }

    /**
     * Gets resources from config mapped by controller name and action
     * @return array
     */
    protected function getControllerResourceMap(): array
    {
        /** @var AclService $acl */
        $acl = $this->getServiceManager()->get('acl');
        $controllersAssoc = $acl->getControllerMap();
        $controllers = array_keys($controllersAssoc);
        $controllerResourceMap = [];
        foreach ($controllers as $controllerClass) {
            if (!preg_match('/[A-Z]/', $controllerClass)) {
                // filter out names in lower case

                continue;
            }
            if (!class_exists($controllerClass)) {
                //can be one module has been disabled (eg. test)
                continue;
            }

            if ($acl->isExcluded($controllerClass)) {
                continue;
            }

            if (StringUtils::indexOf('console', $controllerClass) > 0) {
                continue;
            }
            try {
                $reflection = new ReflectionClass($controllerClass);
            } catch (ReflectionException $e) {
                continue;
            }
            if ($reflection->isAbstract()) {
                //  can't be
                continue;
            }
            $controllerResourceMap[$controllerClass] = $this->getPrivileges($controllerClass);
        }
        return $controllerResourceMap;
    }

    protected function getPrivileges(string $controllerClass): array
    {
        $reflection = null;
        try {
            $reflection = new ReflectionClass($controllerClass);
        } catch (ReflectionException $e) {
            return [];
        }
        $privileges = [];
        foreach ($reflection->getMethods() as $methodObj) {
            if (!$methodObj->isPublic() || in_array($methodObj->getName(), $this->black_list, true)) {
                continue;
            }
            if (preg_match('/Action$/', $methodObj->getName())) {
                $action = preg_replace('/Action$/', '', $methodObj->getName());
                $privileges[] = strtolower($action);
            }
        }
        return $privileges;
    }


    /**
     * @return string
     */
    abstract public function getResourceType(): string;
}
