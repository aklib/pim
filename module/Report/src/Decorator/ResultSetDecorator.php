<?php
/**
 * Class ResultSetDecorator
 * @package Report\Decorator
 *
 * since: 29.09.2020
 * author: alexej@kisselev.de
 */

namespace Report\Decorator;

use Application\Decorator\AbstractAwareDecorator;
use Elastica\ResultSet;
use Exception;

/**
 * @method ResultSet getObject()
 */
class ResultSetDecorator extends AbstractAwareDecorator
{
    public function getAggregationCount(string $name): string
    {
        $decimals = 0;
        switch ($name) {
            case 'cardinalityIp':
            case 'bidCount':
            case 'bidWon':
            case 'bidClicked':

                break;
            case 'adminCosts':
            case 'adminRevenue':
            case 'profit':
            case 'revenue':
            case 'publisherRevenue':
                $decimals = 5;
                break;
            case 'processing_time_min':
            case 'processing_time_max':
            case 'processing_time_avg':
                $decimals = 4;
                break;
        }


        $val = 0;
        try {
            $agg = $this->getObject()->getAggregation($name);
            if (isset($agg['doc_count'])) {
                $val = (int)$agg['doc_count'];
            } else {
                $val = round($agg['value'], $decimals);
            }
        } catch (Exception $e) {
            return 'n/a';
        }
        return $this->getObject()->getResponse()->isOk() ? number_format($val, $decimals, ',', '.') : 'n/a';
    }

    public function getFilterCount(string $name): string
    {
        $decimals = 0;
        try {
            $agg = $this->getObject()->getAggregation($name);
            if (isset($agg['count'])) {
                $val = round($agg['count']['value'], 0);
            } elseif (isset($agg['doc_count'])) {
                $val = (int)$agg['doc_count'];
            } else {
                $val = round($agg['value'], $decimals);
            }
        } catch (Exception $e) {
            return 'n/a';
        }
        return $this->getObject()->getResponse()->isOk() ? number_format($val, $decimals, ',', '.') : 'n/a';
    }


    /* public function getTotalHits()
     {
         return number_format($this->getObject()->getTotalHits(), 0, '', '.');
     }*/

}