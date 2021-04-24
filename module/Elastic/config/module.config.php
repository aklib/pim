<?php

namespace Elastic;

use Application\ServiceManager\InvokableAwareFactory;
use Elastic\Decorator\AggregationRowDecorator;
use Elastic\Factory\Client;
use Elastic\Factory\ClientFactory;
use Elastic\ODM\Query\QueryBuilder;
use Elastic\View\AggregationRow;
use Elastic\View\Helper\ChartBuilder;
use Elastic\View\Helper\TableBuilder;
use Laminas\Router\Http\Segment;

return [
    'router'                  =>
        [
            'routes' => [
                'default' => [
                    'child_routes' => [
                        'elastic' => [
                            'type'    => Segment::class,
                            'options' => [
                                'route'       => '/elastic/[:controller][/:action][/:id][/]',
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
    'service_manager'         => [
        'factories' => [
            ModuleOptions::class                     => InvokableAwareFactory::class,
            Client::class                            => ClientFactory::class,
            QueryBuilder::class                      => InvokableAwareFactory::class,
            AggregationRowDecorator::class => InvokableAwareFactory::class,
        ],
        'aliases'   => [
            'elastic.client' => Client::class,
            AggregationRow::class . 'Decorator' => AggregationRowDecorator::class
        ],
    ],
    'view_helpers'            => [
        'factories' => [
            ChartBuilder::class => InvokableAwareFactory::class,
            TableBuilder::class => InvokableAwareFactory::class,
        ],
        'aliases'   => [
            'chartBuilder' => ChartBuilder::class,
            'tableBuilder' => TableBuilder::class,
        ],
    ],
    'view_manager'            => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
    'module_options'          => [
        strtolower(__NAMESPACE__) => [],
    ],
    'laminas-developer-tools' => [
        'profiler' => [
            'collectors' => [
                'elastic.toolbar' => Client::class,
            ]
        ],
        'toolbar'  => [
            'entries' => [
                'elastic.toolbar' => 'developer-tools/toolbar/elastic',
            ]
        ]
    ]
];
