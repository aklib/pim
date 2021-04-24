<?php

/**
 *
 * Decorate.php
 *
 * @since 25.05.2017
 * @author Alexej Kisselev <alexej.kisselev@gmail.com>
 */
namespace Application\Controller\Plugin;

use Laminas\Mvc\Controller\Plugin\AbstractPlugin;

class Decorate extends AbstractPlugin {
    protected $helper;

    public function __invoke($item, $method, $args = []) {
        if(is_null($this->helper)){
            $this->helper = $this->getController()->getServiceManager()->get('ViewHelperManager')->get('decorate');
        }
        $decorate = $this->helper;
        return $decorate($item, $method, $args);
    }
}
