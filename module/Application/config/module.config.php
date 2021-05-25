<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-skeleton for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-skeleton/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-skeleton/blob/master/LICENSE.md New BSD License
 */

declare(strict_types = 1);

namespace Application;

use Application\Controller\Plugin\Acl;
use Application\Controller\Plugin\Decorate;
use Application\Controller\Plugin\HeadTitle;
use Application\Controller\Plugin\History;
use Application\Controller\Plugin\Identity;
use Application\Controller\Plugin\IsAjax;
use Application\Controller\Plugin\IsAllowedColumn;
use Application\Controller\Plugin\Translate;
use Application\Controller\Plugin\Urlx;
use Application\Doctrine\Event\DoctrineEventSubscriber;
use Application\Form\ConfirmForm;
use Application\Form\EntityEdit;
use Application\Form\Factory\FormElementFactory;
use Application\Form\ListForm;
use Application\Form\StatusEdit;
use Application\Repository\Factory\RepositoryAwareFactory;
use Application\Repository\Factory\RepositoryAwareFactoryFactory;
use Application\ServiceManager\InvokableAwareFactory;
use Application\View\Helper\Acl as AclHeleper;
use Application\View\Helper\Decorate as DecorateHelper;
use Application\View\Helper\Form\FormCheckbox;
use Application\View\Helper\Form\FormCollection;
use Application\View\Helper\Form\FormFile;
use Application\View\Helper\Form\FormRadio;
use Application\View\Helper\Form\FormRow;
use Application\View\Helper\ServiceManager;
use Application\View\Manager\EmptyAdapter;
use Application\View\Manager\ViewManager;
use Attribute\Hydrator\CustomEntityHydratorFactory;
use Doctrine\Laminas\Hydrator\DoctrineObject;
use Gedmo\Timestampable\TimestampableListener;
use Laminas\Router\Http\Segment;

return [
    'router'             => [
        'routes' => [
            'default' => [
                'type'          => Segment::class,
                'options'       => [
                    'route'    => '/[:language]',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                        'language'   => 'en',
                    ],
                ],
                'may_terminate' => true,
                'child_routes'  => [
                    'admin'       => [
                        'type'    => Segment::class,
                        'options' => [
                            'route'    => '/admin/[:controller][/:action][/:id][/]',
                            'defaults' => [
                                'controller' => 'index',
                                'id'         => '',
                            ],
                        ],
                    ],
                    'integration' => [
                        'type'    => Segment::class,
                        'options' => [
                            'route'    => '/help[/:action]',
                            'defaults' => [
                                'controller' => 'help',
                                'action'     => 'list'
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'controllers'        => [
        'factories' => [
            Controller\IndexController::class => InvokableAwareFactory::class,
        ],
        'aliases'   => [

        ],
    ],
    'controller_plugins' => [
        'invokables' => [
            'history'         => History::class,
            'translate'       => Translate::class,
            'decorate'        => Decorate::class,
            'urlx'            => Urlx::class,
            'acl'             => Acl::class,
            'identity'        => Identity::class,
            'headTitle'       => HeadTitle::class,
            'isAllowedColumn' => IsAllowedColumn::class,
            'isAjax'          => IsAjax::class,
        ],
    ],
    'navigation'         => [
        'default' => [
            'admin' => [
                'label' => 'Administration',
                'route' => 'default/admin',
                'order' => 30,
            ]
        ],
    ],
    'service_manager'    => [
        'factories' => [
            ModuleOptions::class           => InvokableAwareFactory::class,
            StatusEdit::class              => InvokableAwareFactory::class,
            ConfirmForm::class             => InvokableAwareFactory::class,
            ViewManager::class             => InvokableAwareFactory::class,
            FormElementFactory::class      => InvokableAwareFactory::class,
            RepositoryAwareFactory::class  => RepositoryAwareFactoryFactory::class,
            EntityEdit::class              => InvokableAwareFactory::class,
            ListForm::class                => InvokableAwareFactory::class,
            EmptyAdapter::class            => InvokableAwareFactory::class,
            DoctrineEventSubscriber::class => InvokableAwareFactory::class,
            DoctrineObject::class          => CustomEntityHydratorFactory::class,
        ],
    ],
    'view_helpers'       => [
        'invokables' => [
            //  override laminas form element  helpers
            'formrow'         => FormRow::class,
            'form_row'        => FormRow::class,
            'formRow'         => FormRow::class,
            'FormRow'         => FormRow::class,
            'formcollection'  => FormCollection::class,
            'form_collection' => FormCollection::class,
            'formCollection'  => FormCollection::class,
            'FormCollection'  => FormCollection::class,
            'formcheckbox'    => FormCheckbox::class,
            'form_checkbox'   => FormCheckbox::class,
            'formCheckbox'    => FormCheckbox::class,
            'FormCheckbox'    => FormCheckbox::class,
            'formradio'       => FormRadio::class,
            'form_radio'      => FormRadio::class,
            'formRadio'       => FormRadio::class,
            'FormRadio'       => FormRadio::class,

            'form_file' => FormFile::class,
            'formfile'  => FormFile::class,
            'formFile'  => FormFile::class,
            'FormFile'  => FormFile::class,
        ],
        'factories'  => [
            ServiceManager::class              => InvokableAwareFactory::class,
            DecorateHelper::class              => InvokableAwareFactory::class,
            //ViewConfig::class                  => InvokableAwareFactory::class,
            AclHeleper::class                  => InvokableAwareFactory::class,
            View\Helper\History::class         => InvokableAwareFactory::class,
            View\Helper\Identity::class        => InvokableAwareFactory::class,
            View\Helper\IsAjax::class          => InvokableAwareFactory::class,
            View\Helper\IsAllowedColumn::class => InvokableAwareFactory::class,
        ],
        'aliases'    => [
            'decorate'          => View\Helper\Decorate::class,
//                'viewConfig'        => ViewConfig::class,
            'acl'               => View\Helper\Acl::class,
            'history'           => View\Helper\History::class,
            'identity'          => View\Helper\Identity::class,
            'isAjax'            => View\Helper\IsAjax::class,
            'serviceManager'    => ServiceManager::class,
            'getServiceManager' => ServiceManager::class,
            'isAllowedColumn'   => View\Helper\IsAllowedColumn::class,

            // alias the first after laminas update
            'formrow'           => FormRow::class,
            'form_row'          => FormRow::class,
            'formRow'           => FormRow::class,
            'FormRow'           => FormRow::class,
            'formcollection'    => FormCollection::class,
            'form_collection'   => FormCollection::class,
            'formCollection'    => FormCollection::class,
            'FormCollection'    => FormCollection::class,
            'formcheckbox'      => FormCheckbox::class,
            'form_checkbox'     => FormCheckbox::class,
            'formCheckbox'      => FormCheckbox::class,
            'FormCheckbox'      => FormCheckbox::class,
            'formradio'         => FormRadio::class,
            'form_radio'        => FormRadio::class,
            'formRadio'         => FormRadio::class,
            'FormRadio'         => FormRadio::class,

            'form_file' => FormFile::class,
            'formfile'  => FormFile::class,
            'formFile'  => FormFile::class,
            'FormFile'  => FormFile::class,
        ],
    ],
    'module_options'     => [
        'application' => [
            'upload_base_path' => APPLICATION_PATH . '/public/files',
            'public_path'      => APPLICATION_PATH . '/public',
            'short_name'       => 'PIM',
            'vendor'           => 'Alexej Kisselev'
        ],
    ],
    'view_manager'       => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map'             => [
//				'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
//				'error/404'               => __DIR__ . '/../view/error/404.phtml',
//				'error/index'             => __DIR__ . '/../view/error/index.phtml',
            'app/footer' => __DIR__ . '/../view/layout/partial/footer.phtml',

            'logo/aside'  => __DIR__ . '/../view/layout/partial/logo-svg-blue.phtml',
            'logo/header' => __DIR__ . '/../view/layout/partial/logo-svg-blue.phtml',
            'logo/login'  => __DIR__ . '/../view/layout/partial/logo-svg-blue-login.phtml',


        ],
        'template_path_stack'      => [
            __DIR__ . '/../view',
        ],
    ],
    'doctrine'           => [
        'eventmanager' => [
            'orm_default' => [
                'subscribers' => [
                    TimestampableListener::class,
                ],
            ],
        ],
        'driver'       => [
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
    'translator'         => [
        'locale'                    => 'en',
        'available'                 => [
            'en' => 'English',
            'de' => 'Deutsch',
        ],
        'translation_file_patterns' => [
            [
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ],
        ],
    ],
];
