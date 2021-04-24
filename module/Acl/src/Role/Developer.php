<?php

/**
 *
 * DeveloperAcl.php
 *
 * @since 20.07.2020
 * @author Alexej Kisselev <alexej.kisselev@gmail.com>
 */

namespace Acl\Role;

class Developer extends AbstractAclRole
{

    public function isAllowed(string $resource = null, $privilege = null): bool
    {
        return true;
    }

    public function isAllowedColumn(string $resource = null, $privilege = null): bool
    {
        return true;
    }
}
