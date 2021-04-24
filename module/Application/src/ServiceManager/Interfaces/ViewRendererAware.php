<?php
    /**
     * Interface ViewRendererAware
     * @package Application\ServiceManager\Interfaces * since: 12.07.2020
     * author: alexej@kisselev.de
     */

    namespace Application\ServiceManager\Interfaces;


    use Laminas\View\Renderer\PhpRenderer;

    interface ViewRendererAware
    {
        public function getViewRenderer(): PhpRenderer;
        public function setViewRenderer(PhpRenderer $viewRenderer): void;
    }