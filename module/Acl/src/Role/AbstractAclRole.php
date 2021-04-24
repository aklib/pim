<?php

/**
 *
 * AbstractAcl.php
 *
 * @since 20.07.2020
 * @author Alexej Kisselev <alexej.kisselev@gmail.com>
 */

namespace Acl\Role;


use Acl\Entity\AclResource;
use Doctrine\Common\Collections\Collection;

abstract class AbstractAclRole
{
    protected string $role;
    private array $resourceMap = [];
    private array $columnMap = [];
    private array $options = [];

    /**
     * @param Collection $resources
     */
    public function setResources(Collection $resources): void
    {
        /** @var AclResource $resource */
        foreach ($resources as $resource) {
            //dump($resource->getResource(),$resource->getPrivilege() );
            if ($resource->getType() === AclResource::RESOURCE_TYPE_MVC) {
                $this->resourceMap[strtolower($resource->getResource())][] = strtolower($resource->getPrivilege());
            } elseif ($resource->getType() === AclResource::RESOURCE_TYPE_COLUMN) {
                $this->columnMap[strtolower($resource->getResource())][] = strtolower($resource->getPrivilege());
            } else {
                $this->options[strtolower($resource->getResource())][] = strtolower($resource->getPrivilege());
            }
        }
    }

    /**
     * AclInterface implementation
     *
     * @param string|null $resource
     * @param null $privilege
     * @return bool
     */
    public function isAllowed(string $resource = null, $privilege = null): bool
    {
        if (!$this->isValidParameter($resource, $privilege)) {
            return false;
        }
        if (!$this->hasResource($resource, $privilege)) {
            return false;
        }
        if ($privilege === null) {
            return true;
        }
        return in_array($privilege, $this->resourceMap[$resource], true);
    }

    public function isAllowedColumn(string $resource = null, $privilege = null): bool
    {
        if (!$this->isValidParameter($resource, $privilege)) {
            return false;
        }
        if (!$this->hasColumnResource($resource)) {
            return false;
        }
        return in_array($privilege, $this->columnMap[$resource], true);
    }

    private function isValidParameter(string $resource = null, string $privilege = null): bool
    {
        return $resource !== null;
    }

    /**
     * AclInterface implementation
     * @param string|null $resource
     * @param string|null $privilege
     * @return bool
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    public function hasResource(string $resource = null, string $privilege = null)
    {
        if ($resource === null) {
            return false;
        }
        if (!array_key_exists($resource, $this->resourceMap)) {
            return false;
        }
        if ($privilege === null) {
            return true;
        }
        return in_array($privilege, $this->resourceMap[$resource], true);
    }

    public function hasColumnResource($resource): bool
    {
        return !empty($this->columnMap[$resource]);
    }
}
