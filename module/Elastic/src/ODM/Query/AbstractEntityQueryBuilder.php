<?php
/**
 * Class AbstractQueryBuilder
 * @package Elastic\ODM\Query
 *
 * since: 27.09.2020
 * author: alexej@kisselev.de
 */

namespace Elastic\ODM\Query;


use Application\ServiceManager\AbstractAwareContainer;
use Application\Utils\DateTimeUtils;
use DateTime;
use DateTimeZone;
use Elastic\Factory\Client;
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\Range;
use Elastica\Search;

abstract class AbstractEntityQueryBuilder extends AbstractAwareContainer
{
    protected string $indexDefault = 'statistics-*';
    private string $timezone;

    /**
     * @var Query
     */
    protected Query $query;
    /**
     * @var Search
     */
    protected Search $search;
//======================= INTERFACE IMPLEMENTATIONS =======================


    /**
     * AbstractQueryBuilder constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->query = $this->createQuery();
        $this->timezone = date_default_timezone_get();
    }

    private function createQuery(): Query
    {
        $query = new Query();
        $query
            ->setTrackTotalHits(true)
            ->setSize(0)
            ->setFrom(0);
        return $query;
    }

    public function init(): void
    {
        $client = $this->getServiceManager()->get(Client::class);
        $this->search = new Search($client);
    }

// ====================== ELASTICSEARCH ======================

    /**
     * @param string $index
     * @return AbstractEntityQueryBuilder
     */
    public function addIndex(string $index): AbstractEntityQueryBuilder
    {
        $this->search->addIndex($index);
        return $this;
    }

    public function getQueryBool(): BoolQuery
    {
        if (!$this->query->hasParam('query')) {
            $queryBool = new BoolQuery();
            $this->query->setQuery($queryBool);
        }
        return $this->query->getParam('query');
    }


    /**
     * @return string
     * @noinspection PhpUnused
     */
    public function getTimezone(): string
    {
        return $this->timezone;
    }

    /**
     * @param string|null $timezone
     * @return AbstractEntityQueryBuilder
     */
    public function setTimezone(string $timezone = null): AbstractEntityQueryBuilder
    {
        if ($timezone !== null) {
            $this->timezone = urldecode($timezone);
        }
        return $this;
    }

    /**
     * @return Range|null
     */
    public function getDateRange(): ?Range
    {
        if ($this->getQueryBool()->hasParam('range')) {
            $this->getQueryBool()->getParam('range');
        }
        return null;
    }

    /**
     * @param string|null $dateRange raw param from URL query
     * @return AbstractEntityQueryBuilder
     */
    public function setDateRange(string $dateRange = null): AbstractEntityQueryBuilder
    {
        if (!empty($dateRange)) {
            $range = urldecode($dateRange);
            $fromString = DateTimeUtils::getFromRangeFrom($range);
            $toString = DateTimeUtils::getFromRangeTo($range);
            $fromDate = DateTimeUtils::from($fromString);
            $toDate = DateTimeUtils::from($toString);
        } else {
            $fromDate = new DateTime();
            $toDate = new DateTime();
        }
        $dateTimeZone = new DateTimeZone($this->timezone);
        //dump($fromDate);
        $fromDate->setTime(0, 0);
        $toDate->setTime(23, 59, 59);
        $fromDate = $fromDate->setTimezone($dateTimeZone);
        $toDate = $toDate->setTimezone($dateTimeZone);

        $rangeQuery = new Range('day');
        $rangeQuery->addField('day', [
            'gte' => $fromDate->format('Y-m-d\TH:i:s'),
            'lte' => $toDate->format('Y-m-d\TH:i:s'),
        ]);
        $this->getQueryBool()->addMust($rangeQuery);

        return $this;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return (int)$this->query->getParam('size');
    }

    /**
     * @param int $size
     * @return AbstractEntityQueryBuilder
     */
    public function setSize(int $size): AbstractEntityQueryBuilder
    {
        $this->query->setSize($size);
        return $this;
    }

    public function getQuery(): Query
    {
        return $this->query;
    }

//======================= METHODS =======================

}