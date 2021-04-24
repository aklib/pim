<?php
    /**
     * Class ORMPaginator
     * @package Application\Doctrine
     *
     * since: 25.08.2020
     * author: alexej@kisselev.de
     */

    namespace Application\Doctrine;

    use Doctrine\ORM\QueryBuilder;
    use Doctrine\ORM\Tools\Pagination\Paginator;

    class ORMPaginator extends Paginator
    {
        private QueryBuilder $queryBuilder;

        /**
         * @return QueryBuilder
         */
        public function getQueryBuilder(): QueryBuilder
        {
            return $this->queryBuilder;
        }

        /**
         * @param QueryBuilder $queryBuilder
         */
        public function setQueryBuilder(QueryBuilder $queryBuilder): void
        {
            $this->queryBuilder = $queryBuilder;
        }

        public function count()
        {
            $qb = clone $this->getQueryBuilder();

            $join = $qb->getDQLPart('join');
            $from = $qb->getDQLPart('from');
            $qb->resetDQLPart('join');
            $qb->resetDQLPart('orderBy');


            $alias = $qb->getRootAliases()[0];
            $qb->select("count($alias.id)");

            $count = $qb->getQuery()->getSingleScalarResult();
            return (int)$count;
        }
    }