<?php

namespace Zf2for1Test\Legacy;

use Zf2for1\Legacy\LegacyParams;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\Http\Request;
use Zend\Mvc\Router\RouteMatch;

class LegacyParamsTest extends TestCase
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

        $this->request = $request;

        $this->routeMatch = new RouteMatch(array('all' => 'route'));
    }

    public function tearDown()
    {
        unset($this->routeMatch);
        unset($this->request);
    }

    public function testGetsParameterFromRouteMatch()
    {
        $legacyParams = new LegacyParams($this->routeMatch, $this->request);

        $this->assertEquals('route', $legacyParams->getParam('all'));
        $this->assertEquals(
            'route',
            LegacyParams::staticGetParam($this->routeMatch, $this->request, 'all')
        );
    }

    public function testFallbacksToQueryIfMissingInRouteMatch()
    {
        $legacyParams = new LegacyParams($this->routeMatch, $this->request);

        $this->assertEquals('query', $legacyParams->getParam('query_and_post'));
        $this->assertEquals(
            'query',
            LegacyParams::staticGetParam($this->routeMatch, $this->request, 'query_and_post')
        );
    }

    public function testFallbacksToQueryIfMissingInQueryAndRouteMatch()
    {
        $legacyParams = new LegacyParams($this->routeMatch, $this->request);

        $this->assertEquals('post', $legacyParams->getParam('post_only'));
        $this->assertEquals(
            'post',
            LegacyParams::staticGetParam($this->routeMatch, $this->request, 'post_only')
        );
    }

    public function testFallbacksToDefaultIfMissingInOtherSources()
    {
        $legacyParams = new LegacyParams($this->routeMatch, $this->request);

        $this->assertEquals('default', $legacyParams->getParam('missing', 'default'));
        $this->assertEquals(
            'default',
            LegacyParams::staticGetParam($this->routeMatch, $this->request, 'missing', 'default')
        );
    }

    public function testReturnsArrayMergedFromAllSourcesInProperOrderWhenParameterNameOmitted()
    {
        $legacyParams = new LegacyParams($this->routeMatch, $this->request);

        $expected = array(
            'all'            => 'route',
            'query_and_post' => 'query',
            'post_only'      => 'post',
        );

        $this->assertEquals($expected, $legacyParams->getParam());
        $this->assertEquals(
            $expected,
            LegacyParams::staticGetParam($this->routeMatch, $this->request)
        );
    }
}

