<?php

namespace Category\Repository;

use Application\ServiceManager\Interfaces\Constant;
use Application\ServiceManager\Interfaces\DoctrineDaoAware;
use Application\ServiceManager\Interfaces\ViewModelAware;
use Category\Entity\Category;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Query;
use Exception;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use RuntimeException;

/**
 * This is the custom repository class for User entity.
 */
class CategoryDao extends NestedTreeRepository implements ViewModelAware, DoctrineDaoAware
{

    /**
     * @param array $config
     * @param array $options
     * @return array|Collection|object[]
     */
    public function getList(array $config = [], array $options = []): array
    {
        return $this->findAll();
    }

    /**
     * @param int $id
     * @return object|null
     */
    public function getDetails(int $id): ?Category
    {
        return $this->find($id);
    }

    final public function getNamedQueryResult(string $name): array
    {

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
            }
            return $results;
        } catch (Exception $e) {

        }
        return [];

    }

    public function setNamedQueryParameter(Query $query): void
    {

    }

    public function getDropDownChoiceText(array $data): string
    {
        return implode(' ', $data);
    }
}