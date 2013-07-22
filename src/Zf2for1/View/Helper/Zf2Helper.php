<?php

namespace Zf2for1\View\Helper;

use Zend\View\HelperPluginManager;
use Zend_View_Helper_Abstract;

class Zf2Helper extends Zend_View_Helper_Abstract
{
    protected $helperManager;

    public function __construct(HelperPluginManager $helperManager)
    {
        $this->helperManager = $helperManager;
    }

    public function zf2Helper($name = null)
    {
        if ($name !== null) {
            return $this->helperManager->get($name);
        }

        return $this;
    }

    public function __call($method, $argv)
    {
        $helper = $this->helperManager->get($method);

        if (is_callable($helper)) {
            return call_user_func_array($helper, $argv);
        }

        return $helper;
    }


}
