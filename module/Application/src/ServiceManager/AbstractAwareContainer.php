<?php
/**
 * Class AbstractContainerAware
 * @package Application\ServiceManager * since: 29.07.2020
 * author: alexej@kisselev.de
 */

namespace Application\ServiceManager;


use Application\ModuleOptions;
use Application\ServiceManager\Interfaces\AuthenticationAware;
use Application\ServiceManager\Interfaces\EntityManagerAware;
use Application\ServiceManager\Interfaces\ServiceManagerAware;
use Application\View\Manager\EmptyAdapter;
use Application\View\Manager\Interfaces\EntityAwareInterface;
use Application\View\Manager\Interfaces\ViewAdapterInterface;
use Doctrine\ORM\EntityManager;
use Laminas\Http\Request;
use Laminas\Router\RouteMatch;
use Laminas\Stdlib\AbstractOptions;
use Psr\Container\ContainerInterface;
use User\Entity\User;

abstract class AbstractAwareContainer extends AbstractOptions implements ServiceManagerAware, EntityManagerAware, AuthenticationAware, EntityAwareInterface
{
    private array $urlParams = [];
    private string $entityName = '';
    private ?ViewAdapterInterface $adapter = null;
    /**
     * @var ContainerInterface
     */
    private ContainerInterface $sm;

    /**
     * @var EntityManager
     */
    private EntityManager $em;
    /**
     * @var User
     */
    private User $user;

    /**
     * Interface ServiceManagerAware implementation
     * @return ContainerInterface
     */
    public function getServiceManager(): ContainerInterface
    {
        return $this->sm;
    }

    /**
     * Interface ServiceManagerAware implementation
     * @param ContainerInterface $sm
     * @return void
     */
    public function setServiceManager(ContainerInterface $sm): void
    {
        $this->sm = $sm;
    }

    /**
     * Interface EntityManagerAware implementation
     * @return EntityManager
     */
    public function getEntityManager(): EntityManager
    {
        return $this->em;
    }

    /**
     * Interface EntityManagerAware implementation
     * @param EntityManager $em
     */
    public function setEntityManager(EntityManager $em): void
    {
        $this->em = $em;
    }

    /**
     * Interface AuthenticationAware implementation
     * @return User
     */
    public function getCurrentUser(): User
    {
        return $this->user;
    }

    /**
     * Interface AuthenticationAware implementation
     * @param User $user
     */
    public function setCurrentUser(User $user): void
    {
        $this->user = $user;
    }

    public function getUrlParams(): array
    {
        if (empty($this->urlParams)) {
            /** @var Request $request */
            $request = $this->getServiceManager()->get('Request');
            $this->urlParams = array_replace_recursive(
                $this->getRouteMatch()->getParams(),
                $request->getQuery()->toArray(),
                $request->getPost()->toArray()
            );
        }
        return $this->urlParams;
    }

    public function setUrlParams(array $urlParams): void
    {
        $this->urlParams = $urlParams;
    }


//=============================== help methods =====================================
    protected function getRequest(): Request
    {
        return $this->getServiceManager()->get('Request');
    }

    public function getEntityName(): string
    {
        return $this->entityName;
    }

    public function setEntityName(string $entityName): void
    {
        $this->entityName = $entityName;
    }

    public function getAdapterName(string $controllerName = null)
    {
        if ($controllerName === null) {
            $controllerName = $this->getControllerName();
        }
        return str_replace('Controller', 'Adapter', $controllerName);
    }

    protected function getAdapter(): ViewAdapterInterface
    {
        if ($this->adapter instanceof ViewAdapterInterface) {
            return $this->adapter;
        }
        $adapterName = $this->getAdapterName();
        if ($this->getServiceManager()->has($adapterName)) {
            $this->adapter = $this->getServiceManager()->get($adapterName);
        } else {
            $this->adapter = $this->getServiceManager()->get(EmptyAdapter::class);
        }
        return $this->adapter;
    }

    public function setAdapter(ViewAdapterInterface $adapter): void
    {
        $this->adapter = $adapter;
    }

    protected function getRouteMatch(): RouteMatch
    {
        return $this->getServiceManager()->get('Application')->getMvcEvent()->getRouteMatch();
    }

    /**
     * Gets a action name like 'list', 'edit' etc.
     * @return string
     */
    protected function getActionName(): string
    {
        return $this->getRouteMatch()->getParam('action');
    }

    private function resolveControllerName(string $name): string
    {
        $controllerConfig = $this->getServiceManager()->get('Config')['controllers'];
        if (!empty($controllerConfig['aliases'][$name])) {
            return $controllerConfig['aliases'][$name];
        }
        return $name;
    }

    public function getControllerName(): string
    {
        return $this->resolveControllerName($this->getControllerAlias());
    }

    public function getControllerAlias(): string
    {
        return $this->getRouteMatch()->getParam('controller');
    }

    protected function displayExceptions(): bool
    {
        /** @var ModuleOptions $moduleOptions */
        return $this->getServiceManager()->get(ModuleOptions::class)->isDisplayExceptions();
    }
}