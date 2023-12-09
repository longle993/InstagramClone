<?php

require_once "../../vendor/autoload.php";
require_once "../../core/rest_init.php";

use models\{User, Follow};
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
require_once "../../functions/sanitize_id.php";
if(!isset($_GET["user_id"])) {
    echo json_encode(
        array(
            "message"=>"Error",
            "success"=>false
        )
    );
    exit();
}
$user_id = $_GET["user_id"];
if(($user_id = sanitize_id($_GET["user_id"])) && 
    User::user_exists("id", $user_id)) {
        $followers = Follow::get_user_followers($user_id);
        if(count($followers) > 0) {
            $followers = json_encode($followers);
            echo json_encode(
                array(
                    "followers"=>$followers,
                    "message"=>"Success",
                    "success"=>true
                )
            );
        } else {
            echo json_encode(
                array(
                    "followers"=>null,
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