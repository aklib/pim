<?php

/**
 * module.config.php
 *
 * @since 11.01.2021
 * @author Alexej Kisselev <alexej.kisselev@gmail.com>
 * @copyright apagmedia
 */

namespace Email;

use Application\ServiceManager\InvokableAwareFactory;
use Email\addapter\PreviewAdapter;
use Email\Controller\PreviewController;
use Email\Service\EmailManager;

return [
    'controllers'     => [
        'factories' => [
            PreviewController::class => InvokableAwareFactory::class,
        ],
        'aliases'   => [
            'preview' => PreviewController::class,
        ],
    ],
    'module_options'  => [
        strtolower(__NAMESPACE__) =>
            [
                // see autoload
            ],
    ],
    'service_manager' => [
        'factories' => [
            ModuleOptions::class  => InvokableAwareFactory::class,
            EmailManager::class   => InvokableAwareFactory::class,
            // adapters
            PreviewAdapter::class => InvokableAwareFactory::class,
        ],
        'aliases'   => [

        ],
    ],
    'view_manager'    => [
        'template_map'        => [
            //'email/layout'                         => __DIR__ . '/../view/layout/html.phtml',
            'email/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'email/header'           => __DIR__ . '/../view/layout/partial/header.phtml',
            'email/footer'           => __DIR__ . '/../view/layout/partial/footer.phtml',
            'email/password/recover' => __DIR__ . '/../view/templates/recover-password.phtml'
        ],
        'template_path_stack' => [
            'email' => __DIR__ . '/../view',
        ],
    ],
    'navigation'      => [
        'default' => [
            'admin' => [
                'pages' => [
                    'email' => [
                        'label'      => 'Email Templates',
                        'route'      => 'default/admin',
                        'controller' => 'preview',
                        'icon'       => 'la la-envelope',
                        'resource'   => 'email\controller\preview',
                        'privilege'  => 'list',
                        'pages'      => [
                            'list' => [
                                'label'      => 'Email Previews',
                                'route'      => 'default/admin',
                                'controller' => 'preview',
                                'action'     => 'list',
                                'icon'       => 'la la-tasks',
                            ],
                        ]
                    ],
                ],
            ],
        ],
    ],
];
