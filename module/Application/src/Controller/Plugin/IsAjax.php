<?php
    /**
     * Class IsAjax
     * @package Application\Controller\Plugin * since: 18.07.2020
     * author: alexej@kisselev.de
     */

    namespace Application\Controller\Plugin;

    use Laminas\Mvc\Controller\Plugin\AbstractPlugin;

    class IsAjax extends AbstractPlugin
    {
        public function __invoke() {
           return $this->getController()->getServiceManager()->get('Request')->isXmlHttpRequest();
        }
    }