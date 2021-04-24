<?php
/**
 * Class AdvertiserDao
 * @package Report\Repository
 *
 * since: 13.10.2020
 * author: alexej@kisselev.de
 */

namespace Report\Repository;


use Company\Entity\Advertiser;
use Elastic\ODM\Query\QueryBuilder;
use Elastic\Repository\AbstractElasticsearchDao;
use Elastica\Query\Terms;
use User\Entity\User;

class AdvertiserDao extends AbstractElasticsearchDao
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
        $advertiserCompanies = $user->getAdvertisers();
        $ids = [];
        /** @var Advertiser $advertiserCompany */
        foreach ($advertiserCompanies as $advertiserCompany) {
            $ids[] = $advertiserCompany->getId();
        }
        $terms = new Terms('advertiser.id', $ids);
        $qb->getQueryBool()->addMust($terms);
    }
}