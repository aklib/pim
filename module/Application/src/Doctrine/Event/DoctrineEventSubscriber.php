<?php /** @noinspection TransitiveDependenciesUsageInspection */

/**
 * Class DoctrineEventSubscriber
 * @package Application\Doctrine\Event
 *
 * since: 11.10.2020
 * author: alexej@kisselev.de
 */

namespace Application\Doctrine\Event;


use Application\ServiceManager\Interfaces\ServiceManagerAware;
use Company\Entity\Advertiser;
use Company\Entity\Publisher;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\UnitOfWork;
use Product\Entity\Product;
use Psr\Container\ContainerInterface;

class DoctrineEventSubscriber implements EventSubscriber, ServiceManagerAware
{
    /**
     * @var ContainerInterface
     */
    private ContainerInterface $sm;
    private AnnotationReader $reader;
    private bool $doUpdate = false;
    private bool $doDelete = false;

    public function __construct()
    {
        $this->reader = new AnnotationReader();
    }

    /**
     * Subscribed events
     * @return array
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::loadClassMetadata,
            Events::preUpdate,
            Events::postFlush
        ];
    }

    /**
     * Init application custom annotations
     * @param LoadClassMetadataEventArgs $eventArgs
     * @noinspection PhpUnused
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs): void
    {
        $classMetadata = $eventArgs->getClassMetadata();
        foreach ($classMetadata->getReflectionClass()->getProperties() as $property) {
            foreach ($this->reader->getPropertyAnnotations($property) as $annotation) {
                if ($annotation instanceof DoctrineEventSubscriberInterface) {
                    if ($classMetadata->hasAssociation($property->getName())) {
                        $classMetadata->associationMappings[$property->getName()][$annotation->getUniqueKey()] = (array)$annotation;
                    } else {
                        $classMetadata->fieldMappings[$property->getName()][$annotation->getUniqueKey()] = (array)$annotation;
                    }
                }
            }
        }
    }

    /**
     * Used to update a service cache
     * @param LifecycleEventArgs $eventArgs
     */
    public function preUpdate(LifecycleEventArgs $eventArgs): void
    {
        $entity = $eventArgs->getEntity();
        $uow = $eventArgs->getEntityManager()->getUnitOfWork();
        // deleted mark
        $isDeleted = $uow->getEntityState($entity) === UnitOfWork::STATE_REMOVED;
        if ($entity instanceof Product) {
            $this->doUpdate = true;
        } elseif ($entity instanceof Advertiser) {
            $this->doUpdate = true;
        } elseif ($entity instanceof Publisher) {
            $this->doUpdate = true;
        }
    }

    /**
     * @param PostFlushEventArgs $eventArgs
     */
    public function postFlush(PostFlushEventArgs $eventArgs): void
    {
        if ($this->doUpdate) {
            $com1 = "/usr/bin/curl --silent 'https://dsp.roidynamic-platform.com/scrips/redis/1_run_campaign.php' > /dev/null";
            $com2 = "/usr/bin/curl --silent 'https://dsp.roidynamic-platform.com/scrips/redis/2_run_publisher.php' > /dev/null";
            $com3 = "/usr/bin/curl --silent 'https://dsp.roidynamic-platform.com/scrips/redis/4_run_api.php' > /dev/null";

            exec("$com1 2>&1", $o, $return);
            exec("$com2 2>&1", $o, $return);
            exec("$com3 2>&1", $o, $return);
        }
    }

    public function getServiceManager(): ContainerInterface
    {
        return $this->sm;
    }

    public function setServiceManager(ContainerInterface $sm): void
    {
        $this->sm = $sm;
    }
}