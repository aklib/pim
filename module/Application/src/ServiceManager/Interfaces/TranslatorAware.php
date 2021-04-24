<?php

    /**
     * TranslatorAware.php
     *
     * @since 05.05.2018
     * @author Alexej Kisselev <alexej.kisselev@gmail.com>
     */

    namespace Application\ServiceManager\Interfaces;

    use Laminas\I18n\Translator\TranslatorInterface;

    interface TranslatorAware
    {
        public function setTranslator(TranslatorInterface $translator = null): void;
    }
