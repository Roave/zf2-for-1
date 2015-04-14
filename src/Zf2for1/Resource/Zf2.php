<?php
/**
* ZF 2-for-1
*
* @link https://github.com/Roave/zf2-for-1 for the canonical source repository
* @copyright Copyright (c) 2005-2013 Evan Coury (http://blog.evan.pro/)
* @license New BSD License
 */

use Zend\Loader\AutoloaderFactory;
use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Service;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\ArrayUtils;

class Zf2for1_Resource_Zf2
    extends Zend_Application_Resource_ResourceAbstract
{
    protected $app;

    public function init()
    {
        $this->registerZf2Autoloader();

        $options = $this->getOptions();
        $configPath = isset($options['config_path'])
            ? $options['config_path']
            : dirname(APPLICATION_PATH) . '/config';

        //whole zf1 application config
        $zf1Config = $this->getBootstrap()->getApplication()->getOptions();

        // get zf2 application config
        $appConfig = require $configPath . '/application.config.php';

        // Load application config overrides from apigility style development.config.php
        if (file_exists($configPath . '/development.config.php')) {
            $appConfig = ArrayUtils::merge($appConfig, require $configPath . '/development.config.php');
        }

        $appConfig = ArrayUtils::merge(
            $appConfig,
            array(
                'modules' => array(
                    'Zf2for1'
                ),
                'module_listener_options' => array(
                    'extra_config' => array(
                        'zf1' => $zf1Config,
                    )
                )
            )
        );

        $configuration = $appConfig;

        //------ copied from Zend\Mvc\Application::init
        $smConfig = isset($configuration['service_manager']) ? $configuration['service_manager'] : array();
        $serviceManager = new ServiceManager(new Service\ServiceManagerConfig($smConfig));
        $serviceManager->setService('ApplicationConfig', $configuration);
        $serviceManager->get('ModuleManager')->loadModules();

        $listenersFromAppConfig     = isset($configuration['listeners']) ? $configuration['listeners'] : array();
        $config                     = $serviceManager->get('Config');
        $listenersFromConfigService = isset($config['listeners']) ? $config['listeners'] : array();

        $listeners = array_unique(array_merge($listenersFromConfigService, $listenersFromAppConfig));

        // Changed. Do not bootstrap yet
        $application = $serviceManager->get('Application');
        //END------ copied from Zend\Mvc\Application::init

        //register zf1 bootstrap in ServiceManager
        $zf1Bootstrap = $this->getBootstrap();
        $serviceManager = $application->getServiceManager();
        $serviceManager->setService('zf1_bootstrap', $zf1Bootstrap);

        // register service manager in zf1 registry
        $registry = Zend_Registry::getInstance();
        $registry->set('service_manager', $serviceManager);

        // trick zf1 bootstrap into thinking Zf2For1\Resource\Zf2 has
        // finished bootstrapping to prevent circular dependency errors
        $zf1Bootstrap->getContainer()->zf2 = $application;
        $reflectionClass = new \ReflectionClass($zf1Bootstrap);
        $reflectionMethod = $reflectionClass->getMethod('_markRun');
        $reflectionMethod->setAccessible(true);
        $reflectionMethod->invoke($zf1Bootstrap, 'zf2');

        // bootstrap zf1 after zf2 MVC is configured but before modules are bootstrapped
        $application->getEventManager()
            ->attach(MvcEvent::EVENT_BOOTSTRAP, array($this, 'onBootstrap'), 9900);

        // now bootstrap zf2
        $application->bootstrap($listeners);

        return $application;
    }

    public function onBootstrap()
    {
        $this->getBootstrap()->bootstrap();
    }

    protected function registerZf2Autoloader()
    {
        $options = $this->getOptions();

        if (!empty($options['zf2_path'])) {
            include_once $options['zf2_path'] . '/Zend/Loader/AutoloaderFactory.php';
        }

        if (!class_exists('Zend\\Loader\\AutoloaderFactory', true)) {
            throw new DomainException('Option "zf2Path" was not provided');
        }

        AutoloaderFactory::factory(array(
            'Zend\Loader\StandardAutoloader' => array(
                'autoregister_zf' => true
            )
        ));
    }
}
