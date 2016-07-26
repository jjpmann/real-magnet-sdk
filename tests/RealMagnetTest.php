<?php

namespace RealMagnet;

class RealMagnetTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
    }

    public function tearDown()
    {
    }

    public function testClassCreate()
    {
        $rm = new RealMagnet('username', 'password');
        $this->assertInstanceOf('RealMagnet\RealMagnet', $rm);
    }

    public function testCreateException()
    {
        $this->setExpectedException('RealMagnet\RealMagnetException');
        $rm = new RealMagnet();
    }
}
