<?php
return array(
    'modules' => array(
    ),
    'module_listener_options' => array(
        'config_glob_paths'    => array(
            __DIR__ . '/autoload/{,*.}{global,local}.php',
        ),
        // paths are relative to this file: ../module and ../vendor
        'module_paths' => array(
            dirname(__DIR__) . '/module',
            dirname(__DIR__) . '/vendor',
        ),
    ),
);

