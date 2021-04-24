<?php
/** @noinspection PhpUnused */

/**
 * @see       https://github.com/laminas/laminas-mvc-skeleton for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-skeleton/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-skeleton/blob/master/LICENSE.md New BSD License
 */

declare(strict_types = 1);

namespace Application;

use Application\Doctrine\Event\DoctrineEventSubscriber;
use Application\ServiceManager\InvokableAwareFactory;
use Application\Utils\RuntimeUtils;
use Application\View\Helper\Params;
use Application\View\Helper\ServiceManager;
use Application\View\Helper\Translate;
use Application\View\Helper\Urlx;
use Doctrine\ORM\EntityManager;
use Laminas\EventManager\EventManager;
use Laminas\I18n\Translator\TranslatorServiceFactory;
use Laminas\Mvc\Application;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\ModuleRouteListener;
use Laminas\Mvc\MvcEvent;
use Psr\Container\ContainerInterface;

class Module
{
    public function getConfig(): array
    {
        return include __DIR__ . '/../config/module.config.php';
    }


    public function onBootstrap(MvcEvent $e): void
    {
        $application = $e->getApplication();
        /* @var $eventManager EventManager */
        $eventManager = $application->getEventManager();
        /*
         * This listener determines if the module namespace should be prepended to the controller name.
         * This is the case if the route match contains a parameter key matching the MODULE_NAMESPACE constant.
         */
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        if (!RuntimeUtils::cli()) {
            $em = $application->getServiceManager()->get(EntityManager::class);
            $doctrineEventSubscriber = $application->getServiceManager()->get(DoctrineEventSubscriber::class);
            // add subscriber to handle elastic annotations
            $em->getEventManager()->addEventSubscriber($doctrineEventSubscriber);
        }

    }

    public function getServiceConfig(): array
    {
        return [
            'aliases'    => [
                'dynamic' => 'doctrine.cache.dynamic'
            ],
            'invokables' => [
                ServiceManager::class => InvokableAwareFactory::class,
            ],
            'factories'  => [
                'translator' => TranslatorServiceFactory::class,
            ]
        ];
    }

    public function getViewHelperConfig(): array
    {
        return [
            'factories' => [
                // the array key here is the name you will call the view helper by in your view scripts

                'urlx'      => static function (ContainerInterface $sm) {
                    return new Urlx($sm->get('Application')->getRequest(), $sm->get('Application')->getMvcEvent());
                },
                'params'    => static function (ContainerInterface $sm) {
                    return new Params($sm->get('Application')->getRequest(), $sm->get('Application')->getMvcEvent());
                },
                'translate' => static function (ContainerInterface $sm) {
                    $app = $sm->get('Application');
                    $routeMatch = $app->getMvcEvent()->getRouteMatch();
                    $locale = 'en';
                    if (!empty($routeMatch)) {
                        $language = strtolower($routeMatch->getParam('language'));
                        if (!empty($language)) {
                            $conf = $sm->get('Config');
                            $available = $conf['translator']['available'] ?: [];
                            foreach ($available as $loc => $name) {
                                if (stripos($loc, $language) === 0) {
                                    $locale = $loc;
                                }
                            }
                        }
                    }
                    $sm->get('MvcTranslator')->setLocale($locale);
                    $helper = new Translate();
                    $helper->setTranslatorTextDomain($locale);
                    return $helper;
                },
            ],
        ];
    }

    public function getControllerConfig(): array
    {
        return [
            'initializers' => [
                'onFinish' => static function (ContainerInterface $sm, AbstractActionController $instance) {
                    //  add ohFinish to controllers. Used for history plugin to save valid requests only
                    $eventManager = $sm->get('Application')->getEventManager();
                    $sharedEventManager = $eventManager->getSharedManager();
                    // Register the event listener method.
                    $sharedEventManager->attach(Application::class, MvcEvent::EVENT_FINISH, [$instance, 'onFinish'], 1);
                },
            ],
        ];
    }
}
