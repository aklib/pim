<?php

/**
 *
 * Identity.php
 *
 * @since 26.05.2017
 * @author Alexej Kisselev <alexej.kisselev@gmail.com>
 */
namespace Application\Controller\Plugin;

use Laminas\Mvc\Controller\Plugin\AbstractPlugin;
use Application\View\Helper\Identity as IdentityHelper;

class Identity extends AbstractPlugin {
    protected IdentityHelper $helper;
    private bool $initialised = false;

    public function __invoke() {
        if(!$this->initialised){
            $this->helper = $this->getController()->getServiceManager()->get('ViewHelperManager')->get('identity');
        }
        $this->initialised = true;
        return $this->helper->__invoke();
    }
}
