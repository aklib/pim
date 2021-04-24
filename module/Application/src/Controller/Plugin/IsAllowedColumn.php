<?php

/**
 * IsAllowedColumn.php
 * 
 * @since 26.10.2018
 * @author Alexej Kisselev <alexej.kisselev@gmail.com>
 */

namespace Application\Controller\Plugin;

use Laminas\Mvc\Controller\Plugin\AbstractPlugin;

class IsAllowedColumn extends AbstractPlugin {
    protected $helper;
    
    public function __invoke(array $column) {        
        if($this->helper === null){
            $this->helper = $this->getController()->getServiceManager()->get('ViewHelperManager')->get('IsAllowedColumn');
        }
        $helper = $this->helper;
        return $helper($column);
    }
}
