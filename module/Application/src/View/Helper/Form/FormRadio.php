<?php

/**
 *
 * FormRadio.php
 * 
 * @since 27.06.2017
 * @author Alexej Kisselev <alexej.kisselev@gmail.com>
 */
namespace Application\View\Helper\Form;


use Laminas\Form\ElementInterface;
use Laminas\Form\View\Helper\FormRadio as ZendFormRadio;
use Laminas\Form\Element\MultiCheckbox as MultiCheckboxElement;

class FormRadio extends ZendFormRadio {
    
    /**
     * Checkbox Helper
     * @param ElementInterface $element
     * @return string HTML
     */
    protected function renderOptions(MultiCheckboxElement $element, array $options, array $selectedOptions, array $attributes){
        $html = parent::renderOptions($element, (array)$options, (array)$selectedOptions, (array)$attributes);
        return str_replace('</label>', '<span></span></label>', $html);
    }
    
}