<?php
    /**
     * Class AbstractElasticsearchDao
     * @package Elastic\Repository
     *
     * since: 23.09.2020
     * author: alexej@kisselev.de
     */

    namespace Elastic\Repository;


    use Application\Repository\AbstractDoctrineDao;
    use Application\ServiceManager\Interfaces\ViewModelAware;
    use Application\View\Manager\ViewManager;
    use Elastic\ODM\Query\QueryBuilder;
    use Elastica\ResultSet;
    use RuntimeException;
    use User\Entity\User;

    abstract class AbstractElasticsearchDao extends AbstractDoctrineDao implements ViewModelAware
    {
        /**
         * Gets an item details
         * @param int $id
         * @return object|null
         * @throws RuntimeException
         */
        public function getDetails(int $id): ?object
        {
            throw new RuntimeException('Not implemented yet');
        }

        /**
         * Main method for create listings. See AbstractModuleController::listAction()
         * @param array $config
         * @param array $options
         * @return object
         */
        public function getList(array $config = [], array $options = []): object
        {
            $qb = $this->getQueryBuilder();

            if(empty($config['daterange']) && !empty($options['daterange'])){
                $config['daterange'] = $options['daterange'];
            }
            if(empty($config['timezone']) && !empty($options['timezone'])){
                $config['timezone'] = $options['timezone'];
            }

            $qb
                ->setTimezone($config['timezone'] ?? null)
                ->setDateRange($config['daterange'] ?? null);

            /*$e = new Term(['campaign.bid_won'=> true]);
            $qb->getQueryBool()->addMust($e);*/

            $this->addUserRestrictions($qb);

            /** @var ViewManager $viewManager */
            $viewManager = $this->getServiceManager()->get(ViewManager::class);
            $columns = $viewManager->getColumns();

            if (!empty($columns) && is_array($columns)) {
                $qb->applyColumns($columns);
            } else {
                return $qb->getErrorResult('');
            }
            $this->postCreateQuery($qb, $config, $options);
            //dump(json_encode($qb->getQuery()->toArray()));echo '<br>';die;
            return $this->createResult($qb->getResult());
        }

        /**
         * Creates a query builder instance
         * @return QueryBuilder
         */
        protected function getQueryBuilder(): QueryBuilder
        {
            /** @var QueryBuilder $qb */
            return $this->getServiceManager()->get(QueryBuilder::class);
        }

        /**
         * Post hook to modify elasticsearch Query
         * @param QueryBuilder $qb
         * @param array $params
         * @param array $options
         */
        protected function postCreateQuery(QueryBuilder $qb, array $params, array $options): void
        {
        }

        /**
         * @param QueryBuilder $qb
         * @param User|null $user
         */
        protected function addUserRestrictions(QueryBuilder $qb, User $user = null): void
        {
        }

        /**
         * Post hook to manipulate a result instance
         * @param ResultSet $resultSet
         * @return object
         */
        protected function createResult(ResultSet $resultSet): object
        {
            //dump($resultSet->getResults());die;
            return $resultSet;
        }
    }