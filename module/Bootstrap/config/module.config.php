<?php

/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Bootstrap;

return [
    'bootstrap'     => [
        'version' => '4.5.0'
    ],
    'view_manager' => [
        'template_map'        => [
            //  navigation
            'navigation/default'     => __DIR__ . '/../view/navigation/default.phtml',
            'navigation/item'        => __DIR__ . '/../view/navigation/item.phtml',
            'navigation/breadcrumbs' => __DIR__ . '/../view/navigation/partial/breadcrumbs.phtml',
            'navigation/button-block' => __DIR__ . '/../view/navigation/partial/button-block.phtml',
            'navigation/user' => __DIR__ . '/../view/navigation/partial/user-header.phtml',
            'messenger/flash' => __DIR__ . '/../view/messenger/flash.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view'
        ],
    ]
];
