<?php

/**
 *
 * Params.php
 *
 * @since 19.12.2019
 * @author Alexej Kisselev <alexej.kisselev@gmail.com>
 */

namespace Application\View\Helper;

use Laminas\Mvc\MvcEvent;
use Laminas\Stdlib\RequestInterface;
use Laminas\View\Helper\AbstractHelper;

class Params extends AbstractHelper
{
    protected RequestInterface $request;
    protected MvcEvent $event;
    protected array $params = [];

    public function __construct(RequestInterface $request, MvcEvent $event)
    {
        $this->request = $request;
        $this->event = $event;
    }

    public function __invoke($name = null, $default = null)
    {
        if (empty($this->params)) {
            $routeMatch = $this->event->getRouteMatch();

            $this->params = array_replace_recursive(
                (array)$this->request->getQuery(),
                (array)$this->request->getPost(),
            );
            if ($routeMatch !== null) {
                $this->params = array_replace_recursive(
                    $this->params,
                    $routeMatch->getParams()
                );

                $this->params['route'] = $this->event->getRouteMatch()->getMatchedRouteName();
            }
        }
        if ($name === null) {
            return $this->params;
        }
        return empty($this->params[$name]) ? $default : $this->params[$name];
    }
}
