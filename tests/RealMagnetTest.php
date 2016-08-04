<?php

namespace RealMagnet;

use Illuminate\Support\Collection;
use Mockery as m;

class RealMagnetTest extends \PHPUnit_Framework_TestCase
{
    // Mocked Client
    protected $client;

    protected function setUp()
    {
        $this->client = m::mock('RealMagnet\RealMagnetClient');
        $this->response = m::mock('GuzzleHttp\Psr7\Response');
    }

    public function tearDown()
    {
    }

    private function buildCall($resp)
    {
        $this->response
            ->shouldReceive('getBody')
            ->andReturn($resp);

        $this->client
            ->shouldReceive('request')
            ->andReturn($this->response);
    }

    private function assertResponseTrue($raw, $message, $coll)
    {
        $this->assertResponse($raw, $message, $coll, true);
    }

    private function assertResponseFalse($raw, $message, $coll)
    {
        $this->assertResponse($raw, $message, $coll, false);
    }

    private function assertResponse($raw, $message, $coll, $resp = true)
    {
        if ($resp) {
            $this->assertTrue($coll->isSuccessful());
        } else {
            $this->assertfalse($coll->isSuccessful());
        }

        $this->assertEquals($message, $coll->message);
        $this->assertEquals($raw, $coll->toJson());
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
        $this->buildCall(json_encode(['SessionID' => 123]));

        $rm = new RealMagnet(null, null, $this->client);
        $this->assertInstanceOf('RealMagnet\RealMagnet', $rm->init());
    }

    public function testGetGroups()
    {
        $groups = new Collection([
            ['GroupID' => 1, 'GroupName' => 'one'],
            ['GroupID' => 2, 'GroupName' => 'two'],
        ]);
        $msg = 'Groups: 2';

        $this->buildCall($groups->toJson());

        $rm = new RealMagnet(null, null, $this->client);
        $coll = $rm->getGroups();

        $this->assertResponseTrue($groups->toJson(), $msg, $coll);
    }

    public function testGetNoGroups()
    {
        $groups = new Collection([]);
        $msg = 'No Groups';

        $this->buildCall($groups->toJson());

        $rm = new RealMagnet(null, null, $this->client);
        $coll = $rm->getGroups();

        $this->assertResponseTrue($groups->toJson(), $msg, $coll);
    }

    public function testGetGroupDetails()
    {
        $raw = '{"DisplayStatus":true,"GroupCreated":"\/Date(1470243619063-0400)\/","GroupID":3509753,"GroupName":"lmo.com","LastUpdated":"\/Date(-62135578800000-0500)\/","SubscriptionGroup":false,"TotalEmailSuppressed":0,"TotalInGroup":0,"TotalUnsubscribed":0}';
        $message = 'Group: lmo.com';
        $id = 3509753;

        $this->buildCall($raw);

        $rm = new RealMagnet(null, null, $this->client);
        $coll = $rm->getGroupDetails($id);

        $this->assertResponseTrue($raw, $message, $coll);
    }

    public function testBadGroupId()
    {
        $raw = '[]';
        $msg = 'Bad call to GetGroupDetails.';
        $id = 'xxx';

        $this->response
            ->shouldReceive('getBody')
            ->andThrow('RealMagnet\RealMagnetException', $msg);

        $this->client
            ->shouldReceive('request')
            ->andReturn($this->response);

        $rm = new RealMagnet(null, null, $this->client);
        $coll = $rm->getGroupDetails($id);

        $this->assertResponseFalse($raw, $msg, $coll);
    }

    public function testNoGroupId()
    {
        $raw = '[]';
        $msg = 'A group ID was not defined.';
        $id = 0;

        $this->buildCall($raw);

        $rm = new RealMagnet(null, null, $this->client);
        $coll = $rm->getGroupDetails($id);

        $this->assertResponseFalse($raw, $msg, $coll);
    }

    public function testAddUserAsArray()
    {
        $raw = '{"AdditionalParams":"2778673272","Error":0,"ErrorObject":{"ErrorCode":"","ErrorDetails":"","ErrorID":0,"ErrorMessage":"","ErrorType":""},"Message":"Recipient added successfully"}';
        $msg = 'Recipient added successfully';
        $user = [
            'firstName' => 'First',
            'lastName'  => 'Last',
            'email'     => 'email2@domain.com',
        ];

        $resp = new Collection($user);
        $resp['id'] = '2778673272';

        $this->buildCall($raw);

        $rm = new RealMagnet(null, null, $this->client);
        $coll = $rm->addRecipient($user);

        $this->assertResponseTrue($resp->toJson(), $msg, $coll);
        $this->assertEquals($resp['id'], $coll->data->get('id'));
    }

    public function testAddUserAsObj()
    {
        $raw = '{"AdditionalParams":"2778673272","Error":0,"ErrorObject":{"ErrorCode":"","ErrorDetails":"","ErrorID":0,"ErrorMessage":"","ErrorType":""},"Message":"Recipient added successfully"}';
        $msg = 'Recipient added successfully';

        $user = new \stdClass();
        $user->firstName = 'First';
        $user->lastName = 'Last';
        $user->email = 'email@gmail.com';

        $resp = new Collection($user);
        $resp['id'] = '2778673272';

        $this->buildCall($raw);

        $rm = new RealMagnet(null, null, $this->client);
        $coll = $rm->addRecipient($user);

        $this->assertResponseTrue($resp->toJson(), $msg, $coll);
    }

    public function testAddDuplicateUser()
    {
        $raw = '{"AdditionalParams":"2778662917","Error":1005,"ErrorObject":{"ErrorCode":"","ErrorDetails":"","ErrorID":0,"ErrorMessage":"","ErrorType":""},"Message":"RECIPIENT_EXISTS"}';
        $msg = 'RECIPIENT_EXISTS';
        $user = [
            'firstName' => 'First',
            'lastName'  => 'Last',
            'email'     => 'email2@domain.com',
        ];

        $this->buildCall($raw);

        $rm = new RealMagnet(null, null, $this->client);
        $coll = $rm->addRecipient($user);

        $this->assertResponseFalse('[]', $msg, $coll);
    }

    public function testBadUser()
    {
        $raw = '{"AdditionalParams":"0","Error":2,"ErrorObject":{"ErrorCode":"NO_EMAIL","ErrorDetails":"Error Details:\nError Category:1Component:Recipient\nFunction:addRecipient","ErrorID":1003,"ErrorMessage":"","ErrorType":"CUSTOM"},"Message":""}';

        $msg = 'NO_EMAIL';
        $user = [
            'firstName' => 'First',
            'lastName'  => 'Last',
        ];

        $this->buildCall($raw);

        $rm = new RealMagnet(null, null, $this->client);
        $coll = $rm->addRecipient($user);

        $this->assertResponseFalse('[]', $msg, $coll);
    }

    public function testEmptySearch()
    {
        $raw = '[]';
        $msg = 'No fields were defined in the search.';
        $user = [];

        $this->buildCall($raw);

        $rm = new RealMagnet(null, null, $this->client);
        $coll = $rm->searchRecipients($user);

        $this->assertResponseFalse($raw, $msg, $coll);
    }

    public function testSearchNoResults()
    {
        $raw = '[]';
        $msg = 'No recipients were found.';
        $user = [
            'firstName' => 'First',
            'lastName'  => 'Last',
        ];

        $this->buildCall($raw);

        $rm = new RealMagnet(null, null, $this->client);
        $coll = $rm->searchRecipients($user);

        $this->assertResponseFalse($raw, $msg, $coll);
    }

    public function testSearchResults()
    {
        $raw = '[{"Address":"","Address2":"","City":"","Company":"","CustomField1":"","CustomField10":"","CustomField11":"","CustomField12":"","CustomField13":"","CustomField14":"","CustomField15":"","CustomField16":"","CustomField17":"","CustomField18":"","CustomField19":"","CustomField2":"","CustomField20":"","CustomField21":"","CustomField22":"","CustomField23":"","CustomField24":"","CustomField25":"","CustomField26":"","CustomField27":"","CustomField28":"","CustomField29":"","CustomField3":"","CustomField30":"","CustomField4":"","CustomField5":"","CustomField6":"","CustomField7":"","CustomField8":"","CustomField9":"","CustomID":"","Email":"test1@domain.com","EmailConfirmed":true,"EmailSendSuppress":true,"Fax":"","FaxSendSupress":true,"FirstName":"First","ID":"2778662917","LastName":"Last","Phone":"","State":"","SuppressedDate":null,"TextOnlyRecipient":false,"Unsubscribed":false,"Zip":""},{"Address":"","Address2":"","City":"","Company":"","CustomField1":"","CustomField10":"","CustomField11":"","CustomField12":"","CustomField13":"","CustomField14":"","CustomField15":"","CustomField16":"","CustomField17":"","CustomField18":"","CustomField19":"","CustomField2":"","CustomField20":"","CustomField21":"","CustomField22":"","CustomField23":"","CustomField24":"","CustomField25":"","CustomField26":"","CustomField27":"","CustomField28":"","CustomField29":"","CustomField3":"","CustomField30":"","CustomField4":"","CustomField5":"","CustomField6":"","CustomField7":"","CustomField8":"","CustomField9":"","CustomID":"","Email":"test2@domain.com","EmailConfirmed":true,"EmailSendSuppress":true,"Fax":"","FaxSendSupress":true,"FirstName":"First","ID":"2778673272","LastName":"Last","Phone":"","State":"","SuppressedDate":null,"TextOnlyRecipient":false,"Unsubscribed":false,"Zip":""},{"Address":"","Address2":"","City":"","Company":"","CustomField1":"","CustomField10":"","CustomField11":"","CustomField12":"","CustomField13":"","CustomField14":"","CustomField15":"","CustomField16":"","CustomField17":"","CustomField18":"","CustomField19":"","CustomField2":"","CustomField20":"","CustomField21":"","CustomField22":"","CustomField23":"","CustomField24":"","CustomField25":"","CustomField26":"","CustomField27":"","CustomField28":"","CustomField29":"","CustomField3":"","CustomField30":"","CustomField4":"","CustomField5":"","CustomField6":"","CustomField7":"","CustomField8":"","CustomField9":"","CustomID":"","Email":"test3@domain.com","EmailConfirmed":true,"EmailSendSuppress":true,"Fax":"","FaxSendSupress":true,"FirstName":"First","ID":"2779717263","LastName":"Last","Phone":"","State":"","SuppressedDate":null,"TextOnlyRecipient":false,"Unsubscribed":false,"Zip":""}]';

        $msg = 'Found: 3';
        $user = [
            'firstName' => 'First',
            'lastName'  => 'Last',
        ];

        $this->buildCall($raw);

        $rm = new RealMagnet(null, null, $this->client);
        $coll = $rm->searchRecipients($user);

        $this->assertResponseTrue($raw, $msg, $coll);
    }

    public function testGetRecipientFields()
    {
        $raw = '{"CustomFields":[{"FieldName":"Email","Label":"Email"},{"FieldName":"First_Name","Label":"First Name"},{"FieldName":"Last_Name","Label":"Last Name"},{"FieldName":"Phone","Label":"Phone"},{"FieldName":"Fax","Label":"Fax"},{"FieldName":"Address","Label":"Address 1"},{"FieldName":"Address2","Label":"Address 2"},{"FieldName":"City","Label":"City"},{"FieldName":"State","Label":"State"},{"FieldName":"Zip","Label":"Zip"},{"FieldName":"Company","Label":"Company"}],"EnhancedFields":[],"Message":"Transaction has been completed successfully!"}';

        $msg = 'Transaction has been completed successfully!';

        $resp = '{"Email":"Email","First_Name":"First Name","Last_Name":"Last Name","Phone":"Phone","Fax":"Fax","Address":"Address 1","Address2":"Address 2","City":"City","State":"State","Zip":"Zip","Company":"Company"}';

        $this->buildCall($raw);

        $rm = new RealMagnet(null, null, $this->client);
        $coll = $rm->getRecipientFields();

        $this->assertResponseTrue($resp, $msg, $coll);
    }

    public function testEditRecipientGroups()
    {
        $raw = '{"Message":"Action has been completed.","Status":1}';

        $msg = 'Action has been completed.';

        $id = 2778662917;

        $this->buildCall($raw);

        $rm = new RealMagnet(null, null, $this->client);
        $coll = $rm->editRecipientGroups($id, [123]);

        $this->assertResponseTrue('{"id":2778662917}', $msg, $coll);
        $this->assertEquals($id, $coll->data->get('id'));
    }

    public function testEditBadRecipientGroup()
    {
        $raw = '{"Message":"An error has occurred. Please check your parameters and make sure that they are passed correctly. If the paramaters are correct, then please contact support@realmagnet.com","Status":0}';

        $msg = 'A recipient ID was not defined.';

        $this->buildCall($raw);

        $id = 0;

        $rm = new RealMagnet(null, null, $this->client);
        $coll = $rm->editRecipientGroups($id);

        $this->assertResponseFalse('[]', $msg, $coll);
    }

    public function testEditRecipientBadGroup()
    {
        $raw = '{"Message":"An error has occurred. Please check your parameters and make sure that they are passed correctly. If the paramaters are correct, then please contact support@realmagnet.com","Status":0}';

        $msg = 'Error updating recipient';

        $this->buildCall($raw);

        $id = 2778662917;

        $rm = new RealMagnet(null, null, $this->client);
        $coll = $rm->editRecipientGroups($id, ['x']);

        $this->assertResponseFalse('[]', $msg, $coll);
    }

    public function testAddGroup()
    {
    }
}
