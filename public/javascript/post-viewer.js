
$("#post-viewer").height($(window).height() - 55);

const urlParams = new URLSearchParams(window.location.search);
const pid = urlParams.get('pid');

let asset_image = $(".asset-image")
let image_height = asset_image.height();
let image_width = asset_image.width();

if(image_height > image_width) {
    asset_image.css("width", "100%");
} else {
    asset_image.css("height", "100%");
}

handle_comment_event();

$("#post-assets-container").on({
    mouseenter: function() {
        if(current_index == 0) {
            $(".asset-back").css("display", "none");
            $(".asset-next").css("display", "flex");
            $(".asset-move-button").animate({
                opacity: 1
            }, 200);
        } else if(current_index != 0 && current_index != images_length-2) {
            $(".asset-back").css("display", "flex");
            $(".asset-next").css("display", "flex");
            $(".asset-move-button").animate({
                opacity: 1
            }, 200);
        } else {
            $(".asset-back").css("display", "flex");
            $(".asset-next").css("display", "none");
            $(".asset-move-button").animate({
                opacity: 1
            }, 200);
        }
    },
    mouseleave: function() {
        if(current_index != 0) {
            $(".asset-move-button").css("display", "none");
            $(".asset-move-button").animate({
                opacity: 0
            }, 200);
        }
    }
})

$(".asset-move-button").on({
    mouseenter: function() {
        $(this).css("backgroundColor", "rgb(16, 16, 16)");
    },
    mouseleave: function() {
        $(this).css("backgroundColor", "rgb(10, 10, 10)");
    }
});

window.onresize = function() {
    $("#post-viewer").height($(window).height() - 55);
}

if($(".post-text").text().length > 200) {
    $(".post-text").text($(".post-text").text().substr(0, 199) + "..");
} else {
    $(".collapse-text").css("display", "none");
}


$(".collapse-text").click(function() {
    if($(this).text() == "See more") {
        $(this).text("See less");
        $(".post-text").text($(".hidden-post-text").text());
    } else {
        $(this).text("See more");
        $(".post-text").text($(".post-text").text().substr(0, 199) + "..");
    }
});

$(".exit-button").click(function(event) {
    window.history.back();
});

$(".asset-image").attr("src", $(".images").find(".post-asset-image").first().val());
$(".asset-back").css("display", "none");

let current_index = $(".current-asset-image").val();
let images_length =$(".images").find(".post-asset-image").length;

if(images_length == 1) {
    $(".asset-move-button").remove();
} else {
    $(".asset-next").click(function() {

        $(".asset-back").css("display", "flex");
        if(current_index == images_length-2) {
            $(".asset-next").css("display", "none");
            let next_image_src = $(".images").find(".post-asset-image").eq(++current_index).val();
            $(".asset-image").attr("src", next_image_src);
            return;
        }
        
        let next_image_src = $(".images").find(".post-asset-image").eq(++current_index).val();
        $(".asset-image").attr("src", next_image_src);
        $(".current-asset-image").val(current_index)
    });

    $(".asset-back").click(function() {
        $(".asset-next").css("display", "flex");
        if(current_index == 1) {
            $(".asset-back").css("display", "none");
            let next_image_src = $(".images").find(".post-asset-image").eq(--current_index).val();
            $(".asset-image").attr("src", next_image_src);
            return;
        }

        let next_image_src = $(".images").find(".post-asset-image").eq(--current_index).val();
        $(".asset-image").attr("src", next_image_src);
        $(".current-asset-image").val(current_index)
    });
}

$(".comment").click(function() {
    $(".comment-inp").focus();
});

$(".like").click(function(event) {
    let like_button = $(this);

    $.ajax({
        url: root + "api/like/post.php",
        type: 'post',
        data: {
            post_id: pid,
            current_user_id: current_user_id
        },
        success: function(response) {
           let like_counter = $(".like-counter").text();
            if(response == 1) {
                if($(".like-counter").text() == "") {
                    $(".like-counter").text("1");
                } else {
                    like_counter = parseInt(like_counter) + 1;
                    $(".like-counter").text(like_counter);
                }

                $(".like-text-state").text("Liked");
                $(".like-button-image").attr("src", "../public/assets/images/icons/like-black-filled.png");

            } else if(response == 2) {
                if($(".like-counter").text() == "1") {
                    $(".like-counter").text("");
                } else {
                    like_counter = parseInt(like_counter) - 1;
                    $(".like-counter").text(like_counter);
                }

                $(".like-button-image").attr("src", "../public/assets/images/icons/like-black.png");
                $(".like-text-state").text("Like");
            }
        }
    });
});

$(".comment-owner").click(function() {
    let username = $(this).text();
    window.location.href = root + "profile.php?username=" + username;
    return false;
});

$(".comment-inp").on({
    keydown: function(event) {
        let comment = $(this);
        if($(this).is(":focus") && (event.keyCode == 13)) {
            if($(this).val() != "") {
                
                $.ajax({
                    url: root + "security/get_current_user.php",
                    success(response) {
                        let comment_owner = response.id;
                        let comment_text = comment.val();

                        $.ajax({
                            url: root+"api/comment/post.php",
                            data: {
                                "comment_owner": comment_owner,
                                "post_id": pid,
                                "comment_text": comment_text,
                                "current_user_id": current_user_id
                            },
                            type: 'POST',
                            success: function(response) {
                                let count = $(".num-of-comments").text();
                                let comments_counter = (count == "0") ? 0 : parseInt($(".num-of-comments").text());

                                if($(".noc-container").hasClass("no-display")) {
                                    $(".noc-container").removeClass("no-display");
                                }

                                comments_counter = comments_counter + 1;
                                $(".num-of-comments").text(comments_counter);
                                comment.val("");

                                $("#pcomments").prepend(response);
                                let component = $("#pcomments .comment-block").first();
                                component.find(".comment_id").val();
                                handle_comment_event(component);
                                $(".delete-comment").click(function() {
                                    decrement_comment_counter();
                                });
                            }
                        })
                    }
                });
            } else {
                console.log("empty comment !");
            }
            return false;
        }
    }
});

$(".delete-comment").click(function() {
    decrement_comment_counter();
});

function decrement_comment_counter() {

    let count = $(".num-of-comments").text();
    let comments_counter = (count == "0") ? 0 : parseInt($(".num-of-comments").text());
    console.log("here !");

    if(comments_counter == 1) {
        $(".num-of-comments").text("0");
        $(".noc-container").addClass("no-display");
    } else {
        comments_counter = comments_counter - 1;
        $(".num-of-comments").text(comments_counter);
    }
}