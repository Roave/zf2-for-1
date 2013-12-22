<?php

namespace Zf2for1\Application;

use Zend_Application_Bootstrap_Bootstrap;
use Zend_Application_Bootstrap_Exception;
use Zf2for1\Resource\Zf2;

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    /**
     * Class cannot have resources.
     * Resources are now handled by ZF2 Service Manager
     * @see http://framework.zend.com/manual/2.2/en/modules/zend.service-manager.intro.html
     *
     * @return array
     */
    final public function getClassResources()
    {
        return array();
    }

    /**
     * Class cannot have resources.
     * Resources are now handled by ZF2 Service Manager
     * @see http://framework.zend.com/manual/2.2/en/modules/zend.service-manager.intro.html
     *
     * @return array
     */
    final public function getClassResourceNames()
    {
        return array();
    }

    /**
     * zf1 bootstrap only needed to bootstrap zf2 resource.
     * That resource will bootstrap Zend\Mvc\Application with compatibility code
     * required to setup zf1 application
     *
     * @param mixed $resource
     * @return void
     */
    final protected function _bootstrap($resource = null)
    {
        //zf2 resource must be bootstrapped first.
        $this->_executeResource('zf2');
        if (null === $resource || 'zf2' == $resource) {
            return; //Noop. This case will be handled during zf2 resource bootstrap
        }
        if (is_string($resource)) {
            // allow forced execution of resource due to legacy static state approach?
            // proper way is to list
            $this->getResource($resource);
        } else if (is_array($resource)) {
            foreach ($resource as $r) {
                $this->getResource($r);
            }
        } else {
            throw new Zend_Application_Bootstrap_Exception('Invalid argument passed to ' . __METHOD__);
        }
    }

    public function hasResource($name)
    {
        if (strtolower($name) == 'zf2') {
            return parent::hasResource('zf2');
        }
        $name = $this->resolveResourceToServiceName($name);
        return $this->getResource('zf2')->getServiceManager()->has($name);
    }

    public function getResource($name)
    {
        if (strtolower($name) == 'zf2') {
            return parent::getResource('zf2');
        }
        $name = $this->resolveResourceToServiceName($name);
        try {
            return parent::getResource('zf2')->getServiceManager()->get($name);
        } catch (\Exception $e) {
            throw new Zend_Application_Bootstrap_Exception($e->getMessage(), 0, $e);
        }
    }

    public function resolveResourceToServiceName($resource)
    {
        return 'zf1_resource_' . $resource;
    }
}
