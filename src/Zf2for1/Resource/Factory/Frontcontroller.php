<?php

namespace Zf2for1\Resource\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend_Controller_Front;

class Frontcontroller implements FactoryInterface
{
    const ZF1_RESOURCE_FRONTCONTROLLER = 'zf1_resource_frontcontroller';

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $em = $serviceLocator->get('EventManager');
        $em->trigger(static::ZF1_RESOURCE_FRONTCONTROLLER);

        $config = $serviceLocator->get('config');
        // @todo extract this ugly code to helper method
        $config = isset($config['zf1']['resources'])
            ? array_change_key_case($config['zf1']['resources'])
            : array();
        $config = isset($config['frontcontroller']) ? $config['frontcontroller'] : array();

        $front = Zend_Controller_Front::getInstance();
        foreach ($config as $key => $value) {
            switch (strtolower($key)) {
                case 'controllerdirectory':
                    if (is_string($value)) {
                        $front->setControllerDirectory($value);
                    } elseif (is_array($value)) {
                        foreach ($value as $module => $directory) {
                            $front->addControllerDirectory($directory, $module);
                        }
                    }
                    break;

                case 'modulecontrollerdirectoryname':
                    $front->setModuleControllerDirectoryName($value);
                    break;

                case 'moduledirectory':
                    if (is_string($value)) {
                        $front->addModuleDirectory($value);
                    } elseif (is_array($value)) {
                        foreach($value as $moduleDir) {
                            $front->addModuleDirectory($moduleDir);
                        }
                    }
                    break;

                case 'defaultcontrollername':
                    $front->setDefaultControllerName($value);
                    break;

                case 'defaultaction':
                    $front->setDefaultAction($value);
                    break;

                case 'defaultmodule':
                    $front->setDefaultModule($value);
                    break;

                case 'baseurl':
                    if (!empty($value)) {
                        $front->setBaseUrl($value);
                    }
                    break;

                case 'params':
                    $front->setParams($value);
                    break;

                case 'plugins':
                    foreach ((array) $value as $pluginClass) {
                        $stackIndex = null;
                        if(is_array($pluginClass)) {
                            $pluginClass = array_change_key_case($pluginClass, CASE_LOWER);
                            if(isset($pluginClass['class']))
                            {
                                if(isset($pluginClass['stackindex'])) {
                                    $stackIndex = $pluginClass['stackindex'];
                                }

                                $pluginClass = $pluginClass['class'];
                            }
                        }

                        $plugin = new $pluginClass();
                        $front->registerPlugin($plugin, $stackIndex);
                    }
                    break;

                case 'returnresponse':
                    $front->returnResponse((bool) $value);
                    break;

                case 'throwexceptions':
                    $front->throwExceptions((bool) $value);
                    break;

                case 'actionhelperpaths':
                    if (is_array($value)) {
                        foreach ($value as $helperPrefix => $helperPath) {
                            Zend_Controller_Action_HelperBroker::addPath($helperPath, $helperPrefix);
                        }
                    }
                    break;

                case 'dispatcher':
                    $front->setDispatcher($serviceLocator->get($value));
                    break;
                default:
                    $front->setParam($key, $value);
                    break;
            }
        }

        $em->trigger(
            static::ZF1_RESOURCE_FRONTCONTROLLER . '.post',
            null,
            array('resource' => $front)
        );

        return $front;
    }
}
