<?php

namespace Zf2for1Test\Resource;

use Zf2for1_Resource_Zf2 as Zf2Resource;
use PHPUnit_Framework_TestCase as TestCase;

class Zf2Test extends TestCase
{
    public function setUp()
    {
        $this->bootstrap = $this->getMockBuilder('Zend_Application_Bootstrap_Bootstrap')
            ->disableOriginalConstructor()
            ->getMock();

        $this->zf2resource = new Zf2Resource;
        $this->zf2resource->setBootstrap($this->bootstrap);
    }
}
