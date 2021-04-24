<?php
/**
 * Class Column
 * @package Application\Doctrine\Annotation
 *
 * since: 16.02.2021
 * author: alexej@kisselev.de
 */

namespace Application\Doctrine\Annotation;


use Application\Doctrine\Event\DoctrineEventSubscriberInterface;
use Doctrine\ORM\Mapping\Annotation;

/**
 * @Annotation
 */
class Column implements Annotation, DoctrineEventSubscriberInterface
{
    /**
     * Form element class full name
     * @var string
     */
    public string $label;
    /**
     * Attribute tab uniqueKey
     * @var string
     */
    public string $sortOrder;

    /**
     * Attribute tab uniqueKey
     * @var bool
     */
    public bool $hidden;

    /**
     * Attribute tab uniqueKey
     * @var string
     */
    public string $class;

    public function __construct(array $properties)
    {
        $this->label = $properties['label'] ?? '';
        $this->sortOrder = (int)($properties['sortOrder'] ?? 0);
        $this->class = $properties['class'] ?? '';
        if(array_key_exists('hidden', $properties)){
            $this->hidden = (bool)$properties['hidden'];
        }

    }

    public function getUniqueKey(): string
    {
        return 'column';
    }
}