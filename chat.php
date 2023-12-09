<?php

require_once "vendor/autoload.php";
require_once "core/init.php";

use classes\{Validation, Common, Session, Token, Hash, Redirect};
use models\{Post, UserRelation, Message, User};
use layouts\chat\ChatComponent;

// if(!$user->getPropertyValue("isLoggedIn")) {
//     Redirect::to("login/login.php");
// }

$welcomeMessage = '';
if(Session::exists("register_success") && $user->getPropertyValue("username") == Session::get("new_username")) {
    $welcomeMessage = Session::flash("new_username") . ", " . Session::flash("register_success");
}
$current_user_id = $user->getPropertyValue("id");
$username = isset($_GET["username"]) ? trim(htmlspecialchars($_GET["username"])) : '';

if(!($user->getPropertyValue("username") == $username) && $username != "") {
    $fetched_user = new User();
    if($fetched_user->fetchUser("username", $username)) {
    }
} else {
    $fetched_user = $user;
}
$username = $user->getPropertyValue("username");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inbox</title>
    <link rel="stylesheet" href="public/css/global.css">
    <link rel="stylesheet" href="public/css/chat.css">
    <link rel="stylesheet" href="public/css/master-left-panel.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="public/javascript/config.js" defer></script>
    <script src="public/javascript/global.js" defer></script>
    <script src="public/javascript/chat.js" defer></script>
</head>
<body>
<main>
    <div id="global-container">
        <?php include_once "page_parts/basic/master-left.php"; ?>
        <div id="master-middle">
            <div id="chat-global-container">
                <div id="first-chat-part" class="relative">
                    <div class="flex">
                        <div class="section-title" style="padding-left: 20px;"><h3 class="title-style-3">@<?php echo $username; ?></h3></div>
                        <div>
                            <?php include_once "page_parts/chat/friends-chat-search-container.php";?>
                        </div>         
                    </div>
                    <div class="section-title" style="margin: 15px;">Messages</div>
                    <div id="friend-chat-discussions-container">
                        <?php
                            $discussions = Message::get_discussions($current_user_id);
                            $temp = array();
                            $result = array();
                            foreach($discussions as $discussion) {
                                $current_disc = array(
                                    "sender"=>$discussion->message_receiver,
                                    "receiver"=>$discussion->message_creator
                                );
                                if(in_array($current_disc, $temp)) {
                                    continue; 
                                }
                                $temp[] = array(
                                    "sender"=>$discussion->message_creator,
                                    "receiver"=>$discussion->message_receiver
                                );
                                $result[] = $discussion;
                            }
                            foreach($result as $discussion) {
                                $chat_comp = new ChatComponent();

                                echo $chat_comp->generate_discussion($current_user_id, $discussion);
                            }
                        ?>
                    </div>
                </div>
                <div id="no-discussion-yet">
                    <div class="flex-justify-column" style="text-align: center">
                        <h2>Start a new conversation</h2>
                        <a href="" class="new-message-button">New Message</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
</body>
</html>