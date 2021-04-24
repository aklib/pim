<?php

/**
 * 
 * ServiceManager.php
 * 
 * @since 19.12.2019
 * @author Alexej Kisselev <alexej.kisselev@gmail.com>
 */

namespace Application\View\Helper;

use Psr\Container\ContainerInterface;

class ServiceManager extends AbstractHelperAware {

    /**
     * 
     * @return ContainerInterface
     */
    public function __invoke() {
        return $this->getServiceManager();
    }

}
