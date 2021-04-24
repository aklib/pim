<?php

/**
 * 
 * IsAjax.php
 * 
 * @since 19.12.2019
 * @author Alexej Kisselev <alexej.kisselev@gmail.com>
 */

namespace Application\View\Helper;

class IsAjax extends AbstractHelperAware {

    protected $request;

    public function __invoke() {
        if (empty($this->request)) {
            $this->request = $this->getServiceManager()->get('Request');
        }
        return $this->request->isXmlHttpRequest();
    }

}
