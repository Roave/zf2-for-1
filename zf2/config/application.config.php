<?php
return array(
    'modules' => array(
    ),
    'module_listener_options' => array(
        'config_glob_paths'    => array(
            dirname(__FILE__) . '/autoload/{,*.}{global,local}.php',
        ),
        // paths are relative to this file: ../module and ../vendor
        'module_paths' => array(
            dirname(dirname(__FILE__)) . '/module',
            dirname(dirname(__FILE__)) . '/vendor',
        ),
    ),
);

