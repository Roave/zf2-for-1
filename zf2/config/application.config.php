<?php
return array(
    'modules' => array(
    ),
    'module_listener_options' => array(
        'config_glob_paths'    => array(
            APPLICATION_PATH . '/zf2/config/autoload/{,*.}{global,local}.php',
        ),
        'module_paths' => array(
            APPLICATION_PATH . '/zf2/module',
            APPLICATION_PATH . '/zf2/vendor',
        ),
    ),
);

