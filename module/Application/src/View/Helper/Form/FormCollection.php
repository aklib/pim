<?php

/**
 *
 * FormCollection.php
 * 
 * @since 26.05.2017
 * @author Alexej Kisselev <alexej.kisselev@gmail.com>
 */
namespace Application\View\Helper\Form;


use Laminas\Form\ElementInterface;
use Laminas\Form\View\Helper\FormCollection as ZendFormCollection;

class FormCollection extends ZendFormCollection
{

    private string $template = 'form/collection';

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

    /**
     *
     * @param ElementInterface $element
     * @return string HTML
     */
    public function render(ElementInterface $element): string
    {
        $html = parent::render($element);
        return $this->getView()
            ->getHelperPluginManager()
            ->getRenderer()
            ->render($this->template, ['element' => $element, 'html' => $html]);
    }
}