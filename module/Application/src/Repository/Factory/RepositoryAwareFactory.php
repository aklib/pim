<?php
    /**
     * Class RepositoryAwareFactory
     * @package Application\Repository\Factory
     *
     * since: 31.07.2020
     * author: alexej@kisselev.de
     */

    namespace Application\Repository\Factory;


    use Application\ServiceManager\InvokableAwareFactory;
    use Doctrine\ORM\EntityManagerInterface;
    use Doctrine\ORM\Mapping as ORM;
    use Doctrine\ORM\Repository\RepositoryFactory;
    use Doctrine\Persistence\ObjectRepository;
    use function spl_object_hash;

    /**
     * This factory is used to create default repository objects for entities at runtime.
     */
    final class RepositoryAwareFactory implements RepositoryFactory
    {
        /**
         * The list of EntityRepository instances.
         * @var ObjectRepository[]
         * @ORM\Column(type="string")
         */
        private array $repositoryList = [];

        /**
         * {@inheritdoc}
         */
        public function getRepository(EntityManagerInterface $entityManager, $entityName): ObjectRepository
        {
            $repositoryHash = $entityManager->getClassMetadata($entityName)->getName() . spl_object_hash($entityManager);

            if (isset($this->repositoryList[$repositoryHash])) {
                return $this->repositoryList[$repositoryHash];
            }
            $repository = $this->repositoryList[$repositoryHash] = $this->createRepository($entityManager, $entityName);
            InvokableAwareFactory::aware($repository);
            if (method_exists($repository, 'init')) {
                $repository->init();
            }
            return $repository;
        }

        /**
         * Create a new repository instance for an entity class.
         *
         * @param EntityManagerInterface $entityManager The EntityManager instance.
         * @param string                               $entityName    The name of the entity.
         *
         * @return ObjectRepository
         */
        private function createRepository(EntityManagerInterface $entityManager, $entityName): ObjectRepository
        {
            $metadata            = $entityManager->getClassMetadata($entityName);
            $repositoryClassName = $metadata->customRepositoryClassName
                ?: $entityManager->getConfiguration()->getDefaultRepositoryClassName();

            return new $repositoryClassName($entityManager, $metadata);
        }
    }