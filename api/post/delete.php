<?php

require_once "../../vendor/autoload.php";
require_once "../../core/rest_init.php";
use models\{Post, Like, Comment};
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json;");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
require_once "../../functions/sanitize_id.php";
if(!isset($_POST["post_id"])) {
    echo json_encode(array(
        "message"=>"Error",
        "success"=>false
    ));
    return;
}
if(!isset($_POST["post_owner"])) {
    echo json_encode(array(
        "message"=>"Error",
        "success"=>false
    ));
    return;
}

$post_id = sanitize_id($_POST["post_id"]);
$post_owner = sanitize_id($_POST["post_owner"]);

if(Post::exists($post_id)) {
    if($post_owner == Post::get_post_owner($post_id)->post_owner) {
        Like::delete_post_likes($post_id);
        Comment::delete_post_comments($post_id);
        $post = new Post();
        $post->set_property('post_id', $post_id);
        $post->delete();
        $shared_posts = Post::get('post_shared_id', $post_id);
        foreach($shared_posts as $p) {
            $p->set_property('post_shared_id', null);
            $test = $p->update();
        }
        echo json_encode(array(
            "success"=>true,
            "message"=>'Success'
        ));
    } else {
        echo json_encode(array(
            "success"=>false,
            "message"=>'user\'s id is invalide!'
        ));
    }
} else {
    echo json_encode(array(
        "success"=>false,
        "message"=>'post not exists'
    ));
}