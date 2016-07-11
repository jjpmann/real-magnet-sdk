<?php

namespace RealMagnet;

use GuzzleHttp\Client;

class RealMagnet
{

    protected $url = 'https://dna.magnetmail.net/ApiAdapter/Rest/'

    protected $username;

    protected $password;

    protected $session;

    protected $client;

    public function __construct($username, $password) 
    {
        $this->username = $username;
        $this->password = $password;

        $this->client = new Client([
            // Base URI is used with relative requests
            'base_uri' => $this->url,
            // You can set any number of default request options.
            'timeout'  => 2.0,
        ]);

    }

    protected function authenticate()
    {
        $response = $this->client->request('POST', 'Authenticate');

        echo "<pre>".__FILE__.'<br>'.__METHOD__.' : '.__LINE__."<br><br>"; var_dump( $response ); exit;
        
    }

    public function getGroups()
    {
        // GetGroups
    }

    public function addGroup()
    {
        // 'AddGroup'
    }

    public function getGroupCategories()
    {
        // GetGroupCategories
    }

    public function getGroupDetails()
    {
        // GetGroupDetails
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