<?php

namespace Zf2for1\Application;

use Zend_Application_Bootstrap_Bootstrap;
use Zend_Application_Bootstrap_Exception;
use Zf2for1\Resource\Zf2;

/**
 * This bootstrap class will botstrap zf1 application as usual,
 * but will run zf2 application instead
 *
 * !!!IMPORTANT!!! This class will NOT run zf2 and zf1 applications in parallel!
 * Nor will it allow to use zf1 plugins and helpers with zf2 app.
 *
 * See README for details
 */
class Zf2Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    /**
     * Ensure zf2 resource is registered
     */
    public function __construct($application)
    {
        parent::__construct($application);

        if (!$this->hasPluginResource('zf2')) {
            $this->registerPluginResource('zf2');
        }
    }

    final protected function _bootstrap($resource = null)
    {
        //zf2 resource must be bootstrapped first.
        $this->_executeResource('zf2');
        return parent::_bootstrap($resource);
    }

    public function hasResource($name)
    {
        //zf2 should never try to proxy to service locator
        if (strtolower($name) == 'zf2') {
            return parent::hasResource('zf2');
        }
        $service = $this->resolveResourceToServiceName($name);
        $serviceLocator = $this->getResource('zf2')->getServiceManager();

        // get resource from service manager or fallback to zf1 resources for
        // greater backwards compatibility while utilizing service locator
        return $serviceLocator->has($service) || parent::hasResource($name);
    }

    public function getResource($name)
    {
        if (strtolower($name) == 'zf2') {
            //for zf2 go directly to resource class
            return parent::getResource('zf2');
        }

        // first try to get resource from service manager
        $service = $this->resolveResourceToServiceName($name);
        $serviceLocator = $this->getResource('zf2')->getServiceManager();
        if ($serviceLocator->has($service)) {
            try {
                return $serviceLocator->get($service);
            } catch (\Exception $e) {
                throw new Zend_Application_Bootstrap_Exception($e->getMessage(), 0, $e);
            }
        }
        // fallback to zf1 resources
        return parent::getResource($name);
    }

    public function resolveResourceToServiceName($resource)
    {
        return 'zf1_resource_' . $resource;
    }

    public function run()
    {
        $front = $this->getResource('frontcontroller');
        $front->setParam('bootstrap', $this);
        $application = $this->getResource('zf2');
        $config = $application->getServiceManager()->get('Config');

        if ($config['zf2_for_1']['silent_zf1_fallback'] === true) {
            $application->getEventManager()->attach('zf1', array($this, 'parentRun'));
        }

        return $application->run();
    }

    public function parentRun($event)
    {
        return parent::run();
    }
}
