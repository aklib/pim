<?php
    /**
     * Class QueryBuilder
     * @package Elastic\ODM
     *
     * since: 27.09.2020
     * author: alexej@kisselev.de
     */

    namespace Elastic\ODM\Query;

    use Elastic\Factory\Client;
    use Elastica\Aggregation\AbstractAggregation;
    use Elastica\Exception\Connection\HttpException;
    use Elastica\Exception\ResponseException;
    use Elastica\Query\AbstractQuery;
    use Elastica\Response;
    use Elastica\ResultSet;
    use Elastica\Search;
    use Exception;
    use RuntimeException;

    class QueryBuilder extends AbstractEntityQueryBuilder
    {
        public function applyColumns(array $columns): void
        {
            $query = $this->getQuery();
            /** @var AbstractAggregation $lastAggregation */
            $lastAggregation = null;
            foreach ($columns as $column) {
                $queryConf = $column['query'] ?? null;
                if ($queryConf === null) {
                    continue;
                }
                $queryOrAgg = $queryConf['targetClass'];
                if (is_string($queryOrAgg)) {
                    $queryOrAgg = new $queryOrAgg($column['name']);
                }
                $type = $queryConf['type'] ?? '';
                if ($queryOrAgg instanceof AbstractQuery) {
                    switch ($type) {
                        case 'mustNot':
                            $this->getQueryBool()->addMustNot($queryOrAgg);
                            break;
                        case 'should':
                            $this->getQueryBool()->addShould($queryOrAgg);
                            break;
                        default:
                            $this->getQueryBool()->addMust($queryOrAgg);
                    }
                } elseif ($queryOrAgg instanceof AbstractAggregation) {
                    if ($type === 'nested') {
                        $lastAggregation = $lastAggregation instanceof AbstractAggregation ? $lastAggregation->addAggregation($queryOrAgg) : $query->addAggregation($queryOrAgg);
                    } else {
                        $query->addAggregation($queryOrAgg);
                    }
                }
            }
        }

        /**
         * @return ResultSet
         */
        public function getResult(): ResultSet
        {
            $client = $this->getServiceManager()->get(Client::class);
            $search = new Search($client);
            try {
                $indices = $search->getIndices();
                if (empty($indices)) {
                    $search->addIndex($this->indexDefault);
                }
                $resultSet = $search->search($this->query);
            } catch (ResponseException $e) {
                // log an exception and move on
                $resultSet = new ResultSet($e->getResponse(), $this->query, []);

            } catch (HttpException $e) {
                // log an exception and move on
                $resultSet = new ResultSet($e->getResponse(), $this->query, []);
            } catch (Exception $e) {
                $resultSet = $this->getErrorResult($e->getMessage());
            }
            return $resultSet;
        }

        public function getErrorResult(string $message): ResultSet
        {
            $response = [
                'errors' => [
                    'message' => $message
                ]
            ];
            // log an exception and move on 400 bad request
            return new ResultSet(new Response($response, 400), $this->query, []);
        }

    }