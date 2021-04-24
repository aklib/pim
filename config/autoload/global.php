<?php

/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

use Application\Repository\Factory\RepositoryAwareFactory;
use Doctrine\DBAL\Driver\PDOMySql\Driver as MySQLDriver;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Laminas\Session\Storage\SessionArrayStorage;

return [
    [
        'db' =>
            [
                'driver_options' => [
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci'
                ],
            ]
    ],
    'doctrine'        => [
        'connection'    => [
            'orm_default' => [
                'driverClass' => MySQLDriver::class
            ]
        ],
        'configuration' => [
            'orm_default' => [
                'naming_strategy'    => new UnderscoreNamingStrategy(),
                'datetime_functions' => [
                ],
                'repository_factory' => RepositoryAwareFactory::class
            ]
        ],
        'driver'        => [
            'application_entities' => [
                'class' => AnnotationDriver::class,
                'cache' => 'array',
                'paths' => []
            ],
            'orm_default'          => [
                'drivers' => [

                ],
            ],
        ],
    ],
    'session_storage' => [
        'type' => SessionArrayStorage::class
    ],
    // Session configuration.
    'session_config'  => [
        'cookie_lifetime' => 60 * 60 * 1,       // Session cookie will expire in 1 hour.
        'gc_maxlifetime'  => 60 * 60 * 24 * 30, // How long to store session data on server (for 1 month).
    ],
    'module_options'  => [
        'application' => [
            'name'      => 'PIM',
            'shortname' => 'PIM',
            'vendor'    => 'Alexej Kisselev'
        ],
    ],
    'email'           => [
        'headline' => 'app/logo',
    ],
];
