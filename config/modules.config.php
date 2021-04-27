<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-skeleton for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-skeleton/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-skeleton/blob/master/LICENSE.md New BSD License
 */

/**
 * List of enabled modules for this application.
 *
 * This should be an array of module namespaces used in the application.
 */
    defined('APPLICATION_PATH') || define('APPLICATION_PATH', __DIR__ . '../');
return [
    'Laminas\Form',
    'Laminas\Navigation',
    'Laminas\Paginator',
//    'Laminas\Di',
    'Laminas\Log',
    'Laminas\Mvc\Plugin\FilePrg',
    'Laminas\Mvc\Plugin\FlashMessenger',
    'Laminas\Mvc\Plugin\Identity',
    'Laminas\Mvc\Plugin\Prg',
    'Laminas\Session',
    'Laminas\Mvc\I18n',
    'Laminas\Mvc\Console',
    'Laminas\Hydrator',
    'Laminas\InputFilter',
    'Laminas\Filter',
    'Laminas\I18n',
    'Laminas\Cache',
    'Laminas\Router',
    'Laminas\Validator',
    'Laminas\Diactoros',
    'Laminas\Mail',
    'DoctrineModule',
    'DoctrineORMModule',
    'Metronic',
    'Bootstrap',
    'Application',
    'User',
    'Category',
//    'Attribute',
//    'Acl',

    /*'Product',
    'Elastic',
    'Email',*/
];
