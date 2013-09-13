<?php
/**
* ZF 2-for-1
*
* @link https://github.com/Roave/zf2-for-1 for the canonical source repository
* @copyright Copyright (c) 2005-2013 Evan Coury (http://blog.evan.pro/)
* @license New BSD License
*/

use  Zf2for1\View\Helper\Zf2Helper;

class Zf2for1_Resource_View
    extends Zend_Application_Resource_View
{
    public function init()
    {
        $bootstrap = $this->getBootstrap();
        $bootstrap->bootstrap('zf2');
        $serviceManager = $bootstrap->getResource('zf2')->getServiceManager();

        //register zf1 helper to grant access to zf2 view helpers
        $zf2Helper = new Zf2Helper($serviceManager->get('ViewHelperManager'));
        $view = $this->getView();
        $view->registerHelper($zf2Helper, 'zf2Helper');

        return parent::init();
    }
}
