<?php

namespace RealMagnet;

class RealMagnetResponseTest extends \PHPUnit_Framework_TestCase
{
    // Mocked Client
    protected $client;

    protected function setUp()
    {
    }

    public function tearDown()
    {
    }

    public function testClassCreate()
    {
        $resp = new RealMagnetResponse('status', 'data');
        $this->assertInstanceOf('RealMagnet\RealMagnetResponse', $resp);
    }

    public function testStaticSuccess()
    {
        $resp = RealMagnetResponse::success('data');
        $this->assertInstanceOf('RealMagnet\RealMagnetResponse', $resp);
        $this->assertTrue($resp->isSuccessful());
    }

    public function testStaticSuccessWithMessage()
    {
        $msg = 'Success Message';

        $resp = RealMagnetResponse::success('data', $msg);
        $this->assertInstanceOf('RealMagnet\RealMagnetResponse', $resp);
        $this->assertTrue($resp->isSuccessful());
        $this->assertEquals($msg, $resp->message);
    }

    public function testStaticError()
    {
        $resp = RealMagnetResponse::error('data');
        $this->assertInstanceOf('RealMagnet\RealMagnetResponse', $resp);
        $this->assertFalse($resp->isSuccessful());
    }

    public function testStaticErrorWithMessage()
    {
        $msg = 'Error Message';

        $resp = RealMagnetResponse::error('data', $msg);
        $this->assertInstanceOf('RealMagnet\RealMagnetResponse', $resp);
        $this->assertFalse($resp->isSuccessful());
        $this->assertEquals($msg, $resp->message);
    }

    public function testGetter()
    {
        $status = 'status';
        $data = 'data';
        $msg = 'message';
        $error = 'error';

        $resp = RealMagnetResponse::respond($status, $data, $msg, $error);
        $this->assertInstanceOf('RealMagnet\RealMagnetResponse', $resp);

        $this->assertEquals($status, $resp->status);
        $this->assertEquals($data, $resp->data);
        $this->assertEquals($msg, $resp->message);
        $this->assertEquals($error, $resp->error);
    }

    public function testPassThruGetter()
    {
        $mock = \Mockery::mock('stdClass');
        $mock->shouldReceive('fakeMethod')->andReturn('fakeResponse');
        $mock->fakeVar = 'fakeValue';

        $resp = RealMagnetResponse::success($mock);

        $this->assertEquals('fakeResponse', $resp->data->fakeMethod());
        $this->assertEquals('fakeValue', $resp->data->fakeVar);
    }

    // public function testCreateException()
    // {
    //     $this->setExpectedException('RealMagnet\RealMagnetException');
    //     $rm = new RealMagnet(false, false, $this->client);
    // }
}
