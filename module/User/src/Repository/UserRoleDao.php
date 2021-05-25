<?php
/**
 * Class UserRoleDao
 * @package User\Repository * since: 19.07.2020
 * author: alexej@kisselev.de
 */

namespace User\Repository;


use Application\Repository\AbstractModelDao;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\AST\Join;

/**
 * @method findOneByName(string $role)
 */
class UserRoleDao extends AbstractModelDao
{
    public function setNamedQueryParameter(Query $query): void
    {
        $query->setParameter('priority', $this->getCurrentUser()->getUserRole()->getPriority());

    }

    public function getAll()
    {
        $alias = $this->getAlias();
        $qb = $this->getQueryBuilder();
        $this->addAssociationJoin($qb, 'resources', $this->getEntityName(), Join::JOIN_TYPE_INNER);
        return $qb->getQuery()->getResult();
    }


}