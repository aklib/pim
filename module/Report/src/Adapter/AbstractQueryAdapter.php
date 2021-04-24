<?php /** @noinspection DuplicatedCode */
/** @noinspection PhpUnused */
/** @noinspection ContractViolationInspection */

/**
 * Class AbstractQueryAdapter
 * @package Report\Adapter
 *
 * since: 14.10.2020
 * author: alexej@kisselev.de
 */

namespace Report\Adapter;

use Application\Utils\DateTimeUtils;
use Application\View\Manager\EmptyAdapter;
use Elastica\Aggregation\Avg;
use Elastica\Aggregation\DateHistogram;
use Elastica\Aggregation\Filter;
use Elastica\Aggregation\Max;
use Elastica\Aggregation\Min;
use Elastica\Aggregation\Sum;
use Elastica\Aggregation\Terms;
use Elastica\Query\Term;

class AbstractQueryAdapter extends EmptyAdapter
{
    public function bidCount(int $sortOrder = 1, string $context = 'publisher_traffic'): array
    {
        // all
        return [
            'bidCount' => [
                'name'      => 'bidCount',
                'label'     => 'Count Bids',
                'subTitle'  => 'count bids',
                'sortOrder' => $sortOrder,
                'type'      => 'integer',
                'method'    => 'getAggregationCount',
                'tab'       => 'summary',
                'query'     => [
                    'targetClass' => (new Sum('bidCount'))->setField("$context.bids")
                ]
            ],
        ];
    }

    public function totalHitsImport(int $sortOrder = 1, string $context = 'import_campaign'): array
    {
        // all
        return [
            'totalHitsImport' => [
                'name'      => 'totalHitsImport',
                'label'     => 'Count Requests Adv.',
                'subTitle'  => 'total count received from advertisers',
                'class'     => 'border-success',
                'sortOrder' => $sortOrder,
                'type'      => 'integer',
                'method'    => 'getAggregationCount',
                'tab'       => 'summary',
                'query'     => [
                    'targetClass' => (new Sum('totalHitsImport'))->setField("$context.count_requests")
                ]
            ],
        ];
    }

    public function advertiserCost(int $sortOrder = 1, string $context = 'publisher_traffic'): array
    {
        // costs: admin and advertiser. No publisher
        return [
            'costs' => [
                'name'      => 'costs',
                'label'     => 'Costs',
                'subTitle'  => 'sum publisher_traffic.bids ($)',
                'sortOrder' => $sortOrder,
                'method'    => 'getAggregationCount',
                'tab'       => 'summary',
                'query'     => [
                    //'targetClass' => (new Sum('costs'))->setField('campaign.bid'),
                    'targetClass' => (new Sum('costs'))->setField("$context.cost"),
                ]
            ],
        ];
    }

    protected function adminCost(int $sortOrder = 1, string $context = 'publisher_traffic'): array
    {
        // Admin = bid - fee. No publisher, advertiser
        return [
            'revenue' => [
                'name'      => 'adminCosts',
                'label'     => 'Costs Advertiser',
                'subTitle'  => 'sum (bid - fee) ($)',
                'sortOrder' => $sortOrder,
                'method'    => 'getAggregationCount',
                'tab'       => 'summary',
                'query'     => [
                    'targetClass' => (new Sum('adminCosts'))->setField("$context.revenue")
                ]
            ],
        ];
    }

    public function publisherRevenue(int $sortOrder = 1, string $context = 'publisher_traffic'): array
    {
        // For admin - cost: publisher-revenue. No and advertiser
        return [
            'revenue' => [
                'name'      => 'publisherRevenue',
                'label'     => 'Revenue Publisher',
                'subTitle'  => 'sum bid_result ($)',
                'sortOrder' => $sortOrder,
                'method'    => 'getAggregationCount',
                'tab'       => 'summary',
                'query'     => [
                    'targetClass' => (new Sum('publisherRevenue'))->setField("$context.revenue"),
                ]
            ],
        ];
    }


    protected function adminRevenue(int $sortOrder = 1, string $context = 'publisher_traffic'): array
    {
        // get from advertiser. No publisher
        return [
            'costs' => [
                'name'      => 'adminRevenue',
                'label'     => 'Revenue Publisher',
                'subTitle'  => 'sum bid ($)',
                'sortOrder' => $sortOrder,
                'method'    => 'getAggregationCount',
                'tab'       => 'summary',
                'query'     => [
                    //'targetClass' => (new Sum('costs'))->setField('campaign.bid'),
                    'targetClass' => (new Sum('adminRevenue'))->setField("$context.cost")
                ]
            ],
        ];
    }

    protected function adminProfit(int $sortOrder = 1, string $context = 'publisher_traffic'): array
    {
        /* $agg = (new Sum('profit'))
             ->setField('campaign.bid_fee')
             ->setScript(new Script("doc['campaign.bid_won'].value ? _value : 0"));*/
        // check user role?
        return [
            'profit' => [
                'name'      => 'profit',
                'label'     => 'Profit Platform',
                'subTitle'  => 'sum fee',
                'sortOrder' => $sortOrder,
                'method'    => 'getAggregationCount',
                'tab'       => 'summary',
                'query'     => [
                    //'targetClass' => (new Sum('costs'))->setField('campaign.bid'),
                    'targetClass' => (new Sum('profit'))->setField("$context.profit")
                ]
            ],
        ];
    }

    public function bidWon(int $sortOrder = 1, string $context = 'publisher_traffic'): array
    {
        return [
            'bidWon' => [
                'name'      => 'bidWon',
                'label'     => 'Bid Won',
                'subTitle'  => 'count bid_won',
                'sortOrder' => $sortOrder,
                'method'    => 'getAggregationCount',
                'tab'       => 'summary',
                'query'     => [
                    'targetClass' => (new Sum('bidWon'))->setField("$context.bids_won")
                ]
            ],
        ];
    }
    public function bidClickedImport(int $sortOrder = 1, string $context = 'import_campaign'): array
    {
        return [
            'bidClickedImport' => [
                'name'      => 'bidClickedImport',
                'label'     => 'Bid Clicked',
                'subTitle'  => 'count bids_clicked received from advertisers',
                'class'     => 'border-success',
                'sortOrder' => $sortOrder,
                'method'    => 'getAggregationCount',
                'tab'       => 'summary',
                'query'     => [
                    'targetClass' => (new Sum('bidClickedImport'))->setField("$context.bids_clicked")
                ]
            ],
        ];
    }

    public function bidClickedGrossImport(int $sortOrder = 1, string $context = 'import_campaign'): array
    {
        return [
            'bidClickedGrossImport' => [
                'name'      => 'bidClickedGrossImport',
                'label'     => 'Bid Clicked Gross',
                'subTitle'  => 'count bids_clicked received from advertisers',
                'class'     => 'border-success',
                'sortOrder' => $sortOrder,
                'method'    => 'getAggregationCount',
                'tab'       => 'summary',
                'query'     => [
                    'targetClass' => (new Sum('bidClickedGrossImport'))->setField("$context.bids_clicked_gross")
                ]
            ],
        ];
    }

    public function bidClicked(int $sortOrder = 1, string $context = 'publisher_traffic'): array
    {
        return [
            'bidClicked' => [
                'name'      => 'bidClicked',
                'label'     => 'Bid Clicked',
                'subTitle'  => 'count bids_clicked',
                'sortOrder' => $sortOrder,
                'method'    => 'getAggregationCount',
                'tab'       => 'summary',
                'query'     => [
                    'targetClass' => (new Sum('bidClicked'))->setField("$context.bids_clicked")
                ]
            ],
        ];
    }

    public function requestStatus(int $sortOrder = 1, int $code = 462): array
    {
        $colName = 'request_status_' . $code;
        switch ($code) {
            case 460:
                $label = 'Publisher not Found';
                $subTitle = 'bad request url';
                break;
            case 461:
                $label = 'IP not Set';
                $subTitle = 'bad request url';
                break;
            case 462:
                $label = 'Campaign not Found';
                $subTitle = 'campaign not matched';
                break;
            default:
                $label = 'Request Status ' . $code;
                $subTitle = 'bad request';
        }
        $filter = new Filter($colName, new Term(['request_status.code' => $code]));
        $filter->addAggregation((new Sum('count'))->setField('request_status.count_requests'));
        return [
            $colName => [
                'name'      => $colName,
                'label'     => $label,
                'subTitle'  => $subTitle,
                'class'     => 'border-warning',
                'sortOrder' => $sortOrder,
                'method'    => 'getFilterCount',
                'tab'       => 'summary',
                'query'     => [
                    'targetClass' => $filter
                ]
            ],
        ];
    }

    public function cardinalityIp(int $sortOrder = 1, string $context = ''): array
    {
        $field = empty($context) ? 'cardinality' : "$context.cardinality";
        return [
            'cardinalityIp' => [
                'name'      => 'cardinalityIp',
                'label'     => 'Unique Ip',
                'subTitle'  => '',
                'sortOrder' => $sortOrder,
                'type'      => 'integer',
                'method'    => 'getAggregationCount',
                'tab'       => 'summary',
                'query'     => [
                    'targetClass' => (new Terms('cardinalityIp'))->setField($field),
                ]
            ],
        ];
    }

    public function totalHits(int $sortOrder = 1, string $context = 'publisher_traffic'): array
    {
        return [
            'totalHits' => [
                'name'      => 'totalHits',
                'tab'       => 'summary',
                'label'     => 'Count Requests',
                'subTitle'  => 'total count',
                'sortOrder' => $sortOrder,
                'type'      => 'integer',
                'method'    => 'getAggregationCount',
                'query'     => [
                    'targetClass' => (new Sum('totalHits'))->setField("$context.count_requests")
                ]
            ],
        ];
    }


    protected function processingTimeMin(int $sortOrder = 1): array
    {
        return [
            'processing_time_min' => [
                'name'      => 'processing_time_min',
                'label'     => 'Min',
                'subTitle'  => 'min proc time',
                'sortOrder' => $sortOrder,
                'type'      => 'integer',
                'method'    => 'getAggregationCount',
                'tab'       => 'summary',
                'query'     => [
                    'targetClass' => (new Min('processing_time_min'))->setField('processing_time.sum'),
                ]
            ],
        ];
    }

    protected function processingTimeMax(int $sortOrder = 1): array
    {
        return [
            'processing_time_max' => [
                'name'      => 'processing_time_max',
                'label'     => 'Max',
                'subTitle'  => 'max proc time',
                'sortOrder' => $sortOrder,
                'type'      => 'integer',
                'method'    => 'getAggregationCount',
                'tab'       => 'summary',
                'query'     => [
                    'targetClass' => (new Max('processing_time_max'))->setField('processing_time.sum'),
                ]
            ],
        ];
    }

    protected function processingTimeAvg(int $sortOrder = 1): array
    {
        return [
            'processing_time_avg' => [
                'name'      => 'processing_time_avg',
                'label'     => 'Avg',
                'subTitle'  => 'avg proc time',
                'sortOrder' => $sortOrder,
                'type'      => 'integer',
                'method'    => 'getAggregationCount',
                'tab'       => 'summary',
                'query'     => [
                    'targetClass' => (new Avg('processing_time_avg'))->setField('processing_time.sum'),
                ]
            ],
        ];
    }

    /**
     * Single date histogram
     * @return DateHistogram
     */
    private function getDateHistogramAggregation(): DateHistogram
    {
        $params = $this->getUrlParams();
        $interval = 'hour';
        if (!empty($params['daterange'])) {
            $range = urldecode($params['daterange']);
            $fromString = DateTimeUtils::getFromRangeFrom($range);
            $toString = DateTimeUtils::getFromRangeTo($range);
            $fromDate = DateTimeUtils::from($fromString);
            $inter = DateTimeUtils::from($toString)->diff($fromDate);
            if ($inter->y > 0) {
                $interval = 'month';
            } elseif ($inter->m > 0) {
                $interval = 'day';
            } elseif ($inter->d > 7) {
                $interval = 'day';
            }
        }

        if (!empty($params['timezone'])) {
            $timezone = urldecode($params['timezone']);
        } else {
            $timezone = date_default_timezone_get();
        }
        $dateHistogram = new DateHistogram('histogram', 'day', $interval);
        $dateHistogram->setTimezone($timezone);
        return $dateHistogram;
    }


    /**
     * @param string $columnName
     * @param string $sumField
     * @return Terms
     */
    private function getDateHistogramAggregations(string $columnName, string $sumField): Terms
    {

        $dateHistogram = $this->getDateHistogramAggregation();
        $sum = (new Sum('count'))->setField($sumField);
        $dateHistogram->addAggregation($sum);

        $category = new Terms('category');
        $category->setField('traffic.category.keyword');

        $quality = new Terms('quality');
        $quality->setField('traffic.quality.keyword');

        // root aggregation
        $type = new Terms($columnName);
        $type->setField('traffic.type.keyword');

        $type->addAggregation($category);
        $category
            ->addAggregation($dateHistogram)
            ->addAggregation($quality);
        // for push
        $quality->addAggregation($dateHistogram);
        return $type;
    }

    private function getPeriodLabel(): string
    {
        $params = $this->getUrlParams();
        return empty($params['daterange']) ? 'today' : 'for a period ' . $params['daterange'];
    }

    protected function hourlyTraffic(int $sortOrder = 1): array
    {
        $type = $this->getDateHistogramAggregations('hourly_traffic', 'publisher_traffic.count_requests');
        $period = $this->getPeriodLabel();

        return [
            'hourly_traffic' => [
                'name'      => 'hourly_traffic',
                'sortOrder' => $sortOrder,
                'class'     => 'col-lg-12 mt-6',
                'type'      => 'default',// chart script (chart/[type].phtml
                'tab'       => 'chart',// chart distribution script (report/partial/[tab].phtml
                'chart'     => [
                    'chart'       => [
                        'type'     => 'areaspline',
                        'zoomType' => 'x'
                    ],
                    'title'       => [
                        'text' => 'Hourly Traffic'
                    ],
                    'subtitle'    => [
                        'text' => "Impressions count $period",
                    ],
                    'plotOptions' => [
                        'area'       => [
                            'stacking' => 'normal'
                        ],
                        'areaspline' => [
                            'stacking'    => 'normal',
                            'fillOpacity' => 0.5
                        ],
                    ],
                    'yAxis'       => [
                        'title' => 'Count bids',
                    ],
                    'xAxis'       => [
                        'type' => 'datetime',
                    ],
                    'legend'      => [
                        'layout' => 'horizontal',
                        'align'  => 'center'
                    ]
                ],
                'query'     => [
                    //'targetClass' => $dateHistogram,
                    'targetClass' => $type,
                ]
            ],
        ];
    }

    protected function campaignTopTableImport(int $sortOrder = 0): array
    {
        $campaignAgg = new Terms('campaignTopTableImport');
        $campaignAgg
            ->setField('campaign.id')
            ->setSize(20);
        $sum = (new Sum('count'))->setField('campaign_status.count_requests');
        $campaignAgg->addAggregation($sum);

        $campaignNameAgg = new Terms('name');
        $campaignNameAgg
            ->setField('campaign.name.keyword')
            ->setSize(1);

        $campaignAgg->addAggregation($campaignNameAgg);


       /* $statusAgg = new Terms('status');
        $statusAgg->setField('campaign_status.code');
        $statusAgg->addAggregation($sum);
        $campaignAgg->addAggregation($statusAgg);*/

        $agg = $this->totalHitsImport()['totalHitsImport']['query']['targetClass'];
        $campaignAgg->addAggregation($agg);

        $agg = $this->bidClicked(2, 'publisher_campaign')['bidClicked']['query']['targetClass'];
        $campaignAgg->addAggregation($agg);

        $agg = $this->bidClickedImport(3)['bidClickedImport']['query']['targetClass'];
        $campaignAgg->addAggregation($agg);

        $agg = $this->bidClickedGrossImport(4)['bidClickedGrossImport']['query']['targetClass'];
        $campaignAgg->addAggregation($agg);

        $agg = $this->bidCount(5, 'publisher_campaign')['bidCount']['query']['targetClass'];
        $campaignAgg->addAggregation($agg);

        $agg = $this->bidWon(6, 'publisher_campaign')['bidWon']['query']['targetClass'];
        $campaignAgg->addAggregation($agg);

        $agg = $this->adminCost(7, 'publisher_campaign')['revenue']['query']['targetClass'];
        $campaignAgg->addAggregation($agg);

        $agg = $this->adminRevenue(8, 'publisher_campaign')['costs']['query']['targetClass'];
        $campaignAgg->addAggregation($agg);

        $agg = $this->adminProfit(9, 'publisher_campaign')['profit']['query']['targetClass'];
        $campaignAgg->addAggregation($agg);

        $period = $this->getPeriodLabel();

        return [
            'campaignTopTableImport' => [
                'name'      => 'campaignTopTableImport',
                'sortOrder' => $sortOrder,
                'class'     => 'col-lg-12  mt-6',
                'type'      => 'default',// chart script (chart/[type].phtml
                'tab'       => 'table',// chart distribution script (report/partial/[tab].phtml
                'table'     => [
                    'title'    => [
                        'text' => 'Advertiser Campaigns Data'
                    ],
                    'subtitle' => [
                        'text' => "Campaign statistics $period",
                    ],
                ],
                'query'     => [
                    //'targetClass' => $dateHistogram,
                    'targetClass' => $campaignAgg,
                ]
            ],
        ];
    }


    protected function campaignTopTable(int $sortOrder = 0): array
    {
        $campaignAgg = new Terms('campaign_top');
        $campaignAgg
            ->setField('campaign.id')
            ->setSize(20);
        $sum = (new Sum('count'))->setField('campaign_status.count_requests');
        $campaignAgg->addAggregation($sum);

        $campaignNameAgg = new Terms('name');
        $campaignNameAgg
            ->setField('campaign.name.keyword')
            ->setSize(1);

        $campaignAgg->addAggregation($campaignNameAgg);


        $statusAgg = new Terms('status');
        $statusAgg->setField('campaign_status.code');
        $statusAgg->addAggregation($sum);
        $campaignAgg->addAggregation($statusAgg);

        $agg = $this->totalHitsImport()['totalHitsImport']['query']['targetClass'];
        $campaignAgg->addAggregation($agg);

        $agg = $this->bidClicked(2, 'publisher_campaign')['bidClicked']['query']['targetClass'];
        $campaignAgg->addAggregation($agg);

        $agg = $this->bidClickedImport(3)['bidClickedImport']['query']['targetClass'];
        $campaignAgg->addAggregation($agg);

        $agg = $this->bidClickedGrossImport(4)['bidClickedGrossImport']['query']['targetClass'];
        $campaignAgg->addAggregation($agg);

        $agg = $this->bidCount(5, 'publisher_campaign')['bidCount']['query']['targetClass'];
        $campaignAgg->addAggregation($agg);

        $agg = $this->bidWon(6, 'publisher_campaign')['bidWon']['query']['targetClass'];
        $campaignAgg->addAggregation($agg);

        $agg = $this->adminCost(7, 'publisher_campaign')['revenue']['query']['targetClass'];
        $campaignAgg->addAggregation($agg);

        $agg = $this->adminRevenue(8, 'publisher_campaign')['costs']['query']['targetClass'];
        $campaignAgg->addAggregation($agg);

        $agg = $this->adminProfit(9, 'publisher_campaign')['profit']['query']['targetClass'];
        $campaignAgg->addAggregation($agg);

        $period = $this->getPeriodLabel();

        return [
            'campaign_top' => [
                'name'      => 'campaign_top',
                'sortOrder' => $sortOrder,
                'class'     => 'col-lg-12  mt-6',
                'type'      => 'default',// chart script (chart/[type].phtml
                'tab'       => 'table',// chart distribution script (report/partial/[tab].phtml
                'table'     => [
                    'title'    => [
                        'text' => 'Top Campaigns'
                    ],
                    'subtitle' => [
                        'text' => "Campaign statistics $period",
                    ],
                ],
                'query'     => [
                    //'targetClass' => $dateHistogram,
                    'targetClass' => $campaignAgg,
                ]
            ],
        ];
    }

    protected function publisherCampaign(int $sortOrder = 0): array
    {
        $campaignAgg = new Terms('campaign');
        $campaignAgg
            ->setField('campaign.id')
            ->setSize(20);
        $sum = (new Sum('count'))->setField('publisher_campaign.count_requests');
        $campaignAgg->addAggregation($sum);

        $campaignNameAgg = new Terms('name');
        $campaignNameAgg
            ->setField('campaign.name.keyword')
            ->setSize(1);

        $campaignAgg->addAggregation($campaignNameAgg);

        $publisherAgg = new Terms('publisher_campaign');
        $publisherAgg
            ->setField('publisher.name.keyword')
            ->setSize(10);

        $publisherAgg->addAggregation($campaignAgg);
        $period = $this->getPeriodLabel();

        return [
            'publisher_campaign' => [
                'name'      => 'publisher_campaign',
                'sortOrder' => $sortOrder,
                'class'     => 'col-lg-3  mt-6',
                'type'      => 'default',// chart script (chart/[type].phtml
                'tab'       => 'chart',// chart distribution script (report/partial/[tab].phtml
                'chart'     => [
                    'chart'    => [
                        'type' => 'sankey'
                    ],
                    'title'    => [
                        'text' => 'Publisher Bids'
                    ],
                    'subtitle' => [
                        'text' => "Publisher Bids $period",
                    ],
                    'legend'   => [
                        'layout' => 'horizontal',
                        'align'  => 'center'
                    ],
                ],
                'query'     => [
                    //'targetClass' => $dateHistogram,
                    'targetClass' => $publisherAgg,
                ]
            ],
        ];
    }

    protected function hourlyBidClicked(int $sortOrder = 1): array
    {
        $type = $this->getDateHistogramAggregations('hourly_bid_clicked', 'publisher_traffic.bids_clicked');
        $period = $this->getPeriodLabel();

        return [
            'hourly_bid_clicked' => [
                'name'      => 'hourly_bid_clicked',
                'sortOrder' => $sortOrder,
                'class'     => 'col-lg-12  mt-6',
                'type'      => 'default',// chart script (chart/[type].phtml
                'tab'       => 'chart',// chart distribution script (report/partial/[tab].phtml
                'chart'     => [
                    'chart'       => [
                        'type'     => 'column',
                        'zoomType' => 'x'
                    ],
                    'title'       => [
                        'text' => 'Bid Clicked'
                    ],
                    'subtitle'    => [
                        'text' => "Bid Clicked $period",
                    ],
                    'plotOptions' => [
                        'area'       => [
                            'stacking' => 'normal'
                        ],
                        'areaspline' => [
                            'stacking'    => 'normal',
                            'fillOpacity' => 0.5
                        ],
                    ],
                    'yAxis'       => [
                        'title' => 'Count bids',
                    ],
                    'xAxis'       => [
                        'type' => 'datetime',
                    ],
                    'legend'      => [
                        'layout' => 'horizontal',
                        'align'  => 'center'
                    ]
                ],
                'query'     => [
                    //'targetClass' => $dateHistogram,
                    'targetClass' => $type,
                ]
            ],
        ];
    }

    protected function hourlyAdvertiserError(int $sortOrder = 1): array
    {
        $dateHistogram = $this->getDateHistogramAggregation();
        $sum = (new Sum('count'))->setField('error.count_requests');
        $dateHistogram->addAggregation($sum);

        $advertiser = new Terms('hourly_advertiser_error');
        $advertiser->setField('advertiser.name.keyword');

        $errors = new Terms('error');
        $errors->setField('error.message.keyword');
        $errors->addAggregation($dateHistogram);
        $advertiser->addAggregation($errors);

        $period = $this->getPeriodLabel();

        return [
            'hourly_advertiser_error' => [
                'name'      => 'hourly_advertiser_error',
                'sortOrder' => $sortOrder,
                'class'     => 'col-lg-12  mt-6',
                'type'      => 'default',// chart script (chart/[type].phtml
                'tab'       => 'chart',// chart distribution script (report/partial/[tab].phtml
                'chart'     => [
                    'chart'       => [
                        'type'     => 'column',
                        'zoomType' => 'x'
                    ],
                    'title'       => [
                        'text' => 'Advertiser Errors'
                    ],
                    'subtitle'    => [
                        'text' => "Advertiser Errors $period",
                    ],
                    'plotOptions' => [
                        'column' => [
                            'stacking' => 'normal'
                        ]
                    ],
                    'yAxis'       => [
                        'title' => 'Count errors',
                    ],
                    'xAxis'       => [
                        'type' => 'datetime',
                    ],
                    'legend'      => [
                        'layout' => 'horizontal',
                        'align'  => 'center'
                    ]
                ],
                'query'     => [
                    'targetClass' => $advertiser,
                ]
            ],
        ];
    }

    protected function cardinalityIPHistogram(int $sortOrder = 1): array
    {
        $terms = new Terms('count');
        $terms->setField('cardinality');
        $dateHistogram = $this->getDateHistogramAggregation();
        $dateHistogram->setName('cardinalityHistogram');
        $dateHistogram->setInterval('1d');
        $dateHistogram->addAggregation($terms);

        $period = $this->getPeriodLabel();

        return [
            'cardinalityHistogram' => [
                'name'      => 'cardinalityHistogram',
                'sortOrder' => $sortOrder,
                'class'     => 'col-lg-6  mt-6',
                'type'      => 'default',// chart script (chart/[type].phtml
                'tab'       => 'chart',// chart distribution script (report/partial/[tab].phtml
                'chart'     => [
                    'chart'       => [
                        'type' => 'column'
                    ],
                    'title'       => [
                        'text' => 'Unique IPs'
                    ],
                    'subtitle'    => [
                        'text' => "Count unique IPs $period",
                    ],
                    'plotOptions' => [
                        'series' => [
                            'dataLabels' => [
                                'enabled' => true,
                                'format'  => '{point.y:,0f}',
                            ],
//                            'showInLegend' => true
                        ],
                        'column' => [
                            'depth'    => 40,
                            'stacking' => 'normal'
                        ],
                    ],
                    'yAxis'       => [
                        'title' => 'Unique IPs',
                    ],
                    'xAxis'       => [
                        'type' => 'datetime'
                    ],
                    'legend'      => false
                ],
                'query'     => [
                    //'targetClass' => $dateHistogram,
                    'targetClass' => $dateHistogram,
                ]
            ],
        ];
    }

    protected function trafficDistribution(int $sortOrder = 1): array
    {
        $type = new Terms('traffic_distribution');
        $type->setField('traffic.type.keyword');

        $category = new Terms('category');
        $category = $category->setField('traffic.category.keyword');

        $quality = new Terms('quality');
        $quality = $quality->setField('traffic.quality.keyword');

        $sum = (new Sum('count'))->setField('publisher_traffic.count_requests');
        // push
        $quality->addAggregation($sum);
        // non-push
        $category->addAggregation($sum);

        $category->addAggregation($quality);
        $type->addAggregation($category);
        return [
            'traffic_distribution' => [
                'name'      => 'traffic_distribution',
                'label'     => 'Traffic Distribution',
                'class'     => 'col-lg-3  mt-6',
                'sortOrder' => $sortOrder,
                'type'      => 'default',// chart script (chart/[type].phtml
                'tab'       => 'chart',// chart distribution script (report/partial/[tab].phtml
                'chart'     => [
                    'chart'       => [
                        'type' => 'column'
                    ],
                    'title'       => [
                        'text' => 'Traffic Distribution'
                    ],
                    'plotOptions' => [
                        'series' => [
                            'dataLabels' => [
                                'enabled' => true,
                                'format'  => '{point.y:,0f}',
                            ],
//                            'showInLegend' => true
                        ],
                        'column' => [
                            'depth'    => 40,
                            'stacking' => 'normal'
                        ],
                    ],
                    'legend'      => true,
                    'yAxis'       => [
                        'title' => 'Count bids',
                    ],
                    'xAxis'       => [
                        'type'       => 'category',
                        'categories' => [
                            /* 'pop mainstream',
                             'pop adult',
                             'push mainstream',
                             'push adult',*/
                        ],
                    ],
                ],
                'query'     => [
                    //'targetClass' => $dateHistogram,
                    'targetClass' => $type,
                ]
            ],
        ];
    }

    protected function trafficBrowser(int $sortOrder = 1): array
    {
        $aggregation = new Terms('traffic_browser');
        $aggregation = $aggregation->setField('browser.name.keyword');
        $aggregation = $aggregation->setSize(20);
        $sum = (new Sum('count'))->setField('publisher_browser.count_requests');
        $aggregation->addAggregation($sum);
        return [
            'traffic_browser' => [
                'name'      => 'traffic_browser',
                //'label'     => 'Traffic Browsers',
                'class'     => 'col-lg-6 mt-6',
                'sortOrder' => $sortOrder,
                'type'      => 'default',// chart script (chart/[type].phtml
                'tab'       => 'chart',// chart distribution script (report/partial/[tab].phtml
                'chart'     => [
                    'chart'       => [
                        'type'                => 'pie',
                        'plotBackgroundColor' => null,
                        'plotBorderWidth'     => null,
                        'plotShadow'          => false,
                    ],
                    'legend'      => [
                        'layout' => 'vertical',
                        'align'  => 'right',
                        'x'      => -50,
                    ],
                    'plotOptions' => [
                        'pie' => [
                            'allowPointSelect' => true,
                            'cursor'           => 'pointer',
                            'dataLabels'       => [
                                'enabled'        => true,
                                'connectorColor' => 'silver',
                                'format'         => '<b>{point.name}</b>: {point.y:,0f}',
                            ],
                            'showInLegend'     => true
                        ]
                    ],
                    'title'       => [
                        'text' => 'Browsers'
                    ],
                ],
                'query'     => [
                    'targetClass' => $aggregation,
                ]
            ],
        ];
    }

    protected function trafficPlatform(int $sortOrder = 1): array
    {
        $aggregation = new Terms('traffic_platform');
        $aggregation = $aggregation->setField('device.platform.keyword');
        $aggregation = $aggregation->setSize(20);
        $sum = (new Sum('count'))->setField('publisher_device.count_requests');
        $aggregation->addAggregation($sum);
        return [
            'traffic_platform' => [
                'name'      => 'traffic_platform',
                'label'     => 'Platforms',
                'class'     => 'col-lg-6 mt-6',
                'sortOrder' => $sortOrder,
                'type'      => 'default',// chart script (chart/[type].phtml
                'tab'       => 'chart',// chart distribution script (report/partial/[tab].phtml
                'chart'     => [
                    'chart'       => [
                        'type'                => 'pie',
                        'plotBackgroundColor' => null,
                        'plotBorderWidth'     => null,
                        'plotShadow'          => false,
                    ],
                    'legend'      => [
                        'layout' => 'vertical',
                        'align'  => 'right',
                        'x'      => -50,
                    ],
                    'plotOptions' => [
                        'pie' => [
                            'allowPointSelect' => true,
                            'cursor'           => 'pointer',
                            'dataLabels'       => [
                                'enabled'        => true,
                                'connectorColor' => 'silver',
                                'format'         => '<b>{point.name}</b>: {point.y:,0f}',
                            ],
                            'showInLegend'     => true
                        ]
                    ],
                    'title'       => [
                        'text' => 'Platforms'
                    ],
                ],
                'query'     => [
                    'targetClass' => $aggregation,
                ]
            ],
        ];
    }

    protected function trafficCountry(int $sortOrder = 1): array
    {
        $aggregation = new Terms('traffic_country');
        $aggregation
            ->setField('country.name.keyword')
            ->setSize(50);
        $sum = (new Sum('count'))->setField('publisher_country.count_requests');
        $aggregation->addAggregation($sum);

        return [
            'traffic_country' => [
                'name'      => 'traffic_country',
                'label'     => 'Countries',
                'class'     => 'col-lg-12 mt-6',
                'sortOrder' => $sortOrder,
                'type'      => 'default',// chart script (chart/[type].phtml
                'tab'       => 'chart',// chart distribution script (report/partial/[tab].phtml
                'chart'     => [
                    'chart'  => [
                        'type' => 'column'
                    ],
                    /*'legend'      => [
                        'layout' => 'horizontal',
                        'align'  => 'center'
                    ],*/
                    'legend' => false,
                    'yAxis'  => [
                        'title' => 'Count bids',
                        'min'   => 0
                    ],
                    'xAxis'  => [
                        'type' => 'category'
                    ],

                    'title'  => [
                        'text' => 'Countries'
                    ],
                    'series' => [
                        0 => [
                            'dataLabels' => [
                                'enabled' => true,
                                'format'  => '{point.y:,0f}', // int
                            ]
                        ]
                    ]
                ],
                'query'     => [
                    'targetClass' => $aggregation,
                ]
            ],
        ];
    }
}
