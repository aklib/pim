<?php /** @noinspection TransitiveDependenciesUsageInspection */

/** @noinspection PhpUnused */


namespace Report;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\Mapping\Driver\MappingDriverChain;
use Elastic\Doctrine\ElasticEventSubscriber;
use Laminas\Mvc\MvcEvent;

class Module
{
    /**
     * This method returns the path to module.config.php file.
     */
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    /**
     * This method is called once the MVC bootstrapping is complete and allows
     * to register event listeners.
     * @param MvcEvent $event
     */
    public function onBootstrap(MvcEvent $event): void
    {
        /** @noinspection UnusedFunctionResultInspection */
        $event->getApplication()->getEventManager()->attach(MvcEvent::EVENT_ROUTE, [$this, 'registerEntities']);
        $em = $event->getApplication()->getServiceManager()->get(EntityManager::class);
        // add subscriber to handle elastic annotations
        //$em->getEventManager()->addEventSubscriber(new ElasticEventSubscriber());
    }

    /**
     * Adds elasticsearch entities into ORM driver to use the entity manager
     * @param MvcEvent $event
     */
    public function registerEntities(MvcEvent $event): void
    {
        $routeMatch = $event->getRouteMatch();
        if ($routeMatch === null) {
            return;
        }
        $name = $routeMatch->getMatchedRouteName();
        /** @var ModuleOptions $moduleOptions */
        $moduleOptions = $event->getApplication()->getServiceManager()->get(ModuleOptions::class);
        if (in_array($name, $moduleOptions->getActivateOnRoutes(), true)) {
            try {
                /** @var EntityManager $em */
                $em = $event->getApplication()->getServiceManager()->get(EntityManager::class);
                // add subscriber to handle elastic annotations
                //$em->getEventManager()->addEventSubscriber( new ElasticEventSubscriber());
                // add driver
                $driverImpl = $em->getConfiguration()->newDefaultAnnotationDriver(__DIR__ . '/Entity', false);
                /** @var MappingDriverChain $driver */
                $driver = $em->getConfiguration()->getMetadataDriverImpl();
                $driver->addDriver($driverImpl, 'Report\Entity');

            } catch (ORMException $e) {
                //throw $e;
            }
        }


    }


}
