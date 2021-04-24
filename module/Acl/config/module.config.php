<?php

namespace Acl;

use Acl\Adapter\ColumnAdapter;
use Acl\Adapter\MVCAdapter;
use Acl\Controller\ColumnController;
use Acl\Role\Admin;
use Acl\Role\Advertiser;
use Acl\Role\Developer;
use Acl\Role\Guest;
use Acl\Role\Manager;
use Acl\Role\Publisher;
use Acl\Role\Redactor;
use Acl\Service\AclService;
use Application\ServiceManager\InvokableAwareFactory;

return [
    'navigation'      => [
        'default' => [
            'admin' => [
                'pages' => [
                    'acl' => [
                        'label'      => 'Access Management',
                        'route'      => 'default/admin',
                        'controller' => 'mvc',
                        'icon'       => 'la la-shield',
                        'pages'      => [
                            'mvc'     => [
                                'label'      => 'Resource Management',
                                'route'      => 'default/admin',
                                'controller' => 'mvc',
                                'action'     => 'list',
                                'icon'       => 'la la-user-shield',
                            ],
                            'columns' => [
                                'label'      => 'Columns Management',
                                'route'      => 'default/admin',
                                'controller' => 'column',
                                'action'     => 'list',
                                'icon'       => 'la la-user-shield',
                            ],
                        ]
                    ],
                ],
            ],
        ],
    ],
    'controllers'     => [
        'factories' => [
            Controller\MVCController::class => InvokableAwareFactory::class,
            ColumnController::class         => InvokableAwareFactory::class,
        ],
        'aliases'   => [
            'mvc'    => Controller\MVCController::class,
            'column' => Controller\ColumnController::class
        ],
    ],
    'service_manager' => [
        'factories' => [
            ModuleOptions::class => InvokableAwareFactory::class,
            AclService::class    => InvokableAwareFactory::class,
            Developer::class     => InvokableAwareFactory::class,
            Admin::class         => InvokableAwareFactory::class,
            Manager::class       => InvokableAwareFactory::class,
            Advertiser::class    => InvokableAwareFactory::class,
            Publisher::class     => InvokableAwareFactory::class,
            Redactor::class      => InvokableAwareFactory::class,
            Guest::class         => InvokableAwareFactory::class,
            MVCAdapter::class    => InvokableAwareFactory::class,
            ColumnAdapter::class => InvokableAwareFactory::class,
        ],
        'aliases'   => [
            'acl'        => AclService::class,
            'developer'  => Developer::class,
            'admin'      => Admin::class,
            'manager'    => Manager::class,
            'advertiser' => Advertiser::class,
            'publisher'  => Publisher::class,
            'redactor'   => Redactor::class,
            'guest'      => Guest::class,
        ],
    ],
    'view_manager'    => [
        'template_map'        => [
            'acl/index/index' => __DIR__ . '/../view/acl/index/index.phtml',
        ],
        'template_path_stack' => [
            'acl' => __DIR__ . '/../view',
        ],
    ],
    'doctrine'        => [
        'driver' => [
            'application_entities' => [
                'paths' => [__DIR__ . '/../src/Entity']
            ],
            'orm_default'          => [
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => 'application_entities',
                ],
            ],
        ]
    ],
    'module_options'  => [
        'acl' =>
            [
                'exclude' => [],
            ],
    ],
];
