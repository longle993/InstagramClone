<?php
use models\Follow;
$current = $user->getPropertyValue("id");
$friend = $fetched_user->getPropertyValue("id");
$follow = new Follow();
$follow->set_data(array(
    "follower"=>$current,
    "followed"=>$friend
));
?>
<div class="flex-space" id="owner-profile-menu-and-profile-edit">
    <div class="flex-row-column">
        <form action="" method="GET" class="flex follow-form follow-menu-header-form">
            <input type="hidden" name="current_user_id" value="<?php echo $current ?>">
            <input type="hidden" name="current_profile_id" value="<?php echo $friend ?>">
            <?php
                if($follow->fetch_follow()) {
                    $follow_unfollow = <<<EOS
                        <div class="sub-option-style-2 post-to-option">
                            <label for="" class="flex padding-back-style-1 unfollow-black follow-label">Unfollow</label>
                            <input type="submit" class="button-style-3-black follow-button" value="Unfollow" style="margin-left: 8px; font-weight: 400">
                        </div>
            EOS;
            ?>
            <input type="submit" class="button-style-3 follow-button followed-user" value="Followed" style="margin-left: 4px; font-weight: 400">
            <?php } else {
                $follow_unfollow = <<<EOS
                    <div class="sub-option-style-2 post-to-option">
                        <label for="" class="flex padding-back-style-1 follow-black follow-label">Follow</label>
                        <input type="submit" class="button-style-3-black follow-button" value="Follow" style="margin-left: 8px; font-weight: 400">
                    </div>
            EOS;            
            ?>
            <input type="submit" class="button-style-3 follow-button follow-user" value="Follow" style="margin-left: 4px; font-weight: 400">
            <?php } ?>
        </form>
    </div>
</div>