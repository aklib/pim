<?php
    /**
     * Class AttributeEntityHydratorFactory
     * @package Attribute\Hydrator
     *
     * since: 19.08.2020
     * author: alexej@kisselev.de
     */

    namespace Attribute\Hydrator;


    use Application\ServiceManager\InvokableAwareFactory;
    use Doctrine\ORM\EntityManager;
    use Psr\Container\ContainerInterface;
    use Laminas\ServiceManager\Factory\FactoryInterface;

    class CustomEntityHydratorFactory implements FactoryInterface
    {

        public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
        {
            $hydrator = new $requestedName($container->get(EntityManager::class));
            InvokableAwareFactory::aware($hydrator);
            return $hydrator;
        }
    }