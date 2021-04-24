<?php

    /**
     * AbstractHelperAware.php
     *
     * @since 19.12.2019
     * @author Alexej Kisselev <alexej.kisselev@gmail.com>
     */

    namespace Application\View\Helper;

    use Application\ServiceManager\Interfaces\ServiceManagerAware;
    use Psr\Container\ContainerInterface;
    use Laminas\View\Helper\AbstractHelper;

    abstract class AbstractHelperAware extends AbstractHelper implements ServiceManagerAware
    {

        /**
         * @var ContainerInterface instance
         */
        protected $sm;

        /**
         * ServiceManagerAware implementation
         * @return ContainerInterface service manager
         */
        public function getServiceManager(): ContainerInterface
        {
            return $this->sm;
        }

        /**
         * ServiceManagerAware implementation
         * @param ContainerInterface $sm
         * @return void
         */
        public function setServiceManager(ContainerInterface $sm): void
        {
            $this->sm = $sm;
        }

    }
