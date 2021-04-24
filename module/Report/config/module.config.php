<?php

namespace Report;

use Application\ServiceManager\InvokableAwareFactory;
use Elastica\ResultSet;
use Laminas\Router\Http\Segment;
use Report\Adapter\DashboardAdapter;
use Report\Decorator\ResultSetDecorator;

return [
    'router'          => [
        'routes' => [
            'default' => [
                'type'          => Segment::class,
                'options'       => [
                    'defaults' => [
                        'controller' => 'dashboard',
                        'action'     => 'list',
                    ],
                ],
                'may_terminate' => true,
                'child_routes'  => [
                    'report'    => [
                        'type'    => Segment::class,
                        'options' => [
                            'route'    => '/report/[:controller][/:action][/:id][/]',
                            'defaults' => [
                                'action' => 'list',
                                'id'     => '',
                            ],
                        ],
                    ],
                    'dashboard' => [
                        'type'    => Segment::class,
                        'options' => [
                            'route'    => '/company[/:action][/]',
                            'defaults' => [
                                'controller' => 'dashboard',
                                'id'         => '',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'navigation'      => [
        'default' => [
            'dashboard' => [
                'label'    => 'Dashboard',
                'subtitle' => 'dashboard & statistics',
                'route'    => 'default',
                'icon'     => 'fa fa-home',
                'pages'    => [
                    'advertiser' => [
                        'label'      => 'Advertiser',
                        'route'      => 'default/dashboard',
                        'controller' => 'dashboard',
                        'action'     => 'advertiser',
                        'icon'       => 'la la-tasks',
                    ],
                    'publisher'  => [
                        'label'      => 'Publisher',
                        'route'      => 'default/dashboard',
                        'controller' => 'dashboard',
                        'action'     => 'publisher',
                        'icon'       => 'la la-edit',
                    ],
                    'admin'      => [
                        'hidden'     => true,
                        'label'      => 'Admin',
                        'route'      => 'default/dashboard',
                        'controller' => 'dashboard',
                        'action'     => 'list',
                        'icon'       => 'la la-plus',
                    ],
                ],
            ],
        ],
    ],
    'controllers'     => [
        'factories' => [
            Controller\DashboardController::class => InvokableAwareFactory::class,
        ],
        'aliases'   => [
            'dashboard' => Controller\DashboardController::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            ModuleOptions::class      => InvokableAwareFactory::class,
            DashboardAdapter::class   => InvokableAwareFactory::class,
            //decorator
            ResultSetDecorator::class => InvokableAwareFactory::class

        ],
        'aliases'   => [
            ResultSet::class . 'Decorator' => ResultSetDecorator::class
        ],
    ],
    'view_manager'    => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
    'module_options'  => [
        strtolower(__NAMESPACE__) =>
            [
                'activate_on_routes' => [
                    'default',
                    'default/report',
                    'default/admin',
                    'default/dashboard'
                ]
            ],
    ],
];
