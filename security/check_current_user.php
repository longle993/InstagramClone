<?php
require_once "../vendor/autoload.php";
require_once "../core/init.php";
require_once "../functions/sanitize_id.php";
use models\User;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
$id = isset($_POST["current_user_id"]) ? $_POST["current_user_id"] : false;
if($id = sanitize_id($id)) {
    if(User::user_exists("id", $id) && $user->getPropertyValue("id") == $id) {
        echo json_encode(1);
    } else {
        echo json_encode(0);
    }
} else {
    echo json_encode(0);
}