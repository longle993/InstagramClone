<?php
use classes\{Session, Token, Common, Redirect};
if(isset($_POST["logout"])) {
    if(Token::check(Common::getInput($_POST, "token_logout"), "logout")) {
        $user->logout();
        Redirect::to("login/login.php");
    }
}
$index_page_path = $root . "index.php";
$setting_profile_path = $root . "settings.php";
$setting_account_path = $root . "settings-account.php";
?>
<div id="setting-left-pannel">
    <h1 class="no-margin">Settings</h1>
    <div class="setting-block-line-separator"></div>
    <div id="left-panel-menu">
        <div class="button-with-suboption relative <?php if(isset($profile_selected)) echo $profile_selected; ?>">
            <div class="relative">
                <div class="menu-button-icon profile-button-icon absolute"></div>
                <a href="<?php echo $setting_profile_path; ?>" class="menu-button">Profile</a>
            </div>
        </div>
        <div class="button-with-suboption relative">
            <div class="relative">
                <div class="menu-button-icon account-button-icon absolute"></div>
                <a href="<?php echo $setting_account_path; ?>" class="menu-button <?php if(isset($account_selected)) echo $account_selected; ?>">Account</a>
            </div>
        </div>
        <div class="button-with-suboption relative">
            <button name="logout" type="submit" form="logout-form" class="logout-btn">
            </button>
            <div class="relative">
                <div class="menu-button-icon logout-button-icon absolute"></div>
                <a href="<?php echo $setting_profile_path; ?>" class="menu-button profile-button">Logout</a>
            </div>
        </div>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="logout-form">
            <input type="hidden" name="token_logout" value="<?php 
                if(Session::exists("logout")) 
                    echo Session::get("logout");
                else {
                    echo Token::generate("logout");
                }
            ?>">
        </form>
    </div>
</div>