<?php

require_once "vendor/autoload.php";
require_once "core/init.php";

use classes\{DB, Config, Validation, Common, Session, Token, Hash, Redirect, Cookie};
use models\{User, Post, Follow, UserRelation};
use layouts\post\Post as Post_View;

if(!$user->getPropertyValue("isLoggedIn")) {
    Redirect::to("login/login.php");
}

if(isset($_POST["save-profile-edites"])) {
    if(Token::check(Common::getInput($_POST, "save_token"), "saveEdits")) {
        $validate = new Validation();

        $validate->check($_POST, array(
            "firstname"=>array(
                "name"=>"Firstname",
                "required"=>true,
                "min"=>4,
                "max"=>40
            ),
            "lastname"=>array(
                "name"=>"Lastname",
                "required"=>true,
                "min"=>4,
                "max"=>40
            ),
            "private"=>array(
                "name"=>"Profile (public/private)",
                "range"=>array(-1, 1)
            )
        ));
        
        if(!empty($_FILES["picture"]["name"])) {
            $validate->check($_FILES, array(
                "picture"=>array(
                    "name"=>"Picture",
                    "image"=>"image"
                )
            ));
        }

        if(!empty($_FILES["cover"]["name"])) {
            $validate->check($_FILES, array(
                "cover"=>array(
                    "name"=>"Cover",
                    "image"=>"image"
                )
            ));
        }

        if($validate->passed()) {
            $user->setPropertyValue("firstname", $_POST["firstname"]);
            $user->setPropertyValue("lastname", $_POST["lastname"]);
            $user->setPropertyValue("bio", $_POST["bio"]);
            $user->setPropertyValue("private", $_POST["private"]);

            $profilePicturesDir = 'data/users/' . $user->getPropertyValue("username") . "/media/pictures/";
            $coversDir = 'data/users/' . $user->getPropertyValue("username") . "/media/covers/";

            if(!empty($_FILES["picture"]["name"])) {
                $generatedName = Hash::unique();
                $generatedName = htmlspecialchars($generatedName);
                $file = $_FILES["picture"]["name"];
                $original_extension = (false === $pos = strrpos($file, '.')) ? '' : substr($file, $pos);

                $targetFile = $profilePicturesDir . $generatedName . $original_extension;
                if (move_uploaded_file($_FILES["picture"]["tmp_name"], $targetFile)) {
                    $user->setPropertyValue("picture", $targetFile);
                } else {
                    $validate->addError("Sorry, there was an error uploading your profile picture.");
                }
            }

            if(!empty($_FILES["cover"]["name"])) {
                $generatedName = Hash::unique();
                $generatedName = htmlspecialchars($generatedName);
                
                $file = $_FILES["cover"]["name"];
                $original_extension = (false === $pos = strrpos($file, '.')) ? '' : substr($file, $pos);

                $targetFile = $coversDir . $generatedName . $original_extension;
                if (move_uploaded_file($_FILES["cover"]["tmp_name"], $targetFile)) {
                    $user->setPropertyValue("cover", $targetFile);
                } else {
                    $validate->addError("Sorry, there was an error uploading your profile picture.");
                }
            }

            $user->update();
        } else {
            foreach($validate->errors() as $error) {
                echo $error . "<br>";
            }
        }
    }
}

$username = isset($_GET["username"]) ? trim(htmlspecialchars($_GET["username"])) : '';

if(!($user->getPropertyValue("username") == $username) && $username != "") {
    $fetched_user = new User();
    if($fetched_user->fetchUser("username", $username)) {
        $posts = Post::get("post_owner", $fetched_user->getPropertyValue("id"));
    }
} else {
    $fetched_user = $user;
    $posts = Post::get("post_owner", $user->getPropertyValue("id"));
}

$profile_user_id = $fetched_user->getPropertyValue("id");
$profile_user_picture = Config::get("root/path") . (empty($fetched_user->getPropertyValue("picture")) ? "public/assets/images/logos/logo512.png" : $fetched_user->getPropertyValue("picture"));
$bio = $fetched_user->getPropertyValue('bio');

if(isset($_POST["logout"])) {
    if(Token::check(Common::getInput($_POST, "token_logout"), "logout")) {
        $user->logout();
        Redirect::to("login/login.php");
    }
}

usort($posts, 'post_date_latest_sort');

function post_date_latest_sort($post1, $post2) {
    return ($post1->get_property('post_date') < $post2->get_property('post_date')) ? 0 : (($post1->get_property('post_date') > $post2->get_property('post_date')) ? -1 : 1);
}

function is_dir_empty($dir) {
    return (count(glob("$dir/*")) === 0); 
}

$posts_number = Post::get_posts_number($profile_user_id);
$followers_number = Follow::get_user_followers_number($profile_user_id);
$followed_number = Follow::get_followed_users_number($profile_user_id);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $fetched_user->getPropertyValue("firstname") . " " . $fetched_user->getPropertyValue("lastname"); ?></title>
    <link rel="stylesheet" href="public/css/global.css">
    <link rel="stylesheet" href="public/css/profile.css">
    <link rel="stylesheet" href="public/css/master-left-panel.css">
    <link rel="stylesheet" href="public/css/post.css">
    <link rel="stylesheet" href="public/css/create-post-style.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js" defer></script>
    <script src="public/javascript/config.js" defer></script>
    <script src="public/javascript/profile.js" defer></script>
    <script src="public/javascript/global.js" defer></script>
    <script src="public/javascript/post.js" defer></script>
</head>
<body>
    <?php include_once "page_parts/basic/master-left.php"; ?>
    <main class="relative">
        <div class="relative flex-column">
            <div id="profile-picture-container">
                <div class="relative picture-back-color" style="border-radius: 50%; overflow: hidden">
                    <div class="profile-picture-cnt">
                        <img src="<?php echo $profile_user_picture; ?>" class="profile-picture" alt="">
                    </div>
                </div>
            </div>
        </div>
        <div class="flex">
            <div id="name-and-username-container">
                <h1 class="title-style-3"><?php echo $fetched_user->getPropertyValue("firstname") . " " . $fetched_user->getPropertyValue("lastname"); ?></h1>
                <p class="regular-text-style-1">@<?php echo $fetched_user->getPropertyValue("username"); ?></p>
            </div>
            <div>
                <?php
                    if($fetched_user->getPropertyValue("id") == $user->getPropertyValue("id")) {
                        include_once "page_parts/profile/owner-profile-header.php";
                    } else {
                        include_once "page_parts/profile/contact-header.php";
                    }
                ?>
            </div>
        </div>
        
        <div class="flex">
            <div class="user-info-section row-v-flex">
                <a href="" class="user-info-section-link">
                    <div class="flex">
                        <h2 class="title-style-4"><?php echo $posts_number; ?></h2>
                        <p class="regular-text-style-2">Posts</p>
                    </div>
                </a>
                <a href="" class="user-info-section-link">
                    <div class="flex">
                        <h2 class="title-style-4"><?php echo $followers_number; ?></h2>
                        <p class="regular-text-style-2">Followers</p>
                    </div>
                </a>
                <a href="" class="user-info-section-link">
                    <div class="flex">
                        <h2 class="title-style-4"><?php echo $followed_number; ?></h2>
                        <p class="regular-text-style-2">Following</p>
                    </div>
                </a>
            </div> 
        </div> 
        <p class="regular-text" style="margin-left: 422px; margin-bottom:50px"><?php echo $bio; ?></p>          
        <div class="setting-block-line-separator"></div>
        <div id="master-middle">
            <div id="posts-container">
                <?php 
                    foreach($posts as $post) {
                        $post_view = new Post_View();
                        echo $post_view->generate_post($post, $user);
                    }
                ?>
            </div>
        </div>
    </main>
</body>
</html>