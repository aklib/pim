<?php
/**
 * Class PublisherDao
 * @package Report\Repository
 *
 * since: 13.10.2020
 * author: alexej@kisselev.de
 */

namespace Report\Repository;


use Company\Entity\Publisher;
use Elastic\ODM\Query\QueryBuilder;
use Elastic\Repository\AbstractElasticsearchDao;
use Elastica\Query\Terms;
use User\Entity\User;

class PublisherDao extends AbstractElasticsearchDao
{
    /**
     * @param QueryBuilder $qb
     * @param User|null $user
     */
    protected function addUserRestrictions(QueryBuilder $qb, User $user = null): void
    {
        /** @var User $user */
        $user = $user ?? $this->getCurrentUser();
        if (!$user->getUserRole()->isRestricted()) {
            return;
        }
        $publisherCompanies = $user->getPublishers();
        $ids = [];
        /** @var Publisher $publisherCompany */
        foreach ($publisherCompanies as $publisherCompany) {
            $ids[] = $publisherCompany->getId();
        }
        $terms = new Terms('publisher.id', $ids);
        $qb->getQueryBool()->addMust($terms);
    }
}