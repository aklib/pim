<?php
/**
 * Class TableBuilder
 * @package Elastic\View\Helper
 *
 * since: 31.01.2021
 * author: alexej@kisselev.de
 */

namespace Elastic\View\Helper;


use Application\ServiceManager\Interfaces\TranslatorAware;
use Application\View\Helper\AbstractHelperAware;
use Laminas\I18n\Translator\TranslatorInterface;

class TableBuilder extends AbstractHelperAware implements TranslatorAware
{
    private ?TranslatorInterface $translator;
    private array $table = [];

    /**
     * @param array|null $column
     * @return $this
     */
    public function __invoke(array $column = null): self
    {
        $this->table = $column['table'] ?? [];
        return $this;
    }

    /** @noinspection PhpUnused */
    public function createTable(array $aggregation, string $name = '', array &$series = null): self
    {
        $data = [];
        switch ($name) {
            case 'campaign_top':
            case 'campaignTopTableImport':
                $data = $this->createCampaignTopData($aggregation, $name);
        }

        $this->table['columns'] = $data['columns'] ?? [];
        $this->table['data'] = $data['data'] ?? [];
        return $this;
    }

    public function createCampaignTopData(array $aggregation, string $name = ''): array
    {
        if (empty($aggregation['buckets'])) {
            return [];
        }

        $data = [];
        $data['columns'] = [
            'id'   => [
                'name'      => 'id',
                'label'     => 'ID',
                'sortOrder' => 1
            ],
            'name' => [
                'name'      => 'name',
                'label'     => 'Name',
                'sortOrder' => 2
            ],
            /* 'status_200' => [
                 'name'      => 'status_200',
                 'label'     => 'Bid Returned<br>#200',
                 'sortOrder' => 100
             ],
             'status_204' => [
                 'name'      => 'status_204',
                 'label'     => 'No Content<br>#204',
                 'sortOrder' => 102
             ],
             'status_206' => [
                 'name'      => 'status_206',
                 'label'     => 'Capping<br>#206',
                 'sortOrder' => 104
             ],
             'status_207' => [
                 'name'      => 'status_207',
                 'label'     => 'Low Bid<br>#207',
                 'sortOrder' => 106
             ],
             'status_408' => [
                 'name'      => 'status_408',
                 'label'     => 'Request Timeout<br>#408',
                 'sortOrder' => 108
             ],
             'status_400' => [
                 'name'      => 'status_400',
                 'label'     => 'Advertiser request Error<br>#400',
                 'sortOrder' => 110
             ],
             'status_500' => [
                 'name'      => 'status_500',
                 'label'     => 'Internal Server Error<br>#500',
                 'sortOrder' => 112
             ],*/
        ];
        $data['data'] = [];

        foreach ($aggregation['buckets'] as $i => $campaignAgg) {
            $campaign = $campaignAgg['key']; // publisher name
            $data['data'][$i]['id'] = $campaign;
            $data['data'][$i]['name'] = $campaignAgg['name']['buckets'][0]['key'] ?? 'unknown';
            $data['data'][$i]['count'] = $campaignAgg['count']['value'];
            $count = 5;
            foreach ($campaignAgg as $key => $agg) {
                if (is_array($agg) && array_key_exists('value', $agg)) {

                    if (!array_key_exists($key, $data['columns'])) {
                        $data['columns'][$key] = [
                            'name'      => $key,
                            'label'     => $this->translate("admin#$key"),
                            'sortOrder' => $this->getSortOrder($key)
                        ];
                    }
                    $data['data'][$i][$key] = $this->formatValue($agg['value']);
                }
            }

            if (array_key_exists('status', $campaignAgg)) {
                foreach ($campaignAgg['status']['buckets'] as $statusAgg) {
                    $statusCode = (int)$statusAgg['key'];
                    $colName = 'status_' . $statusCode;
                    if (!array_key_exists($colName, $data['columns'])) {
                        $data['columns'][$colName] = [
                            'name'  => $colName,
                            'label' => $colName,
                            'sortOrder' => 120
                        ];
                    }
                    $data['data'][$i][$colName] = $statusAgg['count']['value'];
                }
            }
        }
        uasort($data['columns'], static function ($a, $b) {
            return (int)$a['sortOrder'] > (int)$b['sortOrder'];
        });
        return $data;
    }


    private function getSortOrder(string $colame): int
    {
        switch ($colame) {
            case 'count':
                return 2;

            case 'totalHitsImport':
                return 12;
            case 'bidCount':
                return 14;
            case 'bidWon':
                return 18;
            case 'bidClicked':
                return 20;
            case 'bidClickedGrossImport':
                return 22;
            case 'bidClickedImport':
                return 24;

            case 'adminRevenue':
                return 30;


            case 'profit':
                return 80;



            case 'profit':
                return 82;

            case 'adminCosts':
                return 60;
            default:
                echo "$colame<br>";
        }
        return 10;
    }

    /*



*/


    /**
     * @param int|float|string|null $val
     * @return string
     */
    private function formatValue($val = null): string
    {
        if (empty($val)) {
            return '';
        } elseif (is_float($val)) {
            /** @noinspection TypeUnsafeComparisonInspection */
            if (((int)$val) == $val) {
                return number_format($val, 0, ',', '.');
            } else {
                return number_format($val, 5, ',', '.');
            }
        } elseif (is_numeric($val)) {
            return number_format($val, 0, ',', '.');
        }
        return $val;
    }


    /**
     * @return array
     */
    public function getTable(): array
    {
        return $this->table;
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
}