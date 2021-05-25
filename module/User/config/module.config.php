<?php /** @noinspection TransitiveDependenciesUsageInspection */

namespace User;

use Application\ServiceManager\InvokableAwareFactory;
use Laminas\Authentication\AuthenticationService;
use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use User\Adapter\ProfileAdapter;
use User\Adapter\UserAdapter;
use User\Controller\ProfileController;
use User\Decorator\User as UserDecorator;
use User\Entity\User as UserEntity;
use User\Form\LoginForm;
use User\Form\UserCreate;
use User\Form\UserEdit;
use User\Form\UserPasswordChange;
use User\Form\UserPasswordReset;
use User\Form\UserPasswordSet;


return [
    'router'          => [
        'routes' => [
            'default' => [
                'child_routes' => [
                    'profile' => [
                        'type'    => Segment::class,
                        'options' => [
                            'route'       => '/my[/:controller][/:action]',
                            'controller'  => ProfileController::class,
                            'defaults'    => [
                                'action' => 'list'
                            ],
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id'     => '[a-zA-Z0-9_-]*',
                            ],
                        ],
                    ],
                ],
            ],
            'auth'    => [
                'type'    => Segment::class,
                'options' => [
                    'route'       => '/auth[/:action]',
                    'defaults'    => [
                        'controller' => Controller\AuthController::class,
                        'action'     => 'login',
                        'id'         => '',
                    ],
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[a-zA-Z0-9_-]*',
                    ],
                ],
            ],
            'logout'  => [
                'type'    => Literal::class,
                'options' => [
                    'route'       => '/logout',
                    'defaults'    => [
                        'controller' => Controller\AuthController::class,
                        'action'     => 'logout'
                    ],
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[a-zA-Z0-9_-]*',
                    ],
                ],
            ],
        ],
    ],
    'navigation'      => [
        'default' => [
            'admin' => [
                'pages' => [
                    'user' => [
                        'label'      => 'User Management',
                        'route'      => 'default/admin',
                        'controller' => 'user',
//                            'action'     => 'list',
                        'icon'       => 'la la-user-friends',
                        'pages'      => [
                            'list'         => [
                                'label'      => 'User Management',
                                'route'      => 'default/admin',
                                'controller' => 'user',
                                'action'     => 'list',
                                'icon'       => 'la la-tasks',
                            ],
                            'edit'         => [
                                'hidden'     => true,
                                //Not visible in menu but visible on breadcrumbs an window title. 'visible' === false not shown nowhere
                                'label'      => 'User Edit',
                                'route'      => 'default/admin',
                                'controller' => 'user',
                                'action'     => 'edit',
                                'icon'       => 'la la-edit',
                            ],
                            'create'       => [
                                'label'      => 'User Create',
                                'route'      => 'default/admin',
                                'controller' => 'user',
                                'action'     => 'create',
                                'icon'       => 'la la-plus',
                            ],
                            'profile-edit' => [
                                'hidden'     => true,
                                'label'      => 'Edit',
                                'route'      => 'default/admin',
                                'controller' => 'profile',
                                'action'     => 'edit',
                                'icon'       => 'la la-edit',
                            ],
                        ],
                    ],
                ],
            ],

            'profile' => [
                'label' => 'Profile',
                'route' => 'default/profile',
                //'icon'  => 'icon-md text-dark-50 flaticon-settings-1',
                'order' => 40,
                //'controller' => 'profile',
                'scope' => 'user',
                'pages' => [
                    'list'     => [
                        'label'      => 'Overview',
                        'route'      => 'default/profile',
                        'controller' => 'profile',
                        'action'     => 'list',
                        'icon'       => 'icon-md text-dark-50 flaticon-list-2',

                    ],
                    'edit'     => [
                        'label'      => 'Edit Profile',
                        'route'      => 'default/profile',
                        'controller' => 'profile',
                        'action'     => 'edit',
                        'icon'       => 'icon-md text-dark-50 flaticon-settings-1',

                    ],
                    'password' => [
                        'label'      => 'Change Password',
                        'route'      => 'default/profile',
                        'controller' => 'profile',
                        'action'     => 'password',
                        'icon'       => 'icon-md text-dark-50 flaticon-lock',

                    ],
                    'email'    => [
                        'label'      => 'Change E-Mail',
                        'route'      => 'default/profile',
                        'controller' => 'profile',
                        'action'     => 'email',
                        'icon'       => 'icon-md text-dark-50 flaticon-email',
                        'visible'    => false, // not implemented yet
                    ],
                    'logout'   => [
                        'label' => 'Logout',
                        'route' => 'logout',
                        'icon'  => 'icon-md text-dark-50 flaticon-logout',
                    ],
                ],
            ],
        ],
    ],
    'controllers'     => [
        'factories' => [
            Controller\AuthController::class     => InvokableAwareFactory::class,
            Controller\UserController::class     => InvokableAwareFactory::class,
            Controller\ProfileController::class  => InvokableAwareFactory::class,
            Controller\UserRoleController::class => InvokableAwareFactory::class,
        ],
        'aliases'   => [
            'user'    => Controller\UserController::class,
            'auth'    => Controller\AuthController::class,
            'profile' => Controller\ProfileController::class,
            'role'    => Controller\UserRoleController::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            AuthenticationService::class => Service\Factory\AuthenticationServiceFactory::class,
            Service\AuthAdapter::class   => InvokableAwareFactory::class,
            Service\AuthManager::class   => InvokableAwareFactory::class,
            Service\UserManager::class   => InvokableAwareFactory::class,

            ModuleOptions::class      => InvokableAwareFactory::class,
            // forms
            LoginForm::class          => InvokableAwareFactory::class,
            UserEdit::class           => InvokableAwareFactory::class,
            UserCreate::class         => InvokableAwareFactory::class,
            UserPasswordChange::class => InvokableAwareFactory::class,
            UserPasswordReset::class  => InvokableAwareFactory::class,
            UserPasswordSet::class    => InvokableAwareFactory::class,
            // decorators
            UserDecorator::class      => InvokableAwareFactory::class,
            UserAdapter::class        => InvokableAwareFactory::class,
            ProfileAdapter::class     => InvokableAwareFactory::class,
        ],
        'aliases'   => [
            'authentication'                => AuthenticationService::class,
            UserEntity::class . 'Decorator' => UserDecorator::class
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
                'csrf_on'             => true, // csrf security
                'registration_on'     => false,// show registration link
                'reset_password_on'   => false,// show password forgot link
                'dispatch_on'         => false, // redirect to login if not authenticated !!!
                'allow_guest'         => true, // no redirect to login
                'min_password_length' => 6,
                'password_options'    => [
                    'min' => 6,
                    'max' => 64
                ]
            ],
        'acl'                     => [
            'exclude' => [
                Controller\AuthController::class
            ]
        ]
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
