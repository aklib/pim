<?php
    /**
     * Class AbstractModuleOptions
     * since: 30.06.2020
     * author: alexej@kisselev.de
     */

    namespace Application\Options;

    use Application\ServiceManager\Interfaces\ModuleOptionsAware;
    use Application\ServiceManager\Interfaces\ServiceManagerAware;
    use Laminas\Stdlib\AbstractOptions;
    use Psr\Container\ContainerInterface;

    abstract class AbstractModuleOptions extends AbstractOptions implements ModuleOptionsAware, ServiceManagerAware
    {
        private ContainerInterface $sm;

        public function __construct($options = null)
        {
            parent::__construct($options);
            $this->__strictMode__ = false;
        }

        /**
         * ServiceManagerAware implementation
         * @return ContainerInterface
         */
        public function getServiceManager(): ContainerInterface
        {
            return $this->sm;
        }

        /**
         * ServiceManagerAware implementation
         * @param ContainerInterface $sm
         */
        public function setServiceManager(ContainerInterface $sm): void
        {
            $this->sm = $sm;
        }
    }