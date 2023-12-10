<?php

namespace layouts\general;

    class CreatePost {
        public static function generatePostCreationImage() {
            echo <<<EOS
            <div class="relative post-creation-item">
                <div class="delete-uploaded-item absolute"></div>
                <img src="" class="image-post-uploaded" alt="">
                <input type="hidden" class="pciid" value="">
            </div>
EOS;
        }
    }
?>