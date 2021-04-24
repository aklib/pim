<?php

/**
 *
 * Translate.php
 *
 * @since 13.05.2017
 * @author Alexej Kisselev <alexej.kisselev@gmail.com>
 */
namespace Application\Controller\Plugin;

use Laminas\Mvc\Controller\Plugin\AbstractPlugin;

class Translate extends AbstractPlugin  {
    protected $translator;

    public function __invoke($key) {
        if(empty($key)){
            return '';
        }
        if(is_null($this->translator)){
            $this->translator = $this->getController()->getServiceManager()->get('ViewHelperManager')->get('translate');
        }
        $translate = $this->translator;
        return $translate($key);
    }
}
