<?php

namespace User\Service;

use Application\ServiceManager\Interfaces\EntityManagerAware;
use Application\ServiceManager\Interfaces\ServiceManagerAware;
use Doctrine\ORM\EntityManager;
use Exception;
use Laminas\Authentication\AuthenticationService;
use Laminas\Authentication\Result;
use Laminas\Http\Request;
use Laminas\Http\Response;
use Laminas\Mvc\MvcEvent;
use Laminas\Session\SessionManager;
use Laminas\View\Model\ViewModel;
use Psr\Container\ContainerInterface;
use RuntimeException;
use User\Controller\AuthController;

/**
 * The AuthManager service is responsible for user's login/logout and simple access
 * filtering. The access filtering feature checks whether the current visitor
 * is allowed to see the given page or not.
 */
class AuthManager implements EntityManagerAware, ServiceManagerAware
{
    /**
     * Entity manager.
     * @var EntityManager
     */
    private EntityManager $em;

    /**
     * Entity manager.
     * @var ContainerInterface
     */
    private ContainerInterface $sm;

    /**
     * Performs a login attempt. If $rememberMe argument is true, it forces the session
     * to last for one month (otherwise the session expires on one hour).
     * @param string $email
     * @param string $password
     * @param bool $rememberMe
     * @return Result
     * @throws Exception
     */
    public function login(string $email, string $password, bool $rememberMe): Result
    {
        /** @var AuthenticationService $authService */
        $authService = $this->getServiceManager()->get(AuthenticationService::class);
        // Check if user has already logged in. If so, do not allow to log in
        // twice.
        if ($authService->hasIdentity()) {
            $authService->clearIdentity();
            //throw new RuntimeException('Already logged in');
        }

        // Authenticate with login/password.

        /** @var AuthAdapter $authAdapter */
        $authAdapter = $authService->getAdapter();
        $authAdapter->setEmail($email);
        $authAdapter->setPassword($password);
        $result = $authService->authenticate();
        if ($result->getCode() !== Result::SUCCESS) {
            return $result;
        }
        // If user wants to "remember him", we will make session to expire in
        // one month. By default session expires in 1 hour (as specified in our
        // config/global.php file).

        if ($rememberMe) {
            // Session cookie will expire in 1 month (30 days).
            $this->getServiceManager()->get(SessionManager::class)->rememberMe(60 * 60 * 24 * 30);
        }

        return $result;
    }


    /**
     * Performs user logout.
     */
    public function logout()
    {
        /** @var AuthenticationService $authService */
        $authService = $this->getServiceManager()->get(AuthenticationService::class);
        if (!$authService->hasIdentity()) {
            return;
        }
        // Allow to log out only when user is logged in.
        if ($authService->getIdentity() === null) {
            throw new RuntimeException('The user is not logged in');
        }

        // Remove identity from session.

        $authService->clearIdentity();
    }

    public function forwardToLogin(MvcEvent $e, string $message = null): ViewModel
    {
        /** @var Request $request */
        $request = $e->getRequest();
        if ($request->isXmlHttpRequest()) {
            die('<script>window.location.reload();</script>');
        }
        $matches = $e->getRouteMatch();
        $matches->setParam('controller', AuthController::class);
        $matches->setParam('action', 'login');

        /** @var Response $response */
        $response = $e->getResponse();
        $response->setStatusCode(Response::STATUS_CODE_403);

        $viewModel = $e->getViewModel();
        $viewModel->setTemplate('layout/login');
        if (!empty($message)) {
            $viewModel->setVariable('message', $message);
        }
        return $viewModel;
    }

    public function getEntityManager(): EntityManager
    {
        return $this->em;
    }

    public function setEntityManager(EntityManager $em): void
    {
        $this->em = $em;
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