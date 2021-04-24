<?php

namespace Product\Repository;

use Attribute\Repository\AbstractAttributeDao;
use Doctrine\ORM\QueryBuilder;
use \InvalidArgumentException;
use User\Entity\User;

/**
 * This is the custom repository class for Product (campaign) entity.
 */
class ProductDao extends AbstractAttributeDao
{
    /**
     * @param QueryBuilder $qb
     * @param User|null $user
     */
    protected function addUserRestriction(QueryBuilder $qb, User $user = null): void
    {
        parent::addUserRestriction($qb, $user);
        /** @var User $user */
        $user ??= $this->getCurrentUser();
        if ($user === null) {
            throw new InvalidArgumentException(sprintf('The current user must be a valid User instance. %s given.', gettype($user)));
        }
        if (!$user->getUserRole()->isRestricted()) {
            return;
        }

        $qb
            ->andWhere($qb->expr()->in($this->getAlias() . '.advertiser', ':advertiser'))
            ->setParameter('advertiser', $user->getAdvertisers());
    }
}