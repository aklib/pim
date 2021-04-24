<?php
/**
 * Class AttributeOptionDao
 * @package Attribute\Repository
 *
 * since: 15.10.2020
 * author: alexej@kisselev.de
 */

namespace Attribute\Repository;


class AttributeOptionDao extends AbstractAttributeDao
{
    protected function getAlias(): string
    {
        return 'valueSelects';
    }

}