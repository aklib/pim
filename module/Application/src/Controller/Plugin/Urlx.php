<?php

/**
 *
 * Urlx.php
 *
 * @since 21.05.2017
 * @author Alexej Kisselev <alexej.kisselev@gmail.com>
 */
namespace Application\Controller\Plugin;

use Laminas\Mvc\Controller\Plugin\AbstractPlugin;

class Urlx extends AbstractPlugin  {
    protected $urlx;

    public function __invoke($options = []) {
        if(is_null($this->urlx)){
            $this->urlx = $this->getController()->getServiceManager()->get('ViewHelperManager')->get('urlx');
        }
        $urlx = $this->urlx;
        return $urlx($options);
    }
}
