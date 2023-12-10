<?php
require_once "vendor/autoload.php";
require_once "core/init.php";

use classes\{Validation, Common, Session, Token, Redirect, Hash};
error_reporting();

if(!$user->getPropertyValue("isLoggedIn")) {
    Redirect::to("login/login.php");
}

$welcomeMessage = '';
if(Session::exists("register_success") && $user->getPropertyValue("username") == Session::get("new_username")) {
    $welcomeMessage = Session::flash("new_username") . ", " . Session::flash("register_success");
}

$fullname = $user->getPropertyValue("firstname") . " " . $user->getPropertyValue("lastname");
$username = $user->getPropertyValue("username");
$bio = $user->getPropertyValue("bio");
$picture = $root . (empty($user->getPropertyValue("picture")) ? "public/assets/images/logos/Profile.jpg" : $user->getPropertyValue("picture"));
$profile = $root . "profile.php?username=" . $user->getPropertyValue("username");

$count = 0;

include_once 'functions/sanitize_text.php';
if(isset($_POST["save-changes"])) {
    if(Token::check(Common::getInput($_POST, "token_save_changes"), "saveEdits")) {
        $fn = sanitize_text($_POST["full-name"]);
        $fn = explode(" ", $fn);
        if(count($fn) > 1) {
            $new_firstname = $fn[0];
            $new_lastname = $fn[1];
        } else {
            $new_firstname = $fn[0];
            $new_lastname = "";
        }
        $new_bio = sanitize_text($_POST["bio"]);
        $new_username = sanitize_text($_POST["username"]);
        $validator = new Validation();
        $validator->check($_POST, array(
            "bio"=>array(
                "name"=>"Bio",
                "max"=>500
            )
        ));
        if($new_username != $username) {
            $validator->check($_POST, array(
                "username"=>array(
                    "name"=>"Username",
                    "required"=>true,
                    "unique"=>true,
                    "max"=>255,
                    "min"=>2,
                )
            ));
        }
        if(file_exists($_FILES['avatar']['tmp_name']) && is_uploaded_file($_FILES['avatar']['tmp_name'])) {
            $validator->check($_FILES, array(
                "avatar"=>array(
                    "name"=>"Avatar",
                    "image"=>"image"
                )
            ));
        }
        if($validator->passed()) {
            $user->setPropertyValue("username", $new_username);
            $user->setPropertyValue("firstname", $new_firstname);
            $user->setPropertyValue("lastname", $new_lastname);
            $user->setPropertyValue("bio", $new_bio);
            $profilePicturesDir = 'data/users/' . $username . "/media/pictures/";

            if(file_exists($_FILES['avatar']['tmp_name']) && is_uploaded_file($_FILES['avatar']['tmp_name'])) {
                $generatedName = Hash::unique();
                $generatedName = trim(htmlspecialchars($generatedName));

                $file = $_FILES["avatar"]["name"];
                $original_extension = (false === $pos = strrpos($file, '.')) ? '' : substr($file, $pos);

                $targetFile = $profilePicturesDir . $generatedName . $original_extension;
                if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $targetFile)) {
                    $new_target = 'data/users/' . $new_username . "/media/pictures/" . $generatedName . $original_extension;
                    $user->setPropertyValue("picture", $new_target);
                } else {
                    $validator->addError("Error");
                }
            }
            if($new_username != $username) {
                $old_user_data_dir = __DIR__ . '/data/users/' . $username;
                $new_user_data_dir = __DIR__ . '/data/users/' . $new_username;
                recurse_copy($old_user_data_dir, $new_user_data_dir);
                deleteDir($old_user_data_dir);
                $user->setPropertyValue("username", $new_username);
            }
            $user->update();
            $fullname = $new_firstname . (empty($new_lastname) ? "" : " " . $new_lastname);
            $username = $new_username;
            $bio = $new_bio;
            $picture = $root . $user->getPropertyValue("picture");
            $profile = $root . "profile.php?username=" . $new_username;
        } else {
            foreach($validator->errors() as $error) {
                echo $error . "<br>";
            }
        }
    }
}

function deleteDir($dirPath) {
    if (! is_dir($dirPath)) {
        throw new InvalidArgumentException("Error directory");
    }
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
        $dirPath .= '/';
    }
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            deleteDir($file);
        } else {
            unlink($file);
        }
    }
    rmdir($dirPath);
}

function recurse_copy($src,$dst) {
    $dir = opendir($src); 
    @mkdir($dst);
    while(false !== ( $file = readdir($dir)) ) { 
        if (( $file != '.' ) && ( $file != '..' )) { 
            if ( is_dir($src . '/' . $file) ) { 
                recurse_copy($src . '/' . $file,$dst . '/' . $file); 
            } 
            else { 
                copy($src . '/' . $file,$dst . '/' . $file); 
            } 
        } 
    } 
    closedir($dir); 
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit profile</title>
    <link rel="stylesheet" href="public/css/global.css">
    <link rel="stylesheet" href="public/css/settings.css">
    <link rel="stylesheet" href="public/css/master-left-panel.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="public/javascript/config.js" defer></script>
    <script src="public/javascript/settings.js" defer></script>
</head>
<body>
<main>
    <?php include_once "page_parts/basic/master-left.php"; ?>
    <?php require_once "page_parts/settings/left-panel.php" ?>
    <div id="global-container">
        <div id="setting-master-container">
            <h1 class="no-margin">Edit profile</h1>
            <div class="setting-block-line-separator"></div>
            <div id="profile-asset-wrapper" class="flex">
                <a href="<?php echo $profile; ?>" id="assets-wrapper">
                    <div class="flex padding16">
                        <div id="setting-picture-container" style="margin-right: 10px">
                            <img src="<?php echo $picture; ?>" alt="avatar" id="setting-picture">
                        </div>
                        <div>
                            <h3 class="no-margin"><?php echo $fullname; ?></h3>
                            <p class="no-margin">@<?php echo $username; ?></p>
                        </div>
                    </div>
                </a>
                <div id="setting-file-inputs-container">
                    <div style="margin-top: 10px">
                        <label for="avatar-input" class="setting-label" style="font-size: 16px; margin-bottom: 5px">Avatar</label>
                        <input type="file" form="save-form" name="avatar" class="block" id="avatar-input">
                    </div>
                </div>
            </div>
            <div class="flex-column">
                <label for="username" class="setting-label1">Username</label>
                <input type="text" form="save-form" class="setting-input-text-style" value="<?php echo $username; ?>" name="username" id="username">
            </div>
            <div class="flex-column">
                <label for="fullname" class="setting-label1">Nickname</label>
                <input type="text" form="save-form" class="setting-input-text-style" value="<?php echo $fullname; ?>" name="full-name" id="fullname">
            </div>
            <div class="flex-column">
                <label for="bio" class="setting-label1">Add bio</label>
                <textarea form="save-form" spellcheck="false" name="bio" id="bio" class="setting-input-text-style textarea-style"><?php echo $bio; ?></textarea>
            </div>    
            <form action="" method="POST" id="save-form" enctype="multipart/form-data">
                <input type="hidden" name="token_save_changes" value="<?php echo Token::generate("saveEdits"); ?>">
                <input type="submit" value="Submit" name="save-changes" id="save-button">
            </form>
        </div>
    </div>
</main>
</body>
</html>