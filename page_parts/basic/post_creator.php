<?php
    use classes\{Config, Token, Session};
?>

<div class="create-post-container">
    <div class="post-created-message">
        <p>Post created successfully<span id="post-creation-place"></span>.</p>
    </div>
    <div class="flex-space create-post-header">
        <div class="row-v-flex">
            <div class="create-post-profile-owner-picture-container">
                <img src="<?php echo Config::get("root/path") . ($user->getPropertyValue("picture") != "" ? $user->getPropertyValue("picture") : "public/assets/images/icons/user.png"); ?>" class="create-post-profile-owner-picture" alt="">
            </div>    
            <div class="horizontal-menu-item-wrapper" style="margin-left: 8px">
                <div class="sub-options-container sub-options-container-style-2" style="z-index: 1">
                    <div class="paragraph-wrapper-style-1">
                        <p class="label-style-2">Who can see your post?</p>
                    </div>
                    <div class="options-container-style-1">
                        <div class="sub-option-style-2 post-to-option">
                            <label for="" class="flex padding-back-style-1 public-back">Public</label>
                            <input type="radio" checked name="post-visibility" form="create-post-form" value="1" class="flex rad-opt">
                        </div>
                        <div class="sub-option-style-2 post-to-option">
                            <label for="" class="flex padding-back-style-1 friends-back">Friends</label>
                            <input type="radio" name="post-visibility" form="create-post-form" value="2" class="flex rad-opt">
                        </div>
                        <div class="sub-option-style-2 post-to-option">
                            <label for="" class="flex padding-back-style-1 lock-back">Only me</label>
                            <input type="radio" name="post-visibility" form="create-post-form" value="3" class="flex rad-opt">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="full-screen-create-post" class="relative">
            <div class="viewer">

            </div>
        </div>
    </div>
    <div>
        <textarea name="post-textual-content" form="create-post-form" id="create-post-textual-content" class="textarea-style-2" placeholder="Post..."></textarea>
    </div>
    <div class="post-assets-uploaded-container">
        
    </div>
    <div class="row-v-flex horizontal-frame-style-1">
        <div class="relative" style="overflow: hidden; width: 40px">
            <input type="file" multiple form="create-post-form" accept=".jpg,.jpeg,.png, .gif" id="post-assets" class="absolute no-opacity-element" style="cursor: pointer">
            <div class="photo-or-video-background button-style-6"></div>
        </div>
    </div>
    <div class="button-style-7-container" id="post-create-button">
        <form action="" method="POST" id="create-post-form" name="create-post-form" enctype="multipart/form-data">
            <input type="hidden" name="post_owner" value="<?php echo $user->getPropertyValue("id"); ?>">
            <input type="hidden" id="share_post_token" name="token_post" value="<?php 
                    if(Session::exists("share-post")) 
                        echo Session::get("share-post");
                    else {
                        echo Token::generate("share-post");
                    }
            ?>">
            <input type="submit" name="share-post-button" value="POST" class="button-style-7 share-post">
        </form>
    </div>
</div>