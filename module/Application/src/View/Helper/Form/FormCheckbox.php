<?php

/**
 *
 * FormCheckbox.php
 * 
 * @since 26.05.2017
 * @author Alexej Kisselev <alexej.kisselev@gmail.com>
 */

namespace Application\View\Helper\Form;

use Laminas\Form\ElementInterface;
use Laminas\Form\View\Helper\FormCheckbox as LaminasFormCheckbox;

class FormCheckbox extends LaminasFormCheckbox {

    /**
     * Checkbox Helper
     * @param ElementInterface $element
     * @return string HTML
     */
    public function render(ElementInterface $element) {
        $html = parent::render($element);
        return $this->getView()
                        ->getHelperPluginManager()
                        ->getRenderer()
                        ->render('form/checkbox', ['element' => $element, 'html' => $html]);
    }

}
