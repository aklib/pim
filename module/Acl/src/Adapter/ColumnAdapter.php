<?php
/**
 * Class ColumnAdapter
 * @package Acl\Adapter
 *
 * since: 31.07.2020
 * author: alexej@kisselev.de
 */

namespace Acl\Adapter;


use Acl\Entity\AclResource;
use Application\View\Manager\EmptyAdapter;

class ColumnAdapter extends EmptyAdapter
{
    public function getEntityName(): string
    {
        return AclResource::class;
    }

    public function isColumnVisible(string $columnName): bool
    {
        return false;
    }

}