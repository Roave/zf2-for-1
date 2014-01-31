<?php

namespace Zf2for1\Mvc\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zf2for1\Legacy\LegacyParams;

class FromLegacyParams extends AbstractPlugin
{
    public function __invoke($param = null, $default = null)
    {
        $request    = $this->getController()->getRequest();
        $routeMatch = $this->getController()->getEvent()->getRouteMatch();
        return LegacyParams::staticGetParam($routeMatch, $request, $param, $default);
    }
}
