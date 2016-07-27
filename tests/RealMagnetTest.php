<?php

namespace RealMagnet;

use \Mockery as m;

class RealMagnetTest extends \PHPUnit_Framework_TestCase
{

    // Mocked Client
    protected $client;

    protected function setUp()
    {
        $this->client   = m::mock('RealMagnet\RealMagnetClient');
        $this->response = m::mock('GuzzleHttp\Psr7\Response');
    }

    public function tearDown()
    {
    }

    public function testClassCreate()
    {
        $rm = new RealMagnet(null, null, $this->client);
        $this->assertInstanceOf('RealMagnet\RealMagnet', $rm);
    }

    public function testCreateException()
    {
        $this->setExpectedException('RealMagnet\RealMagnetException');
        $rm = new RealMagnet(false, false, $this->client);
    }

    public function testAuthenticateThrowsException()
    {
        $this->response
            ->shouldReceive('getBody')
            ->andReturn(false);
        
        $this->client
            ->shouldReceive('request')
            ->andReturn($this->response);

        $this->setExpectedException('RealMagnet\RealMagnetException');
        $rm = new RealMagnet(null, null, $this->client);
        $rm->init();
    }

    public function testAuthenticatePasses()
    {
        $this->response
            ->shouldReceive('getBody')
            ->andReturn(json_encode(['SessionID' => 123]));
        
        $this->client
            ->shouldReceive('request')
            ->andReturn($this->response);

        $rm = new RealMagnet(null, null, $this->client);
        $this->assertInstanceOf('RealMagnet\RealMagnet', $rm->init());
    }

    public function testGetGroups()
    {   

        $groups = [
            ['GroupID' => 1, 'GroupName' => 'one'],
            ['GroupID' => 2, 'GroupName' => 'two'],
        ];

        $this->response
            ->shouldReceive('getBody')
            ->andReturn(json_encode($groups));
        
        $this->client->SessionId = 123;
        $this->client->UserId = 123;

        $this->client
            ->shouldReceive('request')
            ->andReturn($this->response);

        $rm = new RealMagnet(null, null, $this->client);
        $this->assertEquals($groups, $rm->getGroups());

    }
}
