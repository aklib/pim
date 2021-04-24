<?php /** @noinspection TransitiveDependenciesUsageInspection */

    /**
     * Class ElasticaEventSubscriber
     * @package Elastic\Doctrine
     *
     * since: 23.09.2020
     * author: alexej@kisselev.de
     */

    namespace Elastic\Doctrine;

    use Doctrine\Common\Annotations\AnnotationReader;
    use Doctrine\Common\EventSubscriber;
    use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
    use Doctrine\ORM\Events;

    class ElasticEventSubscriber implements EventSubscriber
    {
        private AnnotationReader $annotationReader;

        public function __construct()
        {
            $this->annotationReader = new AnnotationReader();
        }


        public function getSubscribedEvents(): array
        {
            return [
                Events::loadClassMetadata
            ];
        }

        /** @noinspection PhpUnused */
        public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs): void
        {
            $classMetadata = $eventArgs->getClassMetadata();
            if(!str_starts_with($classMetadata->getName(), 'Report')){
                return;
            }
            $properties = $classMetadata->getReflectionClass()->getProperties();

            $mappings = [];
            foreach ($properties as $property) {
                //$refProp = $classMetadata->getReflectionClass()->getProperty($field);
                foreach ($this->annotationReader->getPropertyAnnotations($property) as $annotation) {

                    if (str_starts_with(get_class($annotation), 'Elastic')) {
                        $className = explode('\\', get_class($annotation));
                        $className = strtolower(array_pop($className));
                        $mappings[$property->getName()][$className] = (array)$annotation;
                        $classMetadata->fieldNames[$property->getName()] = $property->getName();
                    }
                }
            }
            if (!empty($mappings)) {
                $classMetadata->fieldMappings = array_replace_recursive($classMetadata->fieldMappings, $mappings);
            }
        }
    }