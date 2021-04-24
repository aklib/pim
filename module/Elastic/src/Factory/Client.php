<?php
    /**
     * Class Client
     * @package Elastic\Client
     *
     * since: 22.09.2020
     * author: alexej@kisselev.de
     */

    namespace Elastic\Factory;

    use Elastica\Client as ElasticaClient;
    use Elastica\Request as ElasticaRequest;
    use Elastica\Response;
    use Laminas\DeveloperTools\Collector\AutoHideInterface;
    use Laminas\DeveloperTools\Collector\CollectorInterface;
    use Laminas\Mvc\MvcEvent;
    use Serializable;
    use function serialize as phpserialize;
    use function unserialize as phpunserialize;

    class Client extends ElasticaClient implements CollectorInterface, AutoHideInterface, Serializable
    {
        private string $name;
        private array $data = [];

        public function __construct($config = [], $callback = null)
        {
            parent::__construct($config, $callback);
            $this->name = 'elastic.toolbar';
        }

        public function getData(): array
        {
            return $this->data;
        }

        protected function collectSingleRequest(): void
        {
            if ($this->_lastRequest instanceof ElasticaRequest) {
                $data = $this->_lastRequest->getData();
                if (empty($data)) {
                    return;
                }
                $index = count($this->data);
                $this->data[$index]['name'] = $this->name;
                $this->data[$index]['request'] = $this->_lastRequest;
                $this->data[$index]['response'] = $this->_lastResponse;
            }
        }


        public function request(string $path, string $method = ElasticaRequest::GET, $data = [], array $query = [], string $contentType = ElasticaRequest::DEFAULT_CONTENT_TYPE): Response
        {
            $response = parent::request($path, $method, $data, $query, $contentType);
            $this->collectSingleRequest();
            return $response;
        }


        public function collect(MvcEvent $mvcEvent): void
        {
            // ignore call from developer toolbar. See above method 'request'
        }

        public function getProfilerRequest($i = 0)
        {
            return $this->data[$i]['request'];
        }

        public function getProfilerResult($i = 0)
        {
            return $this->data[$i]['response'];
        }

//==== INTERFACE IMPLEMENTATIONS

        /**
         * @return bool
         */
        public function canHide():bool
        {
            return empty($this->data);
        }

        /**
         * @return string
         */
        public function getName(): string
        {
            return $this->name;
        }

        /**
         * @return int
         */
        public function getPriority(): int
        {
            return 10;
        }

        /**
         * @return string
         */
        public function serialize()
        {
            return phpserialize($this->data);
        }

        /**
         * @param string $serialized
         */
        public function unserialize($serialized)
        {
            $this->data = phpunserialize($serialized);
            $this->name = $this->data['name'];
        }
    }