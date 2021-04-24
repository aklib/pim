<?php

/**
 *
 * Acl.php
 *
 * @since 19.06.2017
 * @author Alexej Kisselev <alexej.kisselev@gmail.com>
 */
namespace Application\Controller\Plugin;

use Laminas\Mvc\Controller\Plugin\AbstractPlugin;

class Acl extends AbstractPlugin {
    protected $helper;

    public function __invoke() {
        if(is_null($this->helper)){
            $this->helper = $this->getController()->getServiceManager()->get('ViewHelperManager')->get('acl');
        }
        $helper = $this->helper;
        return $this->helper->__invoke();
    }
}
