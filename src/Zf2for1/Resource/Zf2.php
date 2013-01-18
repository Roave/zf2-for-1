<?php
/**
* ZF 2-for-1
*
* @link https://github.com/EvanDotPro/zf-2-for-1 for the canonical source repository
* @copyright Copyright (c) 2005-2012 Evan Coury (http://blog.evan.pro/)
* @license New BSD License
*/

class Zf2for1_Resource_Zf2
    extends Zend_Application_Resource_ResourceAbstract
{
    public function init()
    {
        $options = $this->getOptions();

        include $options['zf2Path'] . '/Zend/Loader/AutoloaderFactory.php';
        \Zend\Loader\AutoloaderFactory::factory(array(
            'Zend\Loader\StandardAutoloader' => array(
                'autoregister_zf' => true
            )
        ));

        $app = \Zend\Mvc\Application::init(require $options['configPath'] . '/application.config.php');
        $serviceManager = $app->getServiceManager();

        $bootstrap = $this->getBootstrap();
        $bootstrap->bootstrap('view');
        $view = $bootstrap->getResource('view');
        $view->zf2 = $serviceManager->get('ViewHelperManager');
    }
}
