<?php

namespace Attribute\Repository;

class AttributeValueSelectDao extends AbstractAttributeDao
{
    protected function getAlias(): string
    {
        return 'valueSelects';
    }
}
