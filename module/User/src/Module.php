<?php /** @noinspection TransitiveDependenciesUsageInspection */


/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace User;

use Acl\Service\AclService;
use Application\View\Helper\History;
use Laminas\Authentication\AuthenticationService;
use Laminas\Mvc\MvcEvent;
use Laminas\View\Model\ViewModel;
use Psr\Container\ContainerInterface;
use User\Controller\AuthController;
use User\Service\AuthManager;

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
        // Get event manager.
        $eventManager = $event->getApplication()->getEventManager();

        // Register the event listener method.
        /** @noinspection UnusedFunctionResultInspection */
        $eventManager->attach(MvcEvent::EVENT_DISPATCH, [$this, 'onDispatch'], 100);
    }

    /**
     * Event listener method for the 'Dispatch' event. We listen to the Dispatch
     * event to call the access filter. The access filter allows to determine if
     * the current visitor is allowed to see the page or not. If he/she
     * is not authorized and is not allowed to see the page, we redirect the user
     * to the login page.
     * @param MvcEvent $event
     * @return ViewModel|void
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function onDispatch(MvcEvent $event)
    {
        /** @var ContainerInterface $sm */
        $sm = $event->getApplication()->getServiceManager();
        /** @var ModuleOptions $moduleOptions */
        $moduleOptions = $sm->get(ModuleOptions::class);
        if (!$moduleOptions->isDispatchOn()) {
            $event->getViewModel()->setTemplate('layout/default');
            return;
        }

        $routeMatch = $event->getRouteMatch();
        if ($routeMatch === null) {
            return;
        }
        // Get controller and action to which the HTTP request was dispatched.
        if ($sm->has('acl')) {
            /** @var AclService $acl */
            $acl = $sm->get('acl');

            $controllerName = $routeMatch->getParam('controller');
            if ($acl->isExcluded($controllerName)) {
                return;
            }
        }

        /** @var AuthManager $authManager */
        $authManager = $event->getApplication()->getServiceManager()->get(AuthManager::class);
        /** @var AuthenticationService $authenticationService */
        $authenticationService = $sm->get(AuthenticationService::class);
        /** @var History $history */
        $history = $sm->get('ViewHelperManager')->get('history');

        if (!$authenticationService->hasIdentity()) {
            // redirect the user to that URL after successful login.
            $history->save();
            return $authManager->forwardToLogin($event);
        }
        $actionName = $routeMatch->getParam('action');
        $controllerName = $routeMatch->getParam('controller');
        if ($sm->has('acl')) {
            /** @var AclService $acl */
            $acl = $sm->get('acl');
            $deny = !$acl->isAllowed($authenticationService->getIdentity(), $controllerName, $actionName);
        } else {
            $deny = $controllerName !== AuthController::class && !$authenticationService->hasIdentity();
        }
        if ($deny) {
            // Remember the URL of the page the user tried to access. We will
            // redirect the user to that URL after successful login.
            $history->save();
            // Forward the user to the "Login" page.
            return $authManager->forwardToLogin($event, 'Access denied');
        }
        $event->getViewModel()->setTemplate('layout/default');
    }
}
