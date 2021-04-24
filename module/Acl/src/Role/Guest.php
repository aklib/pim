<?php

/**
 *
 * GuestAcl.php
 *
 * @since 20.07.2020
 * @author Alexej Kisselev <alexej.kisselev@gmail.com>
 */

namespace Acl\Role;


class Guest extends AbstractAclRole
{

    public function isAllowed(string $resource = null, $privilege = null): bool
    {
        return false;
    }

    public function isAllowedColumn(string $resource = null, $privilege = null): bool
    {
        return false;
    }
}
