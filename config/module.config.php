<?php

return array(
    'zf2_for_1' => array(
        'silent_zf1_fallback' => true,
    ),
    'controller_plugins' => array(
        'invokables' => array(
            'fromLegacyParams' => 'Zf2for1\Mvc\Controller\Plugin\FromLegacyParams',
        ),
    ),
    'service_manager' => array (
        'factories' => array(
        ),
    ),
);
