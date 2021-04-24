<?php

    namespace Product;

    use Application\ServiceManager\InvokableAwareFactory;
    use Laminas\Router\Http\Segment;
    use Product\Adapter\ProductAdapter;
    use Product\Decorator\ProductDecorator;
    use Product\Entity\Product;
    use Product\Form\ProductEdit;

    return [
        'router'          => [
            'routes' => [
                'default' => [
                    'child_routes' => [
                        'product' => [
                            'type'    => Segment::class,
                            'options' => [
                                'route'       => '/product/[:controller][/:action][/:id][/]',
                                'defaults'    => [
                                    'action' => 'list',
                                    'id'     => '',
                                ],
                                'constraints' => [
                                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                    'id'     => '[a-zA-Z0-9_-]*',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
        'navigation'      => [
            'default' => [
                'campaign' => [
                    'label'      => 'Campaign',
                    'route'      => 'default/product',
                    'controller' => 'campaign',
                    'order'      => 30,
                    'pages'      => [
                        'list'   => [
                            'label'      => 'Campaign Management',
                            'route'      => 'default/product',
                            'controller' => 'campaign',
                            'action'     => 'list',
                            'icon'       => 'la la-tasks',
                        ],
                        'edit'   => [
                            'hidden'     => true,
                            //Not visible in menu but visible on breadcrumbs an window title. 'visible' === false not shown nowhere
                            'label'      => 'Campaign Edit',
                            'route'      => 'default/product',
                            'controller' => 'campaign ',
                            'action'     => 'edit',
                            'icon'       => 'la la-edit',
                        ],
                        'create' => [
                            'label'      => 'Campaign Create',
                            'route'      => 'default/product',
                            'controller' => 'campaign',
                            'action'     => 'create',
                            'icon'       => 'la la-plus',
                        ],
                    ],

                ],
            ],
        ],
        'controllers'     => [
            'factories' => [
                Controller\ProductController::class => InvokableAwareFactory::class,
            ],
            'aliases'   => [
                'campaign' => Controller\ProductController::class,
            ],
        ],
        'service_manager' => [
            'factories' => [
                ProductAdapter::class   => InvokableAwareFactory::class,
                ModuleOptions::class    => InvokableAwareFactory::class,
                // forms
                ProductEdit::class      => InvokableAwareFactory::class,
                // decorators
                ProductDecorator::class => InvokableAwareFactory::class,

            ],
            'aliases'   => [
                Product::class . 'Decorator' => ProductDecorator::class
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
                    'upload_accept' => [
                        'creativeFiles' => '.png, .jpg, .jpeg'
                    ],
                    'upload_path'   => APPLICATION_PATH . '/public/files/creative',
                ],

            'report' =>
                [
                    'activate_on_routes' => [
                        'default/product'
                    ]
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
