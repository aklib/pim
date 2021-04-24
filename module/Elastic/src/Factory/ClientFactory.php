<?php
    /**
     * Class ClientFactory
     * @package Elastic\Factory
     *
     * since: 23.09.2020
     * author: alexej@kisselev.de
     */

    namespace Elastic\Factory;


    use Application\ServiceManager\InvokableAwareFactory;
    use Elastic\ModuleOptions;
    use Laminas\ServiceManager\Factory\FactoryInterface;
    use Psr\Container\ContainerInterface;

    class ClientFactory implements FactoryInterface
    {
        /**
         * This method creates the Elasticsearch client instance
         * @param ContainerInterface $container
         * @param $requestedName
         * @param array|null $options
         * @return Client
         */
        public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
        {
            /** @var ModuleOptions $moduleOptions */
            $moduleOptions = $container->get(ModuleOptions::class);
            // Create the service and inject dependencies into its constructor.
            $client = new Client($moduleOptions->getConnections());
            InvokableAwareFactory::aware($client);
            return $client;
        }
    }

