<?php

require_once "../../vendor/autoload.php";
require_once "../../core/rest_init.php";

use models\{Message};
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json;");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
require_once "../../functions/sanitize_id.php";
require_once "../../functions/sanitize_text.php";
$message_id = sanitize_id($_POST["message_id"]);
$is_received = sanitize_id($_POST["is_received"]);
if(Message::exists($message_id)) {
    $message_manager = new Message();
    $message_manager->set_property("id", $message_id);
    if($is_received == 'yes') {
        $message_manager->delete_received_message();
    } else {
        $message_manager->delete_sended_message();
    }
    echo json_encode(array(
        "success"=>true,
        "message"=>'Success'
    ));
} else {
    echo json_encode(array(
        "success"=>false,
        "message"=>'Error'
    ));
}