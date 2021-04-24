<?php /** @noinspection ContractViolationInspection */

namespace Elastic\Decorator;

use Application\Decorator\AbstractEntityDecorator;
use Elastic\View\AggregationRow;
use FlorianWolters\Component\Core\StringUtils;


/**
 * @method AggregationRow getObject()
 */
class AggregationRowDecorator extends AbstractEntityDecorator
{


    private function get($getter, $args)
    {

        $key = strtolower(preg_replace('/^get/', '', $getter));
        $agg = $this->getObject()->getAggregation();
        if (!array_key_exists($key, $agg)) {
            return '';
        }
        if (!is_array($agg[$key])) {
            return $agg[$key];
        }
        if (array_key_exists('value', $agg[$key])) {
            return $this->formatOutput($agg[$key]['value'], $args);
        }
        if (array_key_exists('count', $agg[$key])) {
            // filtered
            if (empty($agg[$key]['count']['value'])) {
                return '';
            }
            return $this->formatOutput($agg[$key]['count']['value'], $args);
        }
        if (array_key_exists('doc_count', $agg[$key])) {
            return $this->formatOutput($agg[$key]['doc_count'], $args);
        }


        return '';
    }

    private function formatOutput($val, array $args): string
    {
        if (!is_numeric($val)) {
            return $val;
        }

        switch ($args[0]['type'] ?? '') {
            case 'integer':
                return number_format((int)$val, 0, ',', '.');
            case 'float':
                return number_format($val, 5, ',', '.');
        }

        if (!is_numeric($val) && empty($val)) {
            return '';
        }
        $decimals = 0;
        if (is_float($val)) {
            $decimals = 5;
        }
        return number_format($val, $decimals, ',', '.');
    }


    public function __call($method, $args)
    {
        if (StringUtils::startsWith($method, 'get')) {
            return $this->get($method, $args);
        }
        return '';
    }
}
