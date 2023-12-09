<?php

include_once "../../vendor/autoload.php";
require_once "../../core/init.php";
use models\User;
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
$user = new User();
if(isset($_GET["id"]) && 
    is_numeric($id=$_GET["id"]) && 
    $user->fetchUser("id", $id)) {
    echo json_encode($user);
} else {
    echo json_encode(array(
        'problem'=>'Error'
    ));
}
