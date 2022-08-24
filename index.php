<?php
ini_set('display_errors', 'Off');
error_reporting(E_ALL);

include_once 'vendor/autoload.php';

use Weza\Application;

$app = new Application();
$app->route();
