<?php

defined('MOODLE_INTERNAL') || die();

$functions = [
    'local_wordpress_integration_sync_user_and_enrol' => [
        'classname'   => 'local_wordpress_integration\external\user_service',
        'methodname'  => 'sync_user_and_enrol',
        'description' => 'Valida o crea un usuario y lo matricula en un curso.',
        'type'        => 'write',
        'ajax'        => true,
        'services'    => ['userapi_service']
    ],
];

$services = [
    'userapi_service' => [
        'functions' => [
            'local_wordpress_integration_sync_user_and_enrol',
        ],
        'restrictedusers' => 0,
        'enabled' => 1,
        'shortname' => 'userapi_service',
    ],
];