<?php

namespace User\Repository;

use Attribute\Repository\AbstractAttributeDao;
use Doctrine\ORM\NonUniqueResultException;
use User\Entity\User;


/**
 * This is the custom repository class for User entity.
 */
class UserDao extends AbstractAttributeDao
{
    private ?User $authenticationUser = null;

    /**
     * User for authentication to reduce connections
     *
     * @param string $email
     * @return User|null
     * @todo reduce connections - qb->addSelect() don't work
     */
    public function getAuthenticationUser(string $email): ?User
    {
        if ($this->authenticationUser === null) {
            $alias = $this->getAlias();
            //$this->authenticationUser =
            $qb = $this->getQueryBuilder();
            try {
                $this->authenticationUser =
                    $qb
                        /*->innerJoin("$alias.userRole", 'r')
                        ->innerJoin("$alias.status", 's')*/
                        ->andWhere("$alias.email = :email")
                        ->setParameter('email', $email)
                        /*->addSelect('r')
                        ->addSelect('s')*/
                        ->getQuery()
                        ->getOneOrNullResult();
            } catch (NonUniqueResultException $e) {
                return null;
            }
        }
        return $this->authenticationUser;
    }
}