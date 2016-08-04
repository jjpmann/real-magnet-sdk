<?php

namespace RealMagnet;

class RealMagnetClient extends \GuzzleHttp\Client
{
    protected $url = 'https://dna.magnetmail.net/ApiAdapter/Rest/';

    public function __construct()
    {
        $config = [
            'base_uri' => $this->url,
            'timeout' => 5.0, // seconds
            'defaults' => [
                'headers' => [
                    'content-type' => 'application/json',
                    'Accept' => 'application/json',
                ],
           ],
        ];

        parent::__construct($config);
    }
}
