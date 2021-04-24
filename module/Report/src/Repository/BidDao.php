<?php
/**
 * Class BidDao
 * @package Report\Repository
 *
 * since: 28.09.2020
 * author: alexej@kisselev.de
 */

namespace Report\Repository;

use Company\Entity\Publisher;
use DateTime;
use Elastic\ODM\Query\QueryBuilder;
use Elastic\Repository\AbstractElasticsearchDao;
use Elastica\Aggregation\Terms as AggTerms;
use Elastica\Exception\InvalidException;
use Elastica\Query\Range;
use Elastica\Query\Terms as QueryTerms;
use Report\Adapter\DashboardAdapter;
use User\Entity\User;
use User\Repository\UserDao;


class BidDao extends AbstractElasticsearchDao
{
    public function getStatistics(array $campaignIds, $today = false): array
    {
        $qb = $this->getQueryBuilder();
        $queryBool = $qb->getQueryBool();
        $this->addUserRestrictions($qb);
        $terms = new QueryTerms('campaign.id', $campaignIds);
        $queryBool->addMust($terms);

        $query = $qb->getQuery();
        $terms = (new AggTerms('id'));
        $terms->setField('campaign.id');

        /* $stats = new Stats('bidCountS');
         $stats->setScript([
             'lang'   => 'painless',
             'source' => "doc['id'].count"
         ]);
         $terms->addAggregation($stats);*/

        /** @var DashboardAdapter $dashboardAdapter */
        $dashboardAdapter = $this->getServiceManager()->get(DashboardAdapter::class);
        $agg = $dashboardAdapter->bidCount(1, 'publisher_campaign')['bidCount']['query']['targetClass'];
        $terms->addAggregation($agg);

        $agg = $dashboardAdapter->bidWon(1, 'publisher_campaign')['bidWon']['query']['targetClass'];
        $terms->addAggregation($agg);

        $agg = $dashboardAdapter->bidClicked(1, 'publisher_campaign')['bidClicked']['query']['targetClass'];
        $terms->addAggregation($agg);
        if ($today) {
            $agg = $dashboardAdapter->cardinalityIp(1, 'cardinality_campaign')['cardinalityIp']['query']['targetClass'];
            $terms->addAggregation($agg);
        }

        $agg = $dashboardAdapter->advertiserCost(1, 'publisher_campaign')['costs']['query']['targetClass'];
        $terms->addAggregation($agg);

        $query->addAggregation($terms);

        /*if($today){
            echo '<pre>';
            print_r(json_encode($query->toArray()));
            echo '</pre><br>';
            die;
        }*/

        $resultSet = $qb->getResult();
        $response = ['success' => $resultSet->getResponse()->isOk()];
        if ($resultSet->getResponse()->isOk()) {
            try {
                $result = $resultSet->getAggregation('id');
                if (!empty($result['buckets'])) {
                    foreach ($result['buckets'] as $data) {
                        $response[$data['key']] = $data;
                    }
                }
            } catch (InvalidException $e) {
                // aggregation missing - problem with elasticsearch index
                $response['success'] = false;
            }
            return $response;
        }
        return $response;
    }

    public function getStatisticsToday(array $campaignIds): array
    {
        $fromDate = new DateTime();
        $fromDate->setTime(0, 0, 0);
        $toDate = new DateTime();
        //dump($fromDate);
        $rangeQuery = new Range('day');
        $rangeQuery->addField('day', [
            'gte' => $fromDate->format('c'),
            'lte' => $toDate->format('c'),
        ]);
        $qb = $this->getQueryBuilder();
        $queryBool = $qb->getQueryBool();
        $queryBool->addMust($rangeQuery);
        return $this->getStatistics($campaignIds, true);
    }

    /**
     * @param QueryBuilder $qb
     * @param User|null $user
     */
    protected function addUserRestrictions(QueryBuilder $qb, User $user = null): void
    {
        /** @var UserDao $dao */
        $dao = $this->getEntityManager()->getRepository(User::class);

        /** @var User $user */
        $user = $user ?? $this->getCurrentUser();
        if (!$user->getUserRole()->isRestricted()) {
            return;
        }
        $publisherCompanies = $user->getAdvertisers();
        $ids = [];
        /** @var Publisher $publisherCompany */
        foreach ($publisherCompanies as $publisherCompany) {
            $ids[] = $publisherCompany->getId();
        }
        $terms = new QueryTerms('advertiser.id', $ids);
        $qb->getQueryBool()->addMust($terms);
    }
}