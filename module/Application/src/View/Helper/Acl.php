<?php

/**
 * 
 * Acl.php
 * 
 * @since 19.12.2019
 * @author Alexej Kisselev <alexej.kisselev@gmail.com>
 */

namespace Application\View\Helper;

use Laminas\Permissions\Acl\AclInterface;

class Acl extends AbstractHelperAware implements AclInterface {

    public function __invoke() {
        if ($this->getServiceManager()->has('acl')) {
            return $this->getServiceManager()->get('acl');
        }
        return $this;
    }

    /**
     * AclInterface implementation. Called if acl service not exists
     * @param boolean $resource
     * @return bool
     */
    public function hasResource($resource): bool {
        return true;
    }

    /**
     * AclInterface implementation. Called if acl service not exists
     * @param mixed $role
     * @param mixed $resource
     * @param mixed $privilege
     * @return bool
     */
    public function isAllowed($role = null, $resource = null, $privilege = null): bool {
        return true;
    }

}
