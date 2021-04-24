<?php

    /**
     *
     * AbstractModelDAO.php
     *
     * @since  01.07.2017
     * @author Alexej Kisselev <alexej.kisselev@gmail.com>
     */

    namespace Application\Repository;

    use Application\ServiceManager\Interfaces\ViewModelAware;
    use Application\Utils\DoctrineUtils;
    use Doctrine\ORM\NonUniqueResultException;
    use Doctrine\ORM\Query\AST\Join;
    use Doctrine\ORM\QueryBuilder;
    use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
    use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
    use Laminas\Paginator\Paginator as ZendPaginator;
    use RuntimeException;
    use User\Entity\User;


    abstract class AbstractModelDao extends AbstractDoctrineDao implements ViewModelAware
    {

        /**
         * ViewModelAware implementation
         * @param array $config
         * @param array $options
         * @return ZendPaginator | mixed
         */
        public function getList(array $config = [], array $options = [])
        {
            $tableFields = $this->getClassMetadata()->getFieldNames();
            $associationFields = $this->getClassMetadata()->getAssociationNames();
            $fields = array_merge($tableFields, $associationFields);

            $qb = $this->getQueryBuilder();

            //========= ACL =========
            /** @var User $user */
            $user = array_key_exists('user', $options) && $options['user'] instanceof User ? $options['user'] : $this->getCurrentUser();
            $this->addUserRestriction($qb, $user);

            //========= FILTERS =========
            if (!empty($config)) {
                foreach ($config as $key => $value) {
                    if (!is_numeric($value) && empty($value)) {
                        continue;
                    }
                    $fieldName = $this->getFieldName($key, $fields);
                    if (empty($fieldName)) {
                        continue;
                    }
                    if ($this->preCreateFilter($qb, $value, $fieldName, !in_array($fieldName, $tableFields, true))) {
                        DoctrineUtils::addFilter($qb, $value, $fieldName);
                    }
                }
            }

            //========= ORDER BY =========
            $useOutputWalkers = false;
            if (!empty($config['sortBy'])) {
                $fields = explode(URL_VALUE_SEPARATOR, $config['sortBy']);
                $fieldName = $this->getFieldName($fields[0], $fields);
                if (!empty($fieldName)) {
                    $isAssociation = !in_array($fieldName, $tableFields, true);

                    $allow = $this->preCreateSort($qb, $config['sortBy'], $isAssociation);
                    if ($allow) {
                        DoctrineUtils::addSort($qb, $config['sortBy']);
                    } else {
                        $useOutputWalkers = true;
                    }
                } else {
                    $useOutputWalkers = true;
                }
            }
            $this->postCreateQueryBuilder($qb, $config, $options);

            //========= CREATE RESULT =========
            $query = $qb->getQuery();
            //disable all caches
            $query->enableResultCache(false);
            if (!empty($options['hydrationMode'])) {
                $query->setHydrationMode($options['hydrationMode']);
            }
            if (isset($options['pagination']) && $options['pagination'] === false) {
                return $query->getResult($options['hydrationMode']);
            }
            $adapter = new PaginatorAdapter(new ORMPaginator($query));
            $adapter->getPaginator()->setUseOutputWalkers($useOutputWalkers);
            $paginator = new ZendPaginator($adapter);

            $page = empty($config['page']) ? 1 : $config['page'];
            $maxResult = empty($config['limit']) ? 20 : $config['limit'];

            $paginator->setCurrentPageNumber($page)->setItemCountPerPage($maxResult);
            //disable pagination caches
            $paginator->setCacheEnabled(false);
            return $paginator;
        }

        protected function getFieldName($rawName, array $fields)
        {
            if (empty($rawName)) {
                return null;
            }
            $key = trim($rawName);
            $res = preg_grep("/^$key$/i", $fields);
            return array_pop($res);
        }

        /**
         * ViewModelAware implementation
         * Gets entity extended with references for edit or details views
         * @param int $id
         * @param array $options
         * @return object
         * @throws RuntimeException
         */
        public function getDetails(int $id, array $options = [])
        {
            try {
                $identifierFields = $this->getClassMetadata()->getIdentifierFieldNames();
                if (count($identifierFields) > 1) {
                    throw new RuntimeException('Entities with multi-column identifiers are not supported');
                }
                $field = array_shift($identifierFields);
                $alias = $this->getAlias();
                $qb = $this->createQueryBuilder($alias)
                    ->where(sprintf('%s.%s = :id', $alias, $field))
                    ->setParameter('id', $id);
                /*if ($this->getAcl() !== null && $this->getAcl()->isRestrictedByCompany($options['user'])) {
                    $this->addUserRestriction($qb, $options['user']);
                }*/
                $this->postCreateQueryBuilder($qb, ['id' => $id], $options);
                return $qb->getQuery()->getOneOrNullResult();
            } catch (NonUniqueResultException $e) {
                throw new RuntimeException(sprintf('Given Id "%s" is not unique into "%s" table', $id, $this->getClassMetadata()->getTableName()), 0, $e);
            }
        }


        /**
         * Adds left join to query if not exists
         * @param QueryBuilder $qb
         * @param string $associationField
         * @param string|null $entityName
         * @param int $joinType
         * @return string alias of join
         * @noinspection PhpUnused
         */
        protected function addAssociationJoin(QueryBuilder $qb, string $associationField, string $entityName = null, int $joinType = 0): ?string
        {
            $entity = $entityName ?? $this->_entityName;
            if(!$this->getEntityManager()->getClassMetadata($entity)->hasAssociation($associationField)){
                return null;
            }
            $targetClass = $this->getEntityManager()->getClassMetadata($entity)->getAssociationTargetClass($associationField);
            $daoT = $this->getEntityManager()->getRepository($targetClass);

            if ($daoT instanceof AbstractDoctrineDao) {
                $allAliases = $qb->getAllAliases();
                /** @var AbstractDoctrineDao $dao */
                $dao =$this->getEntityManager()->getRepository($entity);
                // owner of association
                $alias =$dao->getAlias();
                // association
                $aliasA = $daoT->getAlias();
                if (in_array($aliasA, $allAliases, true)) {
                    return $aliasA;
                }
                if ($joinType === Join::JOIN_TYPE_INNER) {
                    $qb->innerJoin("$alias.$associationField", $aliasA);
                } else {
                    $qb->leftJoin("$alias.$associationField", $aliasA);
                }
                return $aliasA;
            }
            return null;
        }

//    ================== ABSTRACT/EMPTY IMPLEMENTATIONS ==================

        /**
         * Gets Query Builder
         * @return QueryBuilder
         */
        protected function getQueryBuilder(): QueryBuilder
        {
            return $this->createQueryBuilder($this->getAlias());
        }


        /**
         * Override and implement me in the child DAO
         * @param QueryBuilder $qb
         * @param User $user
         */
        protected function addUserRestriction(QueryBuilder $qb, User $user = null): void
        {
        }

        /**
         * Override and implement me in the child DAO
         * @param QueryBuilder $qb
         * @param array $rawParams
         * @param array $options
         */
        protected function postCreateQueryBuilder(QueryBuilder $qb, array $rawParams = [], array $options = []): void
        {
        }

        /**
         * Override and implement me in the child DAO
         * @param QueryBuilder $qb
         * @param $value
         * @param string $fieldName
         * @param bool $isAssociation
         * @return bool
         * @noinspection PhpUnusedParameterInspection
         */
        public function preCreateFilter(QueryBuilder $qb, $value, string $fieldName,bool $isAssociation): bool
        {
            return true;
        }

        /**
         * Override and implement me in the child DAO
         * @param QueryBuilder $qb
         * @param $param
         * @param $isAssociation
         * @return bool
         * @noinspection PhpUnusedParameterInspection
         */
        public function preCreateSort(QueryBuilder $qb, $param, $isAssociation): bool
        {
            return true;
        }

    }
