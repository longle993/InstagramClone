<?php

require_once "../../vendor/autoload.php";
require_once "../../core/rest_init.php";
use models\User;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
$user = new User();
if(isset($_GET["username"])) {
    $username = trim(htmlspecialchars($_GET["username"]));
    if($user->fetchUser("username", $username)) {
        echo json_encode(array(
            "user"=>$user,
            "success"=>true
        ));
    } else {
        echo json_encode(array(
            "success"=>false
        ));
    }
}