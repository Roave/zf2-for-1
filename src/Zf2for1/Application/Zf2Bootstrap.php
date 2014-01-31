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

        // return registered resource or proxy to service locator to allow
        // greater backwards compatibility while utilizing service locator
        return parent::hasResource($name) || $serviceLocator->has($service);
    }

    public function getResource($name)
    {
        if (strtolower($name) == 'zf2' || parent::hasResource($name)) {
            return parent::getResource($name);
        }

        $service = $this->resolveResourceToServiceName($name);
        $serviceLocator = $this->getResource('zf2')->getServiceManager();
        if ($serviceLocator->has($service)) {
            try {
                return $serviceLocator->get('service');
            } catch (\Exception $e) {
                throw new Zend_Application_Bootstrap_Exception($e->getMessage(), 0, $e);
            }
        }

        return null;
    }

    public function resolveResourceToServiceName($resource)
    {
        return 'zf1_resource_' . $resource;
    }

    public function run()
    {
        $front = $this->getResource('frontcontroller');
        $front->setParam('bootstrap', $this);
        return $this->getResource('zf2')->getApplication()->run();
    }
}
