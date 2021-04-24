<?php
    /**
     * Class RepositoryAwareFactoryFactory
     * @package Application\Repository\Factory
     *
     * since: 31.07.2020
     * author: alexej@kisselev.de
     */

    namespace Application\Repository\Factory;

    use Psr\Container\ContainerInterface;
    use Laminas\ServiceManager\Factory\FactoryInterface;

    class RepositoryAwareFactoryFactory implements FactoryInterface
    {

        public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
        {
            return new RepositoryAwareFactory($container);
        }
    }