<?php

require 'vendor/autoload.php';

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

$username = env('username');
$password = env('password');


$rm = RealMagnet();