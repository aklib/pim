<?php

namespace Attribute;

use Application\ServiceManager\InvokableAwareFactory;
use Attribute\Adapter\AttributeAdapter;
use Attribute\Adapter\TabAdapter;
use Attribute\Decorator\AttributeDecorator;
use Attribute\Decorator\TabDecorator;
use Attribute\Form\AttributeEdit;
use Attribute\Form\ReorderForm;
use Attribute\Hydrator\AttributeEntityHydrator;
use Attribute\Hydrator\CustomEntityHydratorFactory;

return [
    'navigation'      => [
        'default' => [
            'admin' => [
                'pages' => [
                    'attribute' => [
                        'label'      => 'Attribute Management',
                        'route'      => 'default/admin',
                        'controller' => 'attribute',
                        'action'     => 'list',
                        'icon'       => 'la la-cog',
                        'pages'      => [
                            'list'   => [
                                'label'      => 'Attribute Management',
                                'route'      => 'default/admin',
                                'controller' => 'attribute',
                                'action'     => 'list',
                                'icon'       => 'la la-tasks',
                            ],
                            'edit'   => [
                                'hidden'     => true,
                                'label'      => 'Attribute Edit',
                                'route'      => 'default/admin',
                                'controller' => 'attribute',
                                'action'     => 'edit',
                                'icon'       => 'la la-edit',
                            ],
                            'create' => [
                                'label'      => 'Attribute Create',
                                'route'      => 'default/admin',
                                'controller' => 'attribute',
                                'action'     => 'create',
                                'icon'       => 'la la-plus',
                            ],

                            'order' => [
                                'label'      => 'Attribute Reorder',
                                'route'      => 'default/admin',
                                'controller' => 'attribute',
                                'action'     => 'reorder',
                                'icon'       => 'la la-exchange-alt',
                            ],

                            'tab-list'   => [
                                'label'      => 'Tab Management',
                                'route'      => 'default/admin',
                                'controller' => 'tab',
                                'action'     => 'list',
                                'icon'       => 'la la-tasks',
                            ],
                            'tab-create' => [
                                'label'      => 'Tab Create',
                                'route'      => 'default/admin',
                                'controller' => 'tab',
                                'action'     => 'create',
                                'icon'       => 'la la-plus',
                            ],
                            'tab-edit'   => [
                                'hidden'     => true,
                                'label'      => 'Edit Attribute Tab',
                                'route'      => 'default/admin',
                                'controller' => 'tab',
                                'action'     => 'create',
                                'icon'       => 'la la-edit',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'controllers'     => [
        'factories' => [
            Controller\AttributeController::class => InvokableAwareFactory::class,
            Controller\TabController::class       => InvokableAwareFactory::class,
        ],
        'aliases'   => [
            'attribute' => Controller\AttributeController::class,
            'tab'       => Controller\TabController::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            ModuleOptions::class           => InvokableAwareFactory::class,
            // adapters
            AttributeAdapter::class        => InvokableAwareFactory::class,
            TabAdapter::class              => InvokableAwareFactory::class,
            //  forms
            ReorderForm::class             => InvokableAwareFactory::class,
            AttributeEdit::class           => InvokableAwareFactory::class,
            // decorators
            TabDecorator::class            => InvokableAwareFactory::class,
            AttributeDecorator::class      => InvokableAwareFactory::class,
            AttributeEntityHydrator::class => CustomEntityHydratorFactory::class,
        ],
        'aliases'   => [
            Entity\Attribute::class . 'Decorator'    => AttributeDecorator::class,
            Entity\AttributeTab::class . 'Decorator' => TabDecorator::class,
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
];
