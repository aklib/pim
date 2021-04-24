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

class History extends AbstractPlugin {
    protected $helper;

    public function __invoke() {
        if(is_null($this->helper)){
            $this->helper = $this->getController()->getServiceManager()->get('ViewHelperManager')->get('history');
        }
        return $this->helper;
    }
}

