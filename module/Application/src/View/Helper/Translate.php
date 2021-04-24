<?php

/**
 *
 * Translate.php
 * 
 * @since 01.06.2017
 * @author Alexej Kisselev <alexej.kisselev@gmail.com>
 */
namespace Application\View\Helper;

use Laminas\I18n\View\Helper\Translate as ZendTranslate;
use Laminas\I18n\Exception;

/**
 * View helper for translating messages.
 */
class Translate extends ZendTranslate {
    
   
    /**
     * Translate a message
     *
     * @param  string $message
     * @param  string $textDomain
     * @param  string $locale
     * @throws Exception\RuntimeException
     * @return string
     */
    public function __invoke($message, $textDomain = null, $locale = null) {
        $m = parent::__invoke($message);        
//        dump($message, $textDomain, $locale);//koshmar!
        return $m;
    }
}