<?php

namespace Zf2for1\Legacy;

use Zend\Console\Request as ConsoleRequest;
use Zend\Mvc\Router\RouteMatch;
use Zend\Stdlib\RequestInterface as Request;

class LegacyParams
{
    protected $routeMatch;
    protected $request;

    public function __construct(RouteMatch $routeMatch, Request $request)
    {
        $this->setRouteMatch($routeMatch);
        $this->setRequest($request);
    }

    /**
     * Mimics zf1 Request::getParam behavior
     *
     * Route match -> GET -> POST
     */
    public static function staticGetParam(
        RouteMatch $routeMatch,
        Request $request,
        $param = null,
        $default = null
    ) {
        if ($param === null) {
            $params = (array) $routeMatch->getParams();
            if ($request instanceof ConsoleRequest) {
                return $params + (array) $request->getParams();
            }
            return (
                $params
                + $request->getQuery()->toArray()
                + $request->getPost()->toArray()
            );

        }

        if ($request instanceof ConsoleRequest) {
            $default = $request->getParam($param, $default);
        } else {
            $default = $request->getQuery(
                $param,
                $request->getPost($param, $default)
            );

        }

        return $routeMatch->getParam(
            $param,
            $default
        );
    }

    /**
     * Mimics zf1 Request::getParam behavior
     *
     * Route match -> GET -> POST
     */
    public function getParam($param = null, $default = null)
    {
        return static::staticGetParam(
            $this->routeMatch,
            $this->request,
            $param,
            $default
        );
    }

    public function setRouteMatch(RouteMatch $routeMatch)
    {
        $this->routeMatch = $routeMatch;
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }
}
