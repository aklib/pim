<?php

/**
 *
 * AbstractDAO.php
 *
 * @since  14.05.2017
 * @author Alexej Kisselev <alexej.kisselev@gmail.com>
 */

namespace Application\Repository;

use Application\Entity\AppStatus;
use Application\ServiceManager\Interfaces\Constant;
use Application\ServiceManager\Interfaces\DoctrineDaoAware;
use Application\Utils\ClassUtils;
use DateTime;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query;
use Doctrine\ORM\UnitOfWork;
use Exception;
use FlorianWolters\Component\Core\StringUtils;
use InvalidArgumentException;
use RuntimeException;
use User\Entity\User;

abstract class AbstractDoctrineDao extends AbstractAwareDao  implements DoctrineDaoAware
{

    private array $requestCache = [];

    /**
     * Persists an entity into database
     *
     * @param object|object[] $entities
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function doSave($entities): void
    {
        if (empty($entities)) {
            return;
        }
        if (!is_array($entities)) {
            $entities = [$entities];
        }
        $em = $this->getEntityManager();
        $now = new DateTime();
        $identity = $this->getCurrentUser();
        foreach ($entities as $key => $entity) {
            if (!is_object($entity)) {
                throw new InvalidArgumentException(sprintf(
                    '%s expects parameter to be valid entity object or array of entities, "%s" given', __METHOD__, ClassUtils::getName($entity)
                ));
            }

            $isNew = $em->getUnitOfWork()->getEntityState($entity) === UnitOfWork::STATE_NEW;
            if ($isNew) {
                if (method_exists($entity, 'setCreated')) {
                    $entity->setCreated($now);
                }
                if ($identity instanceof User && method_exists($entity, 'setCreateId')) {
                    $entity->setCreateId($identity->getId());
                }
            }
            if (method_exists($entity, 'setChanged')) {
                $entity->setChanged($now);
            }
            if ($identity instanceof User && method_exists($entity, 'setChangeId')) {
                $entity->setChangeId($identity->getId());
            }
            $em->persist($entity);
        }
        $em->flush();
    }

    /**
     * @param $entity
     * @return int
     * @noinspection PhpUnused
     */
    protected function getEntityStatus($entity): int
    {
        return $this->getEntityManager()->getUnitOfWork()->getEntityState($entity);
    }

    /**
     * Deletes entities
     *
     * @param object|object[] $entities
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function doDelete($entities): void
    {
        if (empty($entities)) {
            return;
        }
        try {
            if (!is_array($entities)) {
                $entities = [$entities];
            }
            $em = $this->getEntityManager();
            foreach ($entities as $key => $entity) {
                if (!is_object($entity)) {
                    throw new InvalidArgumentException(sprintf(
                        '%s expects parameter to be valid entity object or array of entities, "%s" given', __METHOD__,
                        ClassUtils::getName($entity)
                    ));
                }
                $em->remove($entity);
            }
            $em->flush();
            //$this->invalidateCache();
        } catch (InvalidArgumentException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new RuntimeException('Failed to delete entities', 0, $e);
        }
    }

    /**
     * Checks if specified by field value entity exists
     * @param string $fieldName
     * @param string $value
     * @return bool
     */
    public function exists(string $fieldName, string $value): bool
    {
        if (!$this->getEntityManager()->getClassMetadata($this->_entityName)->hasField($fieldName)) {
            return false;
        }
        $entityName = $this->_entityName;
        $dql = "SELECT e.$fieldName FROM $entityName e WHERE e.$fieldName = :value";
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('value', $value);
        try {
            return $query->getOneOrNullResult(AbstractQuery::HYDRATE_SCALAR) !== null;
        } catch (NonUniqueResultException $e) {
        }
        return false;
    }

//================================ HELP METHODS ================================

    /**
     * Gets options list for form select elements or other usage eg. decorator
     * @param string $name
     * @return array
     */
    final public function getNamedQueryResult(string $name): array
    {
        if (isset($this->requestCache[__METHOD__][$name][$this->getEntityName()])) {
            // Lesha, this is ONLY(!) one request cache!
            return $this->requestCache[__METHOD__][$name][$this->getEntityName()];
        }
        $namedQueries = $this->getEntityManager()->getClassMetadata($this->getEntityName())->namedQueries;
        if (!isset($namedQueries[$name])) {
            throw new RuntimeException(sprintf(
                '%s: Dear developer! Please implement an Named Query "%s" in %s',
                __METHOD__,
                $name,
                $this->getEntityName()
            ));
        }

        try {
            $query = $this->createNamedQuery($name);
            $this->setNamedQueryParameter($query);
            $results = $query->getResult();

            if (str_starts_with($name, Constant::NAMED_QUERY_DROPDOWN_CHOICE)) {
                $options = [];
                foreach ($results as $result) {
                    //create dropdown options
                    $id = array_shift($result);
                    $options[$id] = $this->getDropDownChoiceText($result);
                }
                $results = $options;
            } elseif ($name === Constant::NAMED_QUERY_ATTRIBUTE_COLUMNS) {
                $options = [];
                foreach ($results as $result) {
                    //create columns options
                    $options[$result['name']] = $result;
                }
                $results = $options;
            }
            return $this->requestCache[__METHOD__][$name][$this->getEntityName()] = $results;
        } catch (Exception $e) {
                throw $e;
        }
        return [];

    }

    /**
     * Can be overridden in the repository class to customize a dropdown text
     * @param array $data
     * @return string
     */
    public function getDropDownChoiceText(array $data): string
    {
        return implode(' ', $data);
    }

    protected function getAlias(): string
    {
        return lcfirst(StringUtils::substringAfterLast($this->_entityName, "\\"));
    }

    /**
     * Sets parameter(s) for named query
     * @param Query $query
     */
    public function setNamedQueryParameter(Query $query): void
    {
        // override in child class to set parameter
    }

}
