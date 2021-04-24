<?php
/**
 * Class AggregationRow
 * @package Elastic\View
 *
 * since: 17.02.2021
 * author: alexej@kisselev.de
 */

namespace Elastic\View;


class AggregationRow
{
    private string $key;
    private string $keyAsString;
    private int $timestamp;
    private int $docCount;
    private array $aggregation;

    /**
     * AggregationRow constructor.
     * @param array $aggregation
     */
    public function __construct(array $aggregation)
    {
        $this->setAggregation($aggregation);
    }


    public function setAggregation(array $aggregation): void
    {
        /*$this->key = $aggregation['key'];
        $this->keyAsString = array_key_exists('key_as_string', $aggregation) ? $aggregation['key_as_string'] : '';
        $this->timestamp = array_key_exists('key_as_string', $aggregation) ? $aggregation['key'] : 0;
        $this->docCount = (int)$aggregation['doc_count'];
        unset($aggregation['key'],$aggregation['key_as_string'], $aggregation['doc_count']);*/
        $this->aggregation = array_change_key_case($aggregation, CASE_LOWER);
    }

    /**
     * @return array
     */
    public function getAggregation(): array
    {
        return $this->aggregation;
    }
}