<?php

require_once "../../vendor/autoload.php";
require_once "../../core/rest_init.php";
use models\{User, Follow};
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
require_once "../../functions/sanitize_id.php";
if(!isset($_POST["current_user_id"])) {
    echo json_encode(
        array(
            "message"=>"Error",
            "success"=>false
        )
    );
    exit();
}
if(!isset($_POST["current_profile_id"])) {
    echo json_encode(
        array(
            "message"=>"Error",
            "success"=>false
        )
    );
    exit();
}
$follower = $_POST["current_user_id"];
$followed = $_POST["current_profile_id"];
if($follower === $followed) {
    echo json_encode(
        array(
            "message"=>"Error",
            "success"=>false
        )
    );
    exit();
}
if(($follower = sanitize_id($follower)) && 
    User::user_exists("id", $follower)) {
        if(isset($followed) && 
            ($followed = sanitize_id($followed)) && 
            User::user_exists("id", $followed)) {
                if(Follow::follow_exists($follower, $followed)) {
                    echo json_encode(
                        array(
                            "message"=>"Error",
                            "success"=>false
                        )
                    );
                } else {
                    $follow = new Follow();
                    $follow->set_data(array(
                        "follower"=>$follower,
                        "followed"=>$followed
                    ));
                    $follow->add();
                    echo json_encode(
                        array(
                            "message"=>"Success",
                            "success"=>true
                        )
                    );
                }
        } else {
            echo json_encode(
                array(
                    "message"=>"Error",
                    "success"=>false
                )
            );
        }
} else {
    echo json_encode(
        array(
            "message"=>"Error",
            "success"=>false
        )
    );
}