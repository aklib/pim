<?php

/**
 *
 * FormRow.php
 * 
 * @since 25.05.2017
 * @author Alexej Kisselev <alexej.kisselev@gmail.com>
 */
namespace Application\View\Helper\Form;


use Laminas\Form\ElementInterface;
use Laminas\Form\View\Helper\FormRow as LaminasFormRow;

class FormRow extends LaminasFormRow {
   public function render(ElementInterface $element, $labelPosition = null){        
       return $this->getView()->getHelperPluginManager()->getRenderer()->render('form/row',['element' => $element, 'labelPosition' => $labelPosition]);
   }
}