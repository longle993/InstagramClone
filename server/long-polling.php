<?php
require_once "../vendor/autoload.php";
require_once "../core/init.php";
use classes\DB;
use models\{User, Message};
use layouts\chat\ChatComponent;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
session_write_close();
ignore_user_abort(true);
set_time_limit(0);
$current_user_id = $user->getPropertyValue("id");
$receiver = isset($_POST["receiver"]) ? $_POST["receiver"] : null;
while(true) {
    $channel_buffer = DB::getInstance()->query("SELECT * FROM `channel` WHERE sender = ? AND receiver = ?", array($receiver, $current_user_id))->results();
    $isEmpty = empty($channel_buffer);
    if(!$isEmpty) {
        $content = '';
        $chat_component = new ChatComponent();
        foreach($channel_buffer as $message) {
            $sender_user = new User();
            $sender_user->fetchUser("id", $message->sender);
            $msg = new Message();
            $msg->get_message("id", $message->message_id);
            $msg_obj = Message::get_message_obj("id", $message->message_id);
            $is_reply = $msg->get_property("reply_to");
            if($is_reply) {
                $original_message_id = $msg->get_property("reply_to");
                $reply_message_id = $msg->get_property("id");
                $reply_creator = $msg->get_property("message_sender");
                $original_message = new Message();
                $msg->get_message("id", $original_message_id);
                $original_creator = $original_message->get_property("message_sender");
                $content .= $chat_component->generate_received_reply_message($original_message_id, $reply_message_id, $original_creator, $reply_creator);
            } else {
                $content .= $chat_component->generate_friend_message($sender_user, $msg_obj, $msg->get_property("message_date"));
            }
        }
        echo json_encode($content);
        Message::dump_channel($receiver, $current_user_id);
        break;
    }
    usleep(10000);
}