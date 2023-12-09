<?php
    use classes\Config;
    use models\User;
    use layouts\chat\ChatComponent;
    $user_profile_picture = $profile_user_picture = Config::get("root/path") . (empty($fetched_user->getPropertyValue("picture")) ? "public/assets/images/logos/Profile.jpg" : $fetched_user->getPropertyValue("picture"));
?>
<div class="flex-space" id="owner-profile-menu-and-profile-edit">
    <div class="row-v-flex">
        <a href="" class="menu-button-style-3 search-button refresh-discussion" id="edit-profile-button"></a>
        <div class="viewer">
            <div class="friends-chat-search-container" style="border-right: none;">
                <a href="" class="button-style-3 close-viewer" style="width: 40px; background-color: rgb(14, 143, 242);">Close</a>
                <div class="section-title"></div>
                    <input type="text" class="chat-input-style friend-search-input search-back white-search" placeholder="Search for a friend (username) ..">
                </div>
                <div id="friends-chat-container" class="relative">
                    <?php
                        $user_relation = new User();
                        $friends = $user_relation->get_friends($current_user_id);
                        foreach($friends as $friend) {
                            ChatComponent::generate_chat_page_friend_contact($current_user_id, $friend);
                        }
                    ?>
            </div>
        </div>
    </div>
</div>