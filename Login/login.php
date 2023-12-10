<?php
require 'function.php';
require_once "../vendor/autoload.php";
require_once "../core/init.php";
use classes\{DB, Config, Validation, Common, Session, Token, Hash, Redirect};
use models\User;
$login = new Login();

if($user->getPropertyValue("isLoggedIn")) {
    Redirect::to("../index.php");
}
$validate = new Validation();
$reg_success_message = '';
$login_failure_message = '';
if(isset($_POST["login"])) {
    if(Token::check(Common::getInput($_POST, "token_log"), "login")) {
        $validate->check($_POST, array(
            "email-or-username"=>array(
                "name"=>"Email or username",
                "required"=>true,
                "max"=>255,
                "min"=>6,
                "email-or-username"=>true
            ),
            "password"=>array(
                "name"=>"Password",
                "required"=>true,
                "strength"=>true
            )
        ));

        if($validate->passed()) {
            $remember = isset($_POST["remember"]) ? true: false;
            $log = $user->login(Common::getInput($_POST, "email-or-username"), Common::getInput($_POST, "password"), $remember);
            
            if($log) {
                Redirect::to("../index.php");
            } else {
                $login_failure_message = "Either email or password is invalide !";
            }
        } else {
            $login_failure_message = $validate->errors()[0];
        }
    } else {
        $validate->addError('Error');
    }
}

if(isset($_POST["submitButton"])){
    $result = $login->login($_POST["email_user"], $_POST["password"]);

    if ($result == 1){
       $_SESSION["login"] =true;
       $_SESSION["id"] = $login->idUser();
       echo
       "<script> 
        alert('Successful login'); 
        window.location.href = 'index.php';
        </script>";
        
    }
    elseif($result == 10){
        echo
        "<script> alert('Incorrect password'); </script>";
    }
    elseif($result == 100){
        echo
        "<script> alert('Unregistered account'); </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <!-- Liên kết tệp CSS của Bootstrap -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- Liên kết tệp CSS tùy chỉnh của bạn -->
    <link rel="stylesheet" href="login.css">
</head>

<body>
    <div class="mainPagecontainer">
        <div class="row">
            <article class="col-6">
            <form method="POST" action="">
                    <div class="loGo">
                        <img src="instagram.png" alt="IG">
                    </div>
                    <input type="text" placeholder="Username or email" class="form--input mb-3" name="email-or-username">
                    <div class="passWordForm">
                        <input type="password" placeholder="Password" class="form--input mb-3" name="password" id="password">
                        <span class="show_hide_text cursor-pointer" id="show_hide_password"></span>
                    </div>
                    <!-- <button class="button cursor-pointer btn btn-primary btn-block" type="submit" name="submitButton"> Sign in </button> -->
                    <form action="<?php echo htmlspecialchars(Config::get("root/path")) . "Login/login.php" ?>" method="post" class="flex-form" id="login-form">
                        <input type="hidden" name="token_log" value="<?php echo Token::generate("login"); ?>">
                        <input type="submit" name="login" tabindex="4" value="Login" class="button-style-1">
                    </form>
                </form>
                <footer class="forgot--Footer">
                Don't have an account yet <a href="register.php" class="sign_in"> Sign Up </a>
                </footer>
            </article>
        </div>
    </div>
