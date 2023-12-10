<?php

    namespace layouts\post;
    use classes\{Config};
    use models\{User, Comment, Like, Post as Pst};
    class Post {

        function generate_post($post, $user) {
            $root = Config::get("root/path");
            $project_name = Config::get("root/project_name");
            $project_path = $_SERVER['DOCUMENT_ROOT'] . "/" . $project_name . "/";

            $current_user_id = $user->getPropertyValue("id");
            $current_user_picture = Config::get("root/path") . (($user->getPropertyValue("picture") != "") ? $user->getPropertyValue("picture") : "public/assets/images/icons/user.png");
            $post_owner_user = new User();
            $post_owner_user->fetchUser("id", $post->get_property("post_owner"));
            $post_owner_picture = Config::get("root/path") . (($post_owner_user->getPropertyValue("picture") != "") ? $post_owner_user->getPropertyValue("picture") : "public/assets/images/logos/logo512.png");
            $post_id= $post->get_property("post_id");
            $post_owner_name = $post_owner_user->getPropertyValue("firstname") . " " . $post_owner_user->getPropertyValue("lastname") . " -@" . $post_owner_user->getPropertyValue("username");
            $post_owner_actions = "";
            if($post->get_property("post_owner") == $user->getPropertyValue('id')) {
                $post_owner_actions = <<<E
                    <div class="sub-option-style-2">
                        <a href="" class="black-link delete-post">Delete post</a>
                    </div>
                    <div class="sub-option-style-2">
                        <a href="" class="black-link edit-post">Edit post</a>
                    </div>
E;
            }

            $post_date = $post->get_property("post_date");
            $post_date = date("F d \a\\t Y h:i A",strtotime($post_date)); 

            $post_visibility = "";
            if($post->get_property('post_visibility') == 1) {
                $post_visibility = "public/assets/images/icons/public-white.png";
            } else if($post->get_property('post_visibility') == 2) {
                $post_visibility = "public/assets/images/icons/group-w.png";
            }  else if($post->get_property('post_visibility') == 3) {
                $post_visibility = "public/assets/images/icons/lock-white.png";
            } 

            $post_owner_profile = Config::get("root/path") . "profile.php?username=" . $post_owner_user->getPropertyValue("username");
            $image_components = "";
            $video_components = "";
            $post_images_location = $post->get_property("picture_media");
            $post_videos_location = $post->get_property("video_media");
            $post_images_dir = $project_path . $post->get_property("picture_media");
            $post_videos_dir = $project_path . $post->get_property("video_media");
            $post_text_content = htmlspecialchars_decode($post->get_property("text_content"));
            if($post_images_location != null && is_dir($post_images_dir)) {
                if($this->is_dir_empty($post_images_dir) == false) {
                    $fileSystemIterator = new \FilesystemIterator($post_images_dir);
                    foreach ($fileSystemIterator as $fileInfo){
                        $image_components .= $this->generate_post_image($root . $post_images_location . $fileInfo->getFilename());
                    }
                }
            }
            if($post_videos_location != null && is_dir($post_videos_dir)) {
                if($this->is_dir_empty($post_videos_dir) == false) {
                    $fileSystemIterator = new \FilesystemIterator($post_videos_dir);
                    foreach ($fileSystemIterator as $fileInfo){
                        $src = $root . $post_videos_location . $fileInfo->getFilename();
                        $video_components = <<<VIDEO
                        <video class="post-video" controls>
                            <source src="$src" type="video/mp4">
                            <source src="movie.ogg" type="video/ogg">
                            Your browser does not support the video tag.
                        </video>
            VIDEO;
                    }
                }
            }
            $like_class = "white-like-back";
            $nodisplay = 'no-display';
            
            $post_meta_like = <<<LM
            <div class="no-display post-meta-likes post-meta"><span class="meta-count">0</span>Likes</div>
            LM;
            $post_meta_comment = <<<CM
            <div class="no-display post-meta-comments post-meta"><span class="meta-count">0</span>Comments</div>
            CM;
            // Comment meta
            $pmc = count(Comment::fetch_post_comments($post_id));
            if($pmc > 0) {
                $post_meta_comment = <<<CM
                <div class="post-meta-comments post-meta"><span class="meta-count">$pmc</span>Comments</div>
            CM;
            }
            $like_manager = new Like();
            if(($likes_count = count($like_manager->get_post_users_likes_by_post($post_id))) > 0) {
                $post_meta_like = <<<LM
                <div class="post-meta-likes post-meta"><span class="meta-count">$likes_count</span>Likes</div>
            LM;
            }
            $like_manager->setData(array(
                "user_id"=>$current_user_id,
                "post_id"=>$post_id
            ));
            $like_class = "white-like-back";
            if($like_manager->exists()) {
                $like_class = "white-like-filled-back bold";
            }
            $comments_components = '';
            foreach(Comment::fetch_post_comments($post_id) as $comment) {
                $cm = new Comment();
                $cm->fetch_comment($comment->id);

                $comments_components .= self::generate_comment($cm, $current_user_id);
            }
            return <<<EOS
            <div class="post-item">
                <div class="timeline-post image-post">
                    <div class="post-header flex-space">
                        <div class="post-header-without-more-button">
                            <div class="post-owner-picture-container">
                                <img src="$post_owner_picture" class="post-owner-picture" alt="">
                            </div>
                            <div class="post-header-textual-section">
                                <a href="$post_owner_profile" class="post-owner-name">$post_owner_name</a>
                                <div class="row-v-flex">
                                    <p class="regular-text"><a href="" class="post-date">$post_date</a> <span style="font-size: 8px">.</span></p>
                                    <img src="$post_visibility" class="image-style-8" alt="" style="margin-left: 8px">
                                </div>
                            </div>
                        </div>
                        <div class="relative">
                            <a href="" class="button-style-10 dotted-more-back button-with-suboption"></a>
                            <div class="sub-options-container sub-options-container-style-2" style="z-index: 1; width: 129px; top: 30px; left: -100px; padding: 4px">
                                $post_owner_actions
                                <div class="sub-option-style-2">
                                    <a href="" class="black-link hide-post">Hide post</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="post-text">
                        $post_text_content
                    </p>
                    <div class="post-edit-container relative" style="padding: 0 10px; box-sizing: border-box">
                        <textarea autocomplete="off" class="editable-input post-editable-text"></textarea>
                        <div class="close-post-edit"></div>
                    </div>
                    <div class="media-container">
                        $video_components
                        $image_components
                    </div>
                    <div class="post-statis row-v-flex $nodisplay">
                        $post_meta_like
                        <div class="right-pos-margin row-v-flex">
                            $post_meta_comment
                        </div>
                    </div>
                    <div class="react-on-opost-buttons-container">
                        <a href="" class="$like_class post-bottom-button like-button">Like</a>
                        <a href="" class="white-comment-back post-bottom-button write-comment-button">Comment</a>
                    </div>
                    <div class="comment-section">
                        $comments_components
                        <div class="owner-comment">
                            <div class="comment-block">
                                <div class="comment-op">
                                    <div class="comment_owner_picture_container">
                                        <img src="$current_user_picture" class="comment_owner_picture" alt="">
                                    </div>
                                </div>
                                <div class="comment-input-form-wrapper">
                                    <form action="" method="POST" class="comment-form relative">
                                        <input type="text" name="comment" autocomplete="off" placeholder="Write a comment .." class="comment-style comment-input">
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" class="pid" value="$post_id">
            </div>

            EOS;
        }
        public static function generate_comment($comment, $current_user_id) {

            $comment_owner = new User();
            $comment_owner->fetchUser('id', $comment->get_property("comment_owner"));
            $comment_owner_picture = Config::get("root/path") . 
                (empty($comment_owner->getPropertyValue("picture")) ? "public/assets/images/logos/logo512.png" : $comment_owner->getPropertyValue("picture"));
            $comment_owner_username = $comment_owner->getPropertyValue("username");
            $comment_owner_profile = Config::get("root/path") . "profile.php?username=" . $comment_owner_username;
            $comment_text = $comment->get_property("comment_text");
            $comment_id = $comment->get_property("id");
            $now = strtotime("now");
            $seconds = floor($now - strtotime($comment->get_property("comment_date")));
            if($seconds > 29030400) {
                $comment_life = floor($seconds / 29030400) . "y";
            } else if($seconds > 604799 && $seconds < 29030400) {
                $comment_life = floor($seconds / 604800) . "w";
            } else if($seconds < 604799 && $seconds > 86400) {
                $comment_life = floor($seconds / 86400) . "d";
            } else if($seconds < 86400 && $seconds > 3600) {
                $comment_life = floor($seconds / 3600) . "h";
            } else if($seconds < 3600 && $seconds > 60) {
                $comment_life = floor($seconds / 60) . "min";
            } else if($seconds > 15){
                $comment_life = $seconds . "sec";
            } else {
                $comment_life = "Now";
            }
            $comment_options = <<<CO
    <div class="relative comment">
        <div class="comment-options-button"></div>
        <div class="sub-options-container sub-options-container-style-2" style="z-index: 1; width: 129px; top: 20px; left: -100px">
            <div class="options-container-style-1 black">
CO;

            $owner_of_post_contains_current_comment = $comment->get_property('post_id');
            $owner_of_post_contains_current_comment = Pst::get_post_owner($owner_of_post_contains_current_comment);
            if(($comment->get_property("comment_owner") == $current_user_id)
                || $current_user_id == $owner_of_post_contains_current_comment->post_owner)
            {
                if($comment->get_property("comment_owner") == $current_user_id) {
                    $comment_options .= <<<CO
                <div class="sub-option-style-2">
                    <a href="" class="black-link edit-comment">Edit comment</a>
                </div>
CO;
                }
                $comment_options .= <<<CO
                <div class="sub-option-style-2">
                    <a href="" class="black-link delete-comment">Delete comment</a>
                </div>
CO;
            }

            $comment_options .= <<<CO
            <div class="sub-option-style-2">
                <a href="" class="black-link hide-button">Hide comment</a>
            </div>
        </div>
    </div>
</div>
CO;

            return <<<COM
                <div class="comment-block">
                    <input type="hidden" class="comment_id" value="$comment_id">
                    <div class="small-text hidden-comment-hint">Click <span class="show-comment">here</span> to show it again</div>
                    <div class="comment-op">
                        <div class="comment_owner_picture_container">
                            <img src="$comment_owner_picture" class="comment_owner_picture" alt="TT">
                        </div>
                    </div>
                    <div class="comment-global-wrapper">
                        <div class="row-v-flex">
                            <div class="comment-wrapper">
                                <div>
                                    <a href="$comment_owner_profile" class="comment-owner">$comment_owner_username</a>
                                    <p class="comment-text">$comment_text</p>
                                    <div class="comment-edit-container relative">
                                        <textarea autocomplete="off" class="editable-input comment-editable-text"></textarea>
                                        <div class="close-edit"></div>
                                    </div>
                                </div>
                            </div>
                            $comment_options
                        </div>
                    </div>
                </div>
COM;
        }

        public function generate_post_image($url) {
            return <<<PI
                <div class="post-media-item-container relative">
                    <img src="$url" class="post-media-image" alt="">
                </div>
PI;
        }

        function is_dir_empty($dir) {
            return (count(glob("$dir/*")) === 0); 
        }
    }

?>
