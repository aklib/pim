<?php

/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Metronic;

return [
    'metronic'     => [
        'variant' => 'demo1'
    ],
    'view_manager' => [
        'template_map'        => [
            //  layout
            //'layout/layout' => __DIR__ . '/../view/layout/unlogged.phtml',
            'layout/default' => __DIR__ . '/../view/layout/layout.phtml',
            'layout/login'  => __DIR__ . '/../view/layout/login.phtml',

            'logo/aside'  => __DIR__ . '/../view/layout/default/logo/aside.phtml',
            'logo/header'  => __DIR__ . '/../view/layout/default/logo/header.phtml',
            'logo/login'  => __DIR__ . '/../view/layout/default/logo/login.phtml',
            'logo/favicon'  => __DIR__ . '/../view/layout/default/logo/favicon.phtml',

            //  layout partials
            'layout/header/base'        => __DIR__ . '/../view/layout/default/header/base.phtml',
            'layout/header/base-mobile' => __DIR__ . '/../view/layout/default/header/base-mobile.phtml',
            'layout/header/menu'        => __DIR__ . '/../view/layout/default/header/menu.phtml',
            'layout/header/brand'        => __DIR__ . '/../view/layout/default/header/brand.phtml',
            'layout/header/topbar'      => __DIR__ . '/../view/layout/default/header/topbar/base.phtml',
            'layout/header/topbar/user' => __DIR__ . '/../view/layout/default/header/topbar/user.phtml',
            'layout/subheader'          => __DIR__ . '/../view/layout/default/subheader/subheader-v1.phtml',
            'layout/aside'              => __DIR__ . '/../view/layout/default/aside/base.phtml',
            'layout/aside/brand'        => __DIR__ . '/../view/layout/default/aside/brand.phtml',
            'layout/aside/menu'         => __DIR__ . '/../view/layout/default/aside/menu.phtml',
            'layout/aside/test'         => __DIR__ . '/../view/layout/default/aside/test.phtml',
            'layout/content'            => __DIR__ . '/../view/layout/default/content/base.phtml',
            'layout/footer'             => __DIR__ . '/../view/layout/default/footer/base.phtml',

				'error/404'               => realpath(__DIR__ . '/../view/error/404.phtml'),
				'error/index'             => realpath(__DIR__ . '/../view/error/index.phtml'),
        ],
        'template_path_stack' => [
            __DIR__ . '/../view'
        ],
    ]
];
