<?php
/**
* ZF 2-for-1
*
* @link https://github.com/Roave/zf2-for-1 for the canonical source repository
* @copyright Copyright (c) 2005-2013 Evan Coury (http://blog.evan.pro/)
* @license New BSD License
*/

namespace Zf2for1;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

class Module implements
    AutoloaderProviderInterface,
    ConfigProviderInterface,
    ServiceProviderInterface
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig($env = null)
    {
        return include __DIR__ . '/config/module.config.php';
    }
}
