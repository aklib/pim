<?php

/**
 *
 * HeadTitle.php
 *
 * @since 26.06.2017
 * @author Alexej Kisselev <alexej.kisselev@gmail.com>
 */
namespace Application\Controller\Plugin;

use Laminas\Mvc\Controller\Plugin\AbstractPlugin;

class HeadTitle extends AbstractPlugin {
    protected  $helper;

    public function __invoke() {
        if(is_null($this->helper)){
            $this->helper = $this->getController()->getServiceManager()->get('ViewHelperManager')->get('headTitle');
        }
        return $this->helper;
    }
}
