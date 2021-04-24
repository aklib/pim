<?php /** @noinspection PhpUnusedParameterInspection */

/**
 * Class Aggregation
 * @package Elastic\View\Helper
 *
 * since: 31.10.2020
 * author: alexej@kisselev.de
 */

namespace Elastic\View\Helper;


use Application\ServiceManager\Interfaces\TranslatorAware;
use Application\View\Helper\AbstractHelperAware;
use Doctrine\ORM\EntityManager;
use Laminas\I18n\Translator\TranslatorInterface;
use Product\Entity\Product;

class ChartBuilder extends AbstractHelperAware implements TranslatorAware
{
    private ?TranslatorInterface $translator;
    private array $chart = [];

    /**
     * @param array|null $column
     * @return $this
     */
    public function __invoke(array $column = null): ChartBuilder
    {
        $this->chart = $column['chart'] ?? [];
        return $this;
    }


    /** @noinspection PhpUnused */
    public function createChart(array $aggregation, string $name = '', array &$series = null): self
    {
        $series = [];
        switch ($name) {
            case 'hourly_bid_clicked':
            case 'hourly_traffic':
                $series = $this->createSeriesHourlyTraffic($aggregation, $name);
                $this->setChartProperty('series', $series);
                return $this;
            case 'traffic_distribution':
                $series = $this->createTrafficDistribution($aggregation, $name);
                $this->setChartProperty('series', $series);
                return $this;
            case 'hourly_advertiser_error':
                $series = $this->createSeriesHourlyAdvertiserError($aggregation, $name);
                $this->setChartProperty('series', $series);
                return $this;
            case 'publisher_campaign':
                $series = $this->createPublisherCampaign($aggregation, $name);
                $this->setChartProperty('series', $series);
                return $this;
            case 'cardinalityHistogram':
                $series = $this->createCardinalityChart($aggregation, 'count unique IPs');
                $this->setChartProperty('series', $series);
                return $this;
        }
        switch ($this->chart['chart']['type']) {
            case 'pie':
            case 'column':
            default:
                $series = $this->createSingleAggregationChart($aggregation, 'count');
                $this->setChartProperty('series', $series);
                break;
        }

        //dump($aggregation);
        $this->setChartProperty('series', $series);
        return $this;
    }

    public function createPublisherCampaign(array $aggregation, string $name = ''): array
    {
        if (empty($aggregation['buckets'])) {
            return [];
        }
        $series = [];
        $series[0]['keys'] = ['from', 'to', 'weight'];
        $series[0]['name'] = 'publisher campaign bids';

        foreach ($aggregation['buckets'] as $publisherAgg) {
            $publisher = $publisherAgg['key']; // publisher name
            foreach ($publisherAgg['campaign']['buckets'] as $campaignAgg) {
                $campaignId = (int)$campaignAgg['key'];
                if (!empty($campaignAgg['name']['buckets'][0]['key'])) {
                    $campaign = $campaignAgg['name']['buckets'][0]['key'] . " #$campaignId";
                } else {
                    /** @var EntityManager $em */
                    $em = $this->getServiceManager()->get(EntityManager::class);
                    $product = $em->getRepository(Product::class)->find($campaignId);
                    if ($product !== null) {
                        $campaign = $product->getName() . " #$campaignId";
                    } else {
                        $campaign = "Campaign #$campaignId";
                    }
                }
                $series[0]['data'][] = [
                    $publisher,
                    $campaign,
                    (int)$campaignAgg['count']['value']
                ];
            }
        }
        return $series;
    }

    public function createCardinalityChart(array $aggregation, string $name = ''): array
    {
        if (empty($aggregation['buckets'])) {
            return [];
        }
        $series = [];
        $series[0]['name'] = $name;
        foreach ($aggregation['buckets'] as $agg) {
            if (array_key_exists('count', $agg)) {
                foreach ($agg['count']['buckets'] as $bucket) {
                    if ($bucket['key'] === 0) {
                        continue;
                    }
                    $series[0]['data'][] = [$agg['key'], $bucket['key']];
                }
            }
        }
        return $this->cleanSeries($series);
    }

    public function createTrafficDistribution(array $aggregation, string $name = '', array &$series = null): array
    {
        if ($series === null) {
            $series = [];
        }
        if (empty($aggregation['buckets'])) {
            return [];
        }
        $seriesData = [];
        $seriesCategory = [];
        foreach ($aggregation['buckets'] as $typeAgg) {
            $type = $typeAgg['key']; // category name

            foreach ($typeAgg['category']['buckets'] as $categoryAgg) {
                $category = $categoryAgg['key'];
                if ($type !== 'push') {
                    $seriesCategory[$type] = $type;
                    $seriesCategoryIndex = count($seriesCategory) - 1;
                    $serieKey = "$type $category";

                    $seriesData[$serieKey][$seriesCategoryIndex] = [
                        $serieKey,
                        $categoryAgg['count']['value']
                    ];
                } else {
                    foreach ($categoryAgg['quality']['buckets'] as $qualityAggs) {
                        $quality = $qualityAggs['key'];

                        $seriesCategory["$type $category"] = "$type $category";
                        $seriesCategoryIndex = count($seriesCategory) - 1;
                        $serieKey = "$type $quality";

                        //dump($categoryIndex, $key);
                        $seriesData[$serieKey][$seriesCategoryIndex] = [
                            "$type $category quality $quality",
                            $qualityAggs['count']['value']
                        ];

                    }
                }
            }
        }


        $categories = array_keys($seriesCategory);

        foreach ($seriesData as $key => $column) {
            foreach ($categories as $i => $category) {
                $series[$key]['name'] = $key;
                //dump($column);
                $series[$key]['data'][$i] = $column[$i] ?? [null, null];
            }
        }

        /*dump($seriesData, '----------------------',$series);
        die;*/
        $this->chart['xAxis']['categories'] = $categories;
        return array_values($series);
    }

    public function createSeriesHourlyTraffic(array $aggregation, string $name = '', array &$series = null): array
    {
        if ($series === null) {
            $series = [];
        }
        if (empty($aggregation['buckets'])) {
            return [];
        }
        foreach ($aggregation['buckets'] as $typeAgg) {
            $type = $typeAgg['key'];
            foreach ($typeAgg['category']['buckets'] as $categoryAgg) {
                $category = $categoryAgg['key'];
                if ($type !== 'push') {
                    $key = "$type-$category";
                    if (!array_key_exists($key, $series)) {
                        $series[$key] = [
                            'name' => "$type $category",
                            'data' => []
                        ];
                    }
                    foreach ($categoryAgg['histogram']['buckets'] as $trafficAgg) {
                        if (empty($trafficAgg['count']['value'])) {
                            continue;
                        }
                        $series[$key]['data'][] = [$trafficAgg['key'], $trafficAgg['count']['value']];
                    }
                } else {
                    foreach ($categoryAgg['quality']['buckets'] as $qualityAggs) {
                        $quality = $qualityAggs['key'];
                        $key = "$type-$category-$quality";
                        if (!array_key_exists($key, $series)) {
                            $series[$key] = [
                                'name' => "$type $category quality-$quality",
                                'data' => []
                            ];
                        }
                        foreach ($qualityAggs['histogram']['buckets'] as $trafficAgg) {
                            if (empty($trafficAgg['count']['value'])) {
                                continue;
                            }
                            $series[$key]['data'][] = [$trafficAgg['key'], $trafficAgg['count']['value']];
                        }
                    }
                }
            }
        }
        return $this->cleanSeries(array_values($series));
    }

    public function createSeriesHourlyAdvertiserError(array $aggregation, string $name = '', array &$series = null): array
    {
        if ($series === null) {
            $series = [];
        }
        if (empty($aggregation['buckets'])) {
            return [];
        }
        foreach ($aggregation['buckets'] as $advertiserAgg) {
            $advertiser = $advertiserAgg['key'];
            foreach ($advertiserAgg['error']['buckets'] as $errorAgg) {
                $error = $errorAgg['key'];
                $key = "$advertiser-$error";
                if (!array_key_exists($key, $series)) {
                    $series[$key] = [
                        'name' => "$advertiser: $error",
                        'data' => []
                    ];

                }
                foreach ($errorAgg['histogram']['buckets'] as $histogramAgg) {
                    if (empty($histogramAgg['count']['value'])) {
                        continue;
                    }
                    $series[$key]['data'][] = ['x' => $histogramAgg['key'], 'y' => $histogramAgg['count']['value']];
                }
            }
        }
        return $this->cleanSeries(array_values($series));
    }


    private function cleanSeries(array $series): array
    {
        $emptySeries = [];
        foreach ($series as $key => $serie) {
            if (empty($serie['data'])) {
                $emptySeries[] = $key;
            }
        }
        foreach ($emptySeries as $key) {
            unset($series[$key]);
        }
        return $series;
    }

    public function createSingleAggregationChart(array $aggregation, string $name = ''): array
    {
        if (empty($aggregation['buckets'])) {
            return [];
        }
        $series = [];
        $series[0]['name'] = $name;
        foreach ($aggregation['buckets'] as $agg) {
            $y = (int)array_key_exists('count', $agg) ? $agg['count']['value'] : $agg['key'];
            $series[0]['data'][] = ['name' => $agg['key'], 'y' => $y];
        }
        usort($series[0]['data'], static function ($a, $b) {
            return $a['y'] < $b['y'];
        });

        return $series;
    }

    public function setTranslator(TranslatorInterface $translator = null): void
    {
        $this->translator = $translator;
    }

    public function translate(string $text = null): string
    {
        if ($text === null || $this->translator === null) {
            return $text;
        }
        return $this->translator->translate($text);
    }

    /**
     * @return array
     */
    public function getChart(): array
    {
        return $this->chart;
    }

    /**
     * @param array $chart
     */
    public function setChart(array $chart): void
    {
        $this->chart = $chart;
    }

    /**
     * @param string $name
     * @param array $value
     */
    public function setChartProperty(string $name, array $value): void
    {
        if (array_key_exists($name, $this->chart)) {
            $this->chart[$name] = array_replace_recursive($this->chart[$name], $value);
        } else {
            $this->chart[$name] = $value;
        }

    }
}