<?php

namespace Elastic\ODM\Annotation;

use Doctrine\ORM\Mapping\Annotation;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class Aggregation implements Annotation
{
    public string $type;

    public function __construct(array $properties)
    {
        $this->type = $properties['type'] ?? '';
    }

    public function getType(): string
    {
        return $this->type;
    }




}