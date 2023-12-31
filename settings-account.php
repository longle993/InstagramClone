<?php
require_once "vendor/autoload.php";
require_once "core/init.php";

use classes\{Validation, Hash, Common, Token, Redirect};
use models\User;

if(!$user->getPropertyValue("isLoggedIn")) {
    Redirect::to("login/login.php");
}

$save_success_message = '';
$save_failure_message = '';

$username = $user->getPropertyValue("username");
$picture = $root . (empty($user->getPropertyValue("picture")) ? "assets/images/logos/Chatapp.png" : $user->getPropertyValue("picture"));
$profile = $root . "profile.php?username=" . $user->getPropertyValue("username");
$email = $user->getPropertyValue("email");

include_once 'functions/sanitize_text.php';

if(isset($_POST["save-changes"])) {
    if(Token::check(Common::getInput($_POST, "token_save_changes"), "saveEdits")) {
        $validator = new Validation();
        $validator->check($_POST, array(
            "email"=>array(
                "name"=>"Email",
                "required"=>true,
                "email"=>true
            ),
            "password"=>array(
                "name"=>"Password",
                "required"=>true,
                "min"=>6
            ),
            "new-password"=>array(
                "name"=>"New password",
                "required"=>true,
                "min"=>6
            ),
            "new-password-again"=>array(
                "name"=>"Repeated password",
                "required"=>true,
                "matches"=>"new-password"
            ),
        ));
    }
    if($validator->passed()) {
        if($email == sanitize_text($_POST["email"])) {
            $current_password = sanitize_text($_POST["password"]);
            $User = new User();
            if($User->fetchUser("email", $email)) {
                $salt = $User->getPropertyValue("salt");
                $pass = Hash::make($current_password, $salt);
                $stored_pass = $User->getPropertyValue("password");
                if($stored_pass == $pass) {
                    $new_password = sanitize_text($_POST["new-password"]);
                    $new_password = Hash::make($new_password, $salt);
                    if($User->update_property("password", $new_password)) {
                        $save_success_message = 'Changes saved successfully !';
                    } else {
                        $save_failure_message = "Erro";    
                    }
                } else {
                    $save_failure_message = "Invalide password !";
                }
            } else {
                $save_failure_message = "Invalide email !";
            }
        } else {
            $save_failure_message = "Invalide email !";
        }
    } else {
        $save_failure_message = $validator->errors()[0];
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit account</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Your existing stylesheets -->
    <link rel="stylesheet" href="public/css/global.css">
    <link rel="stylesheet" href="public/css/settings.css">
    <link rel="stylesheet" href="public/css/master-left-panel.css">

    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script src="public/javascript/config.js" defer></script>
    <script src="public/javascript/settings.js" defer></script>
</head>
<body>
<main>
    <?php include_once "page_parts/basic/master-left.php"; ?>
    <?php require_once "page_parts/settings/left-panel.php" ?>
    <div id="global-container" class="container mt-3">
        <div id="setting-master-container">
            <h1 class="no-margin">Security</h1>
            <div class="setting-block-line-separator"></div>
            <div>
                <div class="green-message">
                    <p class="green-message-text"><?php echo $save_success_message; ?></p>
                    <script>
                        if($(".green-message-text").text() !== "") {
                            $(".green-message").css("display", "block");
                        }
                    </script>
                </div>
                <div class="red-message">
                    <p class="red-message-text"><?php echo $save_failure_message; ?></p>
                    <script>
                        if($(".red-message-text").text() !== "") {
                            $(".red-message").css("display", "block");
                        }
                    </script>
                </div>
                <div class="form-group">
                    <label for="email">E-mail address<span class="text-danger">*</span></label>
                    <input type="text" form="save-form" class="form-control" autocomplete="off" value="<?php echo $email; ?>" name="email" id="email">
                </div>
                <div class="form-group">
                    <label for="password">Current password<span class="text-danger">*</span></label>
                    <input type="password" form="save-form" class="form-control" name="password" id="password">
                </div>
                <div class="form-group">
                    <label for="new-password">New password</label>
                    <input type="password" form="save-form" class="form-control" name="new-password" id="new-password">
                </div>
                <div class="form-group">
                    <label for="new-password-again">Confirm new password</label>
                    <input type="password" form="save-form" class="form-control" name="new-password-again" id="new-password-again">
                </div>
                
                <form action="" method="POST" id="save-form" enctype="multipart/form-data">
                    <input type="hidden" name="token_save_changes" value="<?php echo Token::generate("saveEdits"); ?>">
                    <button type="submit" class="btn btn-primary" name="save-changes" id="save-button">Submit</button>
                </form>
            </div>
        </div>
    </div>
</main>
</body>
</html>
