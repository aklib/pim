<?php

namespace Category;

use Application\ServiceManager\InvokableAwareFactory;
use Category\Adapter\CategoryAdapter;
use Category\Adapter\TabAdapter;
use Category\Decorator\CategoryDecorator;
use Category\Decorator\TabDecorator;
use Category\Form\CategoryEdit;
use Category\Form\ReorderForm;
use Category\Hydrator\AttributeEntityHydrator;
use Category\Hydrator\CustomEntityHydratorFactory;

return [
    'navigation'      => [
        'default' => [
            'admin' => [
                'pages' => [
                    'category' => [
                        'label'      => 'Category Management',
                        'route'      => 'default/admin',
                        'controller' => 'category',
                        'action'     => 'list',
                        'icon'       => 'la la-tree',
                    ],
                ],
            ],
        ],
    ],
    'controllers'     => [
        'factories' => [
            Controller\CategoryController::class => InvokableAwareFactory::class,
        ],
        'aliases'   => [
            'category' => Controller\CategoryController::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            ModuleOptions::class           => InvokableAwareFactory::class,
            // adapters
            CategoryAdapter::class         => InvokableAwareFactory::class,

            //  forms
            CategoryEdit::class            => InvokableAwareFactory::class,
            // decorators
            CategoryDecorator::class       => InvokableAwareFactory::class,
        ],
        'aliases'   => [
            Entity\Category::class . 'Decorator'    => CategoryDecorator::class,
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
