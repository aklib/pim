<?php

    namespace Application\Doctrine\Annotation;

    use Application\Doctrine\Event\DoctrineEventSubscriberInterface;
    use Doctrine\ORM\Mapping\Annotation;

    /**
     * @Annotation
     */
    class Element implements Annotation, DoctrineEventSubscriberInterface
    {
        /**
         * Form element class full name
         * @var string
         */
        public string $type;
        /**
         * Attribute tab uniqueKey
         * @var string
         */
        public string $tab;

        public function __construct(array $properties)
        {
            $this->type = $properties['type'] ?? '';
            $this->tab = $properties['tab'] ?? 'general';
        }

        public function getType(): string
        {
            return $this->type;
        }

        public function getUniqueKey(): string
        {
            return 'element';
        }
    }
