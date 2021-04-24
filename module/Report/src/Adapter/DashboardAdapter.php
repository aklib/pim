<?php /** @noinspection ContractViolationInspection */

namespace Report\Adapter;

use Report\Entity\Advertiser;
use Report\Entity\Bid;
use Report\Entity\Publisher;

class DashboardAdapter extends AbstractQueryAdapter
{
    public function getEntityName(): string
    {
        switch ($this->getActionName()) {
            case 'list':
                return Bid::class;
            case 'advertiser':
                return Advertiser::class;
            case 'publisher':
                return Publisher::class;
        }

        return '';
    }

    public function getLayoutSpecifications(): array
    {
        return [];
    }

    public function getContentSpecifications(): array
    {
        $content = [
            'top' => 'report/partial/top-filter'
        ];
        switch ($this->getActionName()) {
            case 'list':
            case 'advertiser':
            case 'publisher':
                $content['data'] = 'report/dashboard/data';
                break;
            default:
                $content['data'] = 'report/report/data';
        }

        return $content;
    }

    public function getColumnsSpecifications(): array
    {
        switch ($this->getActionName()) {
            case 'list':
                return $this->getAdminColumns();
            case 'advertiser':
                return $this->getAdvertiserColumns();
            case 'publisher':
                return $this->getPublisherColumns();
        }

        return parent::getColumnsSpecifications();
    }

    public function getAdvertiserColumns(): array
    {
        return
            array_replace_recursive(
                $this->bidCount(),
                //$this->cardinalityIp(2),
                $this->bidWon(3),
                $this->bidClicked(4),
                $this->advertiserCost(5),
            );
    }

    public function getPublisherColumns(): array
    {
        return
            array_replace_recursive(
                $this->totalHits(),
                //$this->cardinalityIp(2),
                $this->bidCount(3),
                //$this->publisherSell(4),
                $this->bidClicked(4),
                $this->publisherRevenue(5),
                $this->hourlyTraffic(9),
            /* $this->processingTimeMin(6),
             $this->processingTimeMax(7),
             $this->processingTimeAvg(8),

             $this->trafficDistribution(10),
             $this->trafficBrowser(11),
             $this->trafficPlatform(12),
             $this->trafficCountry(14)*/
            );
    }

    public function getAdminColumns(): array
    {
        return
            array_replace_recursive(
                $this->totalHits(),
                $this->totalHitsImport(2),
                //$this->cardinalityIp(2),
                $this->bidCount(3),
                $this->bidWon(4),

                $this->bidClicked(5),
                $this->bidClickedImport(5),

                $this->adminCost(6),
                $this->adminRevenue(7),
                $this->adminProfit(8),
                $this->requestStatus(9, 460),
                $this->requestStatus(9, 461),
                $this->requestStatus(9, 462),
                /*$this->error4(10),*/

                $this->hourlyTraffic(9),
                $this->hourlyBidClicked(10),
                //$this->hourlyAdvertiserError(10),

                $this->campaignTopTable(8),
                $this->campaignTopTableImport(8),

                $this->publisherCampaign(14),
                $this->trafficDistribution(11),
                $this->cardinalityIPHistogram(12),

                $this->trafficBrowser(16),
                $this->trafficPlatform(17),
                $this->trafficCountry(18)
            /* $is->processingTimeMin(9),
             $this->processingTimeMax(10),
             $this->processingTimeAvg(11),
             $this->hourlyTraffic(12),

             $this->hourlyBidWon(15),
             $this->trafficBrowser(16),
             $this->trafficPlatform(17),
             $this->trafficCountry(18)*/
            );
    }


    public function isColumnVisible(string $columnName): bool
    {
        if ($this->getControllerAlias() === 'dashboard') {
            return $columnName === 'totalHits';
        }

        return parent::isColumnVisible($columnName);
    }
}
