<?php

/**
 *
 * Urlx.php
 * 
 * @since 01.05.2017
 * @author Alexej Kisselev <alexej.kisselev@gmail.com>
 */
namespace Application\View\Helper;

use Laminas\Http\Request;
use Laminas\View\Helper\AbstractHelper;
use Laminas\Mvc\MvcEvent;

class Urlx extends AbstractHelper {

    protected $request;
    protected $event;
    protected $routeParams;
    
    public function __construct(Request $request, MvcEvent $event) {
        $this->request = $request;
        $this->event = $event;
        $params = (array)$this->event->getRouteMatch()->getParams();
        $params['controller'] = $params['controller'];
        $this->routeParams = $params;          
    }

    public function __invoke($opt = []) {
        $queryArray = (array)$this->request->getQuery();//array_merge_recursive((array)$this->request->getQuery(),(array)$options);
        if(!empty($opt)){
            $queryArray = array_replace_recursive($queryArray, $opt);
        }       
        $options = ['query'=>$queryArray];
        
        //  don't override original params
        $routeParams = $this->routeParams;
        foreach ($routeParams as $key => $value) {
            if(array_key_exists($key, $options)){
                $routeParams[$key] = $options[$key];
                unset($options[$key]);
            }
        }
        
        if(is_array($options['query'])){
            foreach($options['query'] as $key=>$option){ 
                if(array_key_exists($key, $routeParams)){                    
                    $routeParams[$key] = $options['query'][$key];
                    unset($options['query'][$key]);
                }
            } 
        }

        return $this->view->url(
                $this->event->getRouteMatch()->getMatchedRouteName(), 
                $routeParams, 
                $options, false);
    }
}
