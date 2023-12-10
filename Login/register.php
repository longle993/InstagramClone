<?php
require 'function.php';
require_once "../vendor/autoload.php";
require_once "../core/init.php";

use classes\{DB, Config, Validation, Common, Session, Token, Hash, Redirect};
use models\User;

$register = new Register();

if(isset($_POST["register"])) {
    $validate = new Validation();
    if(Token::check(Common::getInput($_POST, "token_reg"), "register")) {
        $validate->check($_POST, array(
            "firstname"=>array(
                "name"=>"Firstname",
                "min"=>2,
                "max"=>50
            ),
            "lastname"=>array(
                "name"=>"Lastname",
                "min"=>2,
                "max"=>50
            ),
            "username"=>array(
                "name"=>"Username",
                "required"=>true,
                "min"=>6,
                "max"=>20,
                "unique"=>true
            ),
            "email"=>array(
                "name"=>"Email",
                "required"=>true,
                "email-or-username"=>true
            ),
            "password"=>array(
                "name"=>"Password",
                "required"=>true,
                "min"=>6
            ),
            "password_again"=>array(
                "name"=>"Repeated password",
                "required"=>true,
                "matches"=>"password"
            ),
        ));

        if($validate->passed()) {
            $salt = Hash::salt(16);

            $user = new User();
            $user->setData(array(
                "firstname"=>Common::getInput($_POST, "firstname"),
                "lastname"=>Common::getInput($_POST, "lastname"),
                "username"=>Common::getInput($_POST, "username"),
                "email"=>Common::getInput($_POST, "email"),
                "password"=> Hash::make(Common::getInput($_POST, "password"), $salt),
                "salt"=>$salt,
                "joined"=> date("Y/m/d h:i:s"),
                "user_type"=>1,
                "cover"=>'',
                "picture"=>'',
                "private"=>-1
            ));
            $user->add();
            mkdir("../data/users/" . Common::getInput($_POST, "username")."/");
            mkdir("../data/users/" . Common::getInput($_POST, "username")."/posts/");
            mkdir("../data/users/" . Common::getInput($_POST, "username")."/media/");
            mkdir("../data/users/" . Common::getInput($_POST, "username")."/media/pictures/");
            mkdir("../data/users/" . Common::getInput($_POST, "username")."/media/covers/");
            $reg_success_message = "Your account has been created successfully.";
            
            Session::flash("register_success", "Welcome");
            Session::flash("new_username", Common::getInput($_POST, "username"));
            echo "<script> alert('Successful registration'); 
            window.location.href = 'login.php';
            </script>";
        } else {
            $login_failure_message = $validate->errors()[0];
        }
    }
}

if (isset($_POST["submitButton"])) {
    $result = $register->register_user($_POST["user_name"], $_POST["full_name"], $_POST["email"], $_POST["password"]);

    if($result == 0){
        echo "<script> alert('Please enter full information'); </script>";
    }else if ($result == 1) {
        echo "<script> 
        alert('Successful registration'); 
        window.location.href = 'login.php';
      </script>";
    } else {
        echo "<script> alert('Username or email already exists'); </script>";
    }
}
?>
<script>
        function togglePassword() {
            var passwordField = document.getElementById("password");
            passwordField.type = (passwordField.type === "password") ? "text" : "password";
        }
</script>
<!DOCTYPE html>
<html lang="en">
<head>
      <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Thư viện CSS Bootstrap -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
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
                   
                    <input type="text" placeholder="First Name" class="form--input mb-3" name="firstname">
                    <input type="text" placeholder="Last Name" class="form--input mb-3" name="lastname">
                    <input type="text" placeholder="Username" class="form--input mb-3" name="username">
                    <input type="email" placeholder="Email" class="form--input mb-3" name="email">
                    <div class="passWordForm mb-3">
                        <input type="password" placeholder="Password" class="form--input" name="password" id="password" autocomplete="new-password">
                        <span class="show_hide_text cursor-pointer" id="show_hide_password" onclick="togglePassword()"></span>
                    </div>
                    <div class="passWordForm mb-3">
                        <input type="password" placeholder="Re-enter password" class="form--input" name="password_again" id="password" autocomplete="new-password">
                        <span class="show_hide_text cursor-pointer" id="show_hide_password" onclick="togglePassword()"></span>
                    </div>
                    <!-- <button class="button cursor-pointer" type="submit" name="submitButton"> Sign Up </button> -->
                    <div class="classic-form-input-wrapper">
                        <input type="hidden" name="token_reg" value="<?php echo Token::generate("register"); ?>">
                        <input type="submit" value="Register" name="register" class="button-style-1" style="width: 70px;">
                    </div>
                </form>
                <footer class="forgot--Footer">
                Already have an account <a href="login.php" class="sign_in"> Sign In </a>
                </footer>
            </article>
        </div>
    </div>
</body>
</html>
