<?php

namespace Zf2for1Test\Mvc\Controller\Plugin;

use Zf2for1\Mvc\Controller\Plugin\FromLegacyParams;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\Http\Request;
use Zend\Mvc\Router\RouteMatch;
use Zend\Mvc\MvcEvent;

class FromLegacyParamsTest extends TestCase
{
    public function setUp()
    {
        $request = new Request();
        $request->getQuery()->fromArray(array(
            'all'            => 'query',
            'query_and_post' => 'query',
        ));
        $request->getPost()->fromArray(array(
            'all'            => 'post',
            'query_and_post' => 'post',
            'post_only'      => 'post',
        ));
        $routeMatch = new RouteMatch(array('all' => 'route'));
        $mvcEvent = new MvcEvent;
        $mvcEvent->setRouteMatch($routeMatch);

        $mock = $this->getMock('Zend\Mvc\Controller\AbstractController');
        $mock->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($request));

        $mock->expects($this->once())
            ->method('getEvent')
            ->will($this->returnValue($mvcEvent));


        $this->plugin = new FromLegacyParams;
        $this->plugin->setController($mock);
    }

    public function tearDown()
    {
        unset($this->plugin);
    }

    public function testGetsParameterFromRouteMatch()
    {
        $this->assertEquals('route', $this->plugin->__invoke('all'));
    }

    public function testFallbacksToQueryIfMissingInRouteMatch()
    {
        $this->assertEquals('query', $this->plugin->__invoke('query_and_post'));
    }

    public function testFallbacksToQueryIfMissingInQueryAndRouteMatch()
    {
        $this->assertEquals('post', $this->plugin->__invoke('post_only'));
    }

    public function testFallbacksToDefaultIfMissingInOtherSources()
    {
        $this->assertEquals('default', $this->plugin->__invoke('missing', 'default'));
    }

    public function testReturnsArrayMergedFromAllSourcesInProperOrderWhenParameterNameOmitted()
    {
        $expected = array(
            'all'            => 'route',
            'query_and_post' => 'query',
            'post_only'      => 'post',
        );
        $this->assertEquals($expected, $this->plugin->__invoke());
    }
}

