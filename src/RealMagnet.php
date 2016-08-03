<?php

namespace RealMagnet;

use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use RealMagnet\RealMagnetResponse as Resp;

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

    private function unknown($method)
    {
        return Resp::error([], "Unknown error: {$method}", true);
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
            //die('You must provide a valid username and password');
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
        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            throw new RealMagnetException('There was an issue connecting with the API. Error: ' . $e->getMessage());
            return false;
        }

        return  json_decode($response->getBody(), true);
    }

    // ------------------------------------------------------------------------

    public function addRecipient($user)
    {

        // AddRecipient
        $user = new Collection($user);

        $body = $user->map(function($v, $k){
            return [ucfirst($k) => $v];
        })->collapse()->toArray();

        // $body = [
        //     'Email'     => $user->email,
        //     'FirstName' => $user->firstName,
        //     'LastName'  => $user->lastName,
        //     'Groups'    => $user->groups,
        // ];

        $data = $this->setBody($body)->call('AddRecipient');

        if ($data['Error']) {
            $msg = $data['Message'] ? $data['Message'] : $data['ErrorObject']['ErrorCode'];
            return Resp::error([], $msg, $data['Error']);
        }

        if (isset($data['AdditionalParams'])) {
            $user['id'] = $data['AdditionalParams'];
            return Resp::success(new Collection($user), $data['Message']);
        }

       return $this->unknown('AddRecipient');
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
        $user = new Collection($user);

        $body = $user->map(function($v, $k){
            return [ucfirst($k) => $v];
        })->collapse()->toArray();

        if (!count($body) > 0) {
            return Resp::error([], 'No fields were defined in the search.');
        }
        
        $data = $this->setBody($body)->call('SearchRecipient');

        if (!count($data) > 0) {
            return Resp::error([], 'No recipients were found.');
        }

        if (count($data)) {

            $users = (new Collection($data))->map(function($user){
                return new Collection($user);
            });

            return Resp::success($users, 'Found: ' . count($data));
        }

        return $this->unknown('SearchRecipient');
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

    public function editRecipientGroups($id = false, $newGroups = [], $removeGroups = [])
    {
        // EditRecipientGroups
        $body = [
            'ID'        => $id,
            'NewGroups' => $newGroups,
            'UnsubscribeGroups' => $removeGroups
        ];

        if (!$id || !is_int($id)) {
            return Resp::error([], 'A recipient ID was not defined.');
        }

        try {
            $data = $this->setBody($body)->call('EditRecipientGroups');
        } catch (RealMagnetException $e) {
            return Resp::error([], 'Error updating recipient');
        }

        if (isset($data['Status'])) {
            if ($data['Status'] === 1) {
                return Resp::success([], 'Action has been completed.');
            }
            if ($data['Status'] === 0) {
                //'An error has occurred. Please check your parameters and make sure that they are passed correctly. If the paramaters are correct, then please contact support@realmagnet.com'
                return Resp::error([], 'Error updating recipient');
            }
        }

        return $this->unknown('EditRecipientGroups');
    }

    public function getRecipientFields()
    {
        // GetRecipientFields
        $data = $this->setBody()->call('GetRecipientFields');

        if ($data['CustomFields']) {

            //echo "<pre>".__FILE__.'<br>'.__METHOD__.' : '.__LINE__."<br><br>"; var_dump( $data['CustomFields'] ); exit;
            $fields = (new Collection($data['CustomFields']))->map(function($field){
                return [$field['FieldName'] => $field['Label']];
            })->collapse();

            return Resp::success($fields, $data['Message']);
        }

        return $this->unknown('GetRecipientFields');
    }

    public function getGroups()
    {
        // GetGroups
        $body = [
            'DisplayStatus'     => 2,
            'SubscriptionGroup' => 2,
        ];

        $data = $this->setBody($body)->call('GetGroups');

        if (is_array($data)) {
            $msg = count($data) > 0 ? 'Groups: ' . count($data) : 'No Groups';
            return Resp::success(new Collection($data), $msg);
        }
        
        return $this->unknown('GetGroups');
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

    public function getGroupDetails($groupId = false)
    {
        // GetGroupDetails
        if (!$groupId) {
            return Resp::error([], 'A group ID was not defined.');
        }

        $body = [
            'GroupID' => $groupId,
        ];

        try {
            $data = $this->setBody($body)->call('GetGroupDetails');
        } catch (RealMagnetException $e) {
            return Resp::error([], 'Bad call to GetGroupDetails.');
        }

        if ($data) {
            return Resp::success(new Collection($data), 'Group: ' . $data['GroupName']);
        }

        return $this->unknown('GetGroupDetails');
    }

    // ------------------------------------------------

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

}
