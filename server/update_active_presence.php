<?php
require_once "../vendor/autoload.php";
require_once "../core/init.php";
header("Access-Control-Allow-Origin: *");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
$user->update_active();
