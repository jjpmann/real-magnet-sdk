<?php

namespace RealMagnet;

use GuzzleHttp\Client;

class RealMagnet
{
    // https://dna.magnetmail.net/ApiAdapter/Rest/help

    protected $username;

    protected $password;

    protected $client;

    protected $options = [
        'headers' => [
            'content-type' => 'application/json',
            'Accept'       => 'application/json',
        ],
    ];

    // ----

    protected $session;

    public function __construct($username = null, $password = null, RealMagnetClient $client = null)
    {
        if (is_null($username)) {
            $username = getenv('REALMAGNET_USERNAME');
        }
        if (is_null($password)) {
            $password = getenv('REALMAGNET_PASSWORD');
        }

        if (!$username or !$password) {
            throw new RealMagnetException('You must provide a valid username and password');
        }

        $this->username = $username;
        $this->password = $password;

        if (is_null($client)) {
            $client = new RealMagnetClient();
        }

        $this->client = $client;

    }

    public function init()
    {
        return $this->authenticate();
    }

    protected function authenticate()
    {
        $this->options['body'] = json_encode([
            'UserName' => $this->username,
            'Password' => $this->password,
        ]);

        $this->session = $this->call('Authenticate');

        if (!$this->session['SessionID']) {
            throw new RealMagnetException('You must provide a valid username and password', 1);
            die('You must provide a valid username and password');

            return false;
        }

        return $this;
    }

    protected function setBody(array $data = [])
    {
        $body = array_merge([
            'SessionID' => $this->SessionID,
            'UserID'    => $this->UserID,
        ], $data);

        $this->options['body'] = json_encode($body);

        return $this;
    }

    public function __get($var)
    {
        if (isset($this->session[$var])) {
            return $this->session[$var];
        }
    }

    protected function call($method)
    {
        try {
            $response = $this->client->request('POST', $method, $this->options);
        // } catch (\GuzzleHttp\Exception\ClientException $e) {
        //     echo('Error');
        //     throw new RealMagnetException("Error Processing Request", 1);
        //     return false;
        // } catch (\GuzzleHttp\Exception\ServerException $e) {
        //     echo('Error');
        //     throw new RealMagnetException("Error Processing Request", 1);
        //     return false;
        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            echo 'Error';
            echo $e->getResponse()->getBody();
            return false;
        }

        return  json_decode($response->getBody(), true);
    }

    public function editRecipient($id, $user)
    {
        // EditRecipient
        $body = [
            'ID'        => $id,
            'Email'     => $user->email,
            'FirstName' => $user->firstName,
            'LastName'  => $user->lastName,
            'Groups'    => $user->groups,
        ];

        $data = $this->setBody($body)->call('EditRecipient');

        return $data;
    }

    public function searchRecipients($user)
    {
        // SearchRecipient
        $body = [
            'Email'     => $user->email,
        ];

        $data = $this->setBody($body)->call('SearchRecipient');

        return $data;
    }

    public function getGroupCategories()
    {
        // GetGroupCategories

        $data = $this->setBody()->call('GetGroupCategories');

        return $data;
    }

    public function getMessageCategories()
    {
        // GetMessageCategory

        $data = $this->setBody()->call('GetMessageCategory');

        return $data;
    }

     /*
        Call this method to see which groups a recipient belongs to.
        return array of users if they have groups with ids of groups they belong to
     */
     public function getRecipientGroups($ids)
     {
         // GetRecipientGroups
        $body = [
            'RecipientIds' => $ids,
        ];

         $data = $this->setBody($body)->call('GetRecipientGroups');

         return $data;
     }

    // public function deleteRecipient($id)
    // {
    //     // DeleteRecipient
    //     $body = [
    //         'ID'        => $id,
    //     ];

    //     $data = $this->setBody($body)->call('RemoveRecipient');

    //     return $data;
    // }

    public function addRecipient($user)
    {

        // AddRecipient
        $body = [
            'Email'     => $user->email,
            'FirstName' => $user->firstName,
            'LastName'  => $user->lastName,
            'Groups'    => $user->groups,
        ];

        $data = $this->setBody($body)->call('AddRecipient');

        /*
        'AdditionalParams' => string '2744024701' (length=10)
        'Error' => int 1005
        'ErrorObject' => 
          array (size=5)
            'ErrorCode' => string '' (length=0)
            'ErrorDetails' => string '' (length=0)
            'ErrorID' => int 0
            'ErrorMessage' => string '' (length=0)
            'ErrorType' => string '' (length=0)
        'Message' => string 'RECIPIENT_EXISTS' (length=16)

        array (size=4)
          'AdditionalParams' => string '2766465601' (length=10)
          'Error' => int 0
          'ErrorObject' => 
            array (size=5)
              'ErrorCode' => string '' (length=0)
              'ErrorDetails' => string '' (length=0)
              'ErrorID' => int 0
              'ErrorMessage' => string '' (length=0)
              'ErrorType' => string '' (length=0)
          'Message' => string 'Recipient added successfully' (length=28)
        */

        return $data;
    }

    public function getSubscribers()
    {
        // {
        //     "SessionID":"String content",
        //     "UserID":"String content",
        //     "EndDate":"String content",
        //     "Groups":[2147483647],
        //     "ReportType":"String content",
        //     "StartDate":"String content"
        // }

        // GetSubscribers
        $body = [];

        $data = $this->setBody($body)->call('GetSubscribers');

        return $data;
    }

    public function getGroups()
    {
        // GetGroups
        $body = [
            'DisplayStatus'     => 2,
            'SubscriptionGroup' => 2,
        ];

        $data = $this->setBody($body)->call('GetGroups');

    

        return $data;
    }

    protected function response($data, $msg)
    {

    }

    public function getRecipientFields()
    {
        // GetRecipientFields
        $data = $this->setBody()->call('GetRecipientFields');

        return $data;
    }

    public function subscribeRecipient($user)
    {
        // {
        //     "SessionID":"String content",
        //     "UserID":"String content",
        //     "Category":2147483647,
        //     "Group":2147483647,
        //     "ID":9223372036854775807,
        //     "RemoveAllUnsubscribes":true
        // }
        // SubscribeRecipient
        $body = [
            'GroupName' => $name,
        ];

        $data = $this->setBody($body)->call('AddGroup');
    }

    public function addGroup($name)
    {
        // 'AddGroup'
        $body = [
            'GroupName' => $name,
        ];

        $data = $this->setBody($body)->call('AddGroup');

        /*
        array(4) {
          ["AdditionalParams"]=>
          string(7) "3449447"
          ["Error"]=>
          int(0)
          ["ErrorObject"]=>
          array(5) {
            ["ErrorCode"]=>
            string(0) ""
            ["ErrorDetails"]=>
            string(0) ""
            ["ErrorID"]=>
            int(0)
            ["ErrorMessage"]=>
            string(0) ""
            ["ErrorType"]=>
            string(0) ""
          }
          ["Message"]=>
          string(38) "The group has successfully been added!"
        }
         */

        return $data;
    }

    public function getGroupDetails($groupId)
    {
        // GetGroupDetails
        $body = [
            'GroupID' => $groupId,
        ];

        $data = $this->setBody($body)->call('GetGroupDetails');

        return $data;
    }
}

/*

Uri Method  Description
AddGroup/   POST    Service at https://dna.magnetmail.net/ApiAdapter/Rest/AddGroup/
AddRecipient/   POST    Service at https://dna.magnetmail.net/ApiAdapter/Rest/AddRecipient/
Authenticate/   POST    Service at https://dna.magnetmail.net/ApiAdapter/Rest/Authenticate/
CreateDynamicContentBlock/  POST    Service at https://dna.magnetmail.net/ApiAdapter/Rest/CreateDynamicContentBlock/
CreateDynamicContentElement/    POST    Service at https://dna.magnetmail.net/ApiAdapter/Rest/CreateDynamicContentElement/
CreateMessage/  POST    Service at https://dna.magnetmail.net/ApiAdapter/Rest/CreateMessage/
EditDynamicContentElement/  POST    Service at https://dna.magnetmail.net/ApiAdapter/Rest/EditDynamicContentElement/
EditMessage/    POST    Service at https://dna.magnetmail.net/ApiAdapter/Rest/EditMessage/
EditRecipient/  POST    Service at https://dna.magnetmail.net/ApiAdapter/Rest/EditRecipient/
EditRecipientGroups/    POST    Service at https://dna.magnetmail.net/ApiAdapter/Rest/EditRecipientGroups/
ExecuteReport/  POST    This method is currently in BETA.
GetDetailedTracking/    POST    Service at https://dna.magnetmail.net/ApiAdapter/Rest/GetDetailedTracking/
GetDynamicContentBlocks/    POST    Service at https://dna.magnetmail.net/ApiAdapter/Rest/GetDynamicContentBlocks/
GetDynamicContentElementDetails/    POST    Service at https://dna.magnetmail.net/ApiAdapter/Rest/GetDynamicContentElementDetails/
GetDynamicContentElements/  POST    Service at https://dna.magnetmail.net/ApiAdapter/Rest/GetDynamicContentElements/
GetGroupCategories/ POST    Service at https://dna.magnetmail.net/ApiAdapter/Rest/GetGroupCategories/
GetGroupDetails/    POST    Service at https://dna.magnetmail.net/ApiAdapter/Rest/GetGroupDetails/
GetGroupRecipients/ POST    Service at https://dna.magnetmail.net/ApiAdapter/Rest/GetGroupRecipients/
GetGroups/  POST    Service at https://dna.magnetmail.net/ApiAdapter/Rest/GetGroups/
GetMessageCategory/ POST    Service at https://dna.magnetmail.net/ApiAdapter/Rest/GetMessageCategory/
GetMessageDetails/  POST    Service at https://dna.magnetmail.net/ApiAdapter/Rest/GetMessageDetails/
GetMessageLinkTracking/ POST    Service at https://dna.magnetmail.net/ApiAdapter/Rest/GetMessageLinkTracking/
GetMessageList/ POST    Service at https://dna.magnetmail.net/ApiAdapter/Rest/GetMessageList/
GetRecipientFields/ POST    Service at https://dna.magnetmail.net/ApiAdapter/Rest/GetRecipientFields/
GetRecipientGroups/ POST    Service at https://dna.magnetmail.net/ApiAdapter/Rest/GetRecipientGroups/
GetRecipientHistory/    POST    Service at https://dna.magnetmail.net/ApiAdapter/Rest/GetRecipientHistory/
GetReportMetaData/  POST    This method is currently in BETA.
GetReports/ POST    This method is currently in BETA.
GetSubscribers/ POST    Service at https://dna.magnetmail.net/ApiAdapter/Rest/GetSubscribers/
GetSuppressedRecipients/    POST    Service at https://dna.magnetmail.net/ApiAdapter/Rest/GetSuppressedRecipients/
GetUnsubscribes/    POST    Service at https://dna.magnetmail.net/ApiAdapter/Rest/GetUnsubscribes/
GetUploadStatus/    POST    Service at https://dna.magnetmail.net/ApiAdapter/Rest/GetUploadStatus/
SearchRecipient/    POST    Service at https://dna.magnetmail.net/ApiAdapter/Rest/SearchRecipient/
SendEmailToGroup/   POST    Service at https://dna.magnetmail.net/ApiAdapter/Rest/SendEmailToGroup/
SendEmailToGroupWithProfile/    POST    Service at https://dna.magnetmail.net/ApiAdapter/Rest/SendEmailToGroupWithProfile/
SendEmailToIndividual/  POST    Service at https://dna.magnetmail.net/ApiAdapter/Rest/SendEmailToIndividual/
SubscribeRecipient/ POST    Service at https://dna.magnetmail.net/ApiAdapter/Rest/SubscribeRecipient/
UnsubscribeRecipient/   POST    Service at https://dna.magnetmail.net/ApiAdapter/Rest/UnsubscribeRecipient/
UploadRecipients/   POST    Service at https://dna.magnetmail.net/ApiAdapter/Rest/UploadRecipients/
UpsertRecipient/    POST    Service at https://dna.magnetmail.net/ApiAdapter/Rest/UpsertRecipient/
 */
