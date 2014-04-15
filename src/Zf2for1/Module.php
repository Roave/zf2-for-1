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
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\EventInterface;

class Module implements
    AutoloaderProviderInterface,
    ConfigProviderInterface,
    BootstrapListenerInterface
{
    public function onBootstrap(EventInterface $e)
    {
        $application = $e->getApplication();
        $config = $application->getServiceManager()->get('Config');

        if ($config['zf2_for_1']['silent_zf1_fallback'] === true) {
            $application->getEventManager()->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'onDispatchError'), 1000);
        }
    }

    public function onDispatchError(MvcEvent $e)
    {
        if ($e->getError() != Application::ERROR_ROUTER_NO_MATCH) {
            return;
        }

        $eventManager = $e->getApplication()->getEventManager();
        $eventManager->attach(MvcEvent::EVENT_RENDER, function($e) { $e->stopPropagation(); }, 1000); // Don't want ZF2's error rendering to kick in
        $eventManager->attach(MvcEvent::EVENT_FINISH, array($this, 'onFinish'), 1000);
    }

    public function onFinish(MvcEvent $e)
    {
        $e->stopPropagation(); // Silently fall back to ZF1
        $e->getApplication()->getEventManager()->trigger('zf1');
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__,
                ),
            ),
        );
    }

    public function getConfig($env = null)
    {
        return include dirname(dirname(__DIR__)) . '/config/module.config.php';
    }
}
