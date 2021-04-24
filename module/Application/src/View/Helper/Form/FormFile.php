<?php
    /**
     * Class FormFile
     * @package Application\View\Helper\Form
     *
     * since: 13.09.2020
     * author: alexej@kisselev.de
     */

    namespace Application\View\Helper\Form;

    use Laminas\Form\ElementInterface;
    use Laminas\Form\View\Helper\FormFile as LaminasFormFile;

    class FormFile extends LaminasFormFile
    {
        private string $template = 'form/file';

        /**
         * @return string
         */
        public function getTemplate(): string
        {
            return $this->template;
        }

        /**
         * @param string $template
         */
        public function setTemplate(string $template): void
        {
            $this->template = $template;
        }

        public function render(ElementInterface $element): string
        {
            $html = parent::render($element);
            return $this->getView()
                ->getHelperPluginManager()
                ->getRenderer()
                ->render($this->template, ['element' => $element, 'html' => $html]);
        }
    }