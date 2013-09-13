<?php
/**
* ZF 2-for-1
*
* @link https://github.com/EvanDotPro/zf-2-for-1 for the canonical source repository
* @copyright Copyright (c) 2005-2012 Evan Coury (http://blog.evan.pro/)
* @license New BSD License
 */
use Zend\Loader\AutoloaderFactory;
use Zend\Mvc\Application;
use Zend\StdLib\ArrayUtils;

class Zf2for1_Resource_Zf2
    extends Zend_Application_Resource_ResourceAbstract
{
    protected $app;

    public function init()
    {
        $options = $this->getOptions();

        include_once $options['zf2Path'] . '/Zend/Loader/AutoloaderFactory.php';
        AutoloaderFactory::factory(array(
            'Zend\Loader\StandardAutoloader' => array(
                'autoregister_zf' => true
            )
        ));

        //whole zf1 application config
        $zf1Config = $this->getBootstrap()->getApplication()->getOptions();

        $appConfig = ArrayUtils::merge(
            // get zf2 application config
            require $options['configPath'] . '/application.config.php',
            //register zf1 config with service manager
            array(
                'module_listener_options' => array(
                    'extra_config' => array(
                        'service_manager' => array(
                            'services' => array(
                                'zf1_config' => $zf1Config
                            )
                        )
                    )
                )
            )
        );


        $this->app = Application::init($appConfig);
        if (
            isset($this->_options['add_sm_to_registry'])
            && $this->_options['add_sm_to_registry'] == true
        ) {
            $serviceManager = $this->getServiceManager();
            $registry = Zend_Registry::getInstance();
            $registry->set('service_manager', $serviceManager);

        }
        return $this;
    }

    public function getServiceManager()
    {
        return $this->app->getServiceManager();
    }
}
