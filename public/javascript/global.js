$(".header-profile-edit-button").click(function() {
    window.location.href = root + "profile.php?edit";
    
    return false;
})

$(".delete-message-hint").click(function() {
    $(this).parent().css("display", "none")
})

$("#addd").click(function() {
    $.ajax({
        type: 'post',
        url: root + "layouts/post/generate_post.php",
        data: {
            "post_id": 40
        },
        success: function(component) {
            $("#posts-container").prepend(component);
        }
    })

    return false;
});

let headerHeight = 55;

$(".button-with-suboption").click(function() {
    let container = $(this).parent().find(".sub-options-container");
    if(container.css("display") == "none") {
        $(".sub-options-container").css("display", "none");
        container.css("display", "block");
    } else {
        container.css("display", "none");
    }
    return false;
});

document.addEventListener("click", function(event) {
    $(".sub-options-container").css("display", "none");
}, false);

let subContainers = document.querySelectorAll('.sub-options-container');
for(let i = 0;i<subContainers.length;i++) {
    subContainers[i].addEventListener("click", function(evt) {
        $(this).css("display", "block");
        evt.stopPropagation();
    }, false);
}

$(".post-to-option").click(function() {
    if(!$(this).find(".rad-opt").is(":disabled")) {
        $(this).parent().find("input[name='post-to']").prop("checked", false);
        $(this).find("input").prop("checked", true);
    }
});

$(".follow-button").click(function(event) {
    event.preventDefault();
    event.stopPropagation();

    let followButton = $(this);
    let form = $(this).parent();
    while(form.prop("tagName") != "FORM") {
        form = form.parent();
    }
    let url = root + 'security/check_current_user.php';
    $.ajax({
        type: "POST",
        url: url,
        data: form.serialize(),
        success: function(response)
        {
            if(response) {
                url = root + "api/follow/add.php";
                $.ajax({
                    type: "POST",
                    url: url,
                    data: form.serialize(),
                    success: function(response)
                    {
                        if(response["success"]) {
                            followButton.removeClass("follow-user");
                            followButton.attr("value", "Unfollow");

                            if($(".follow-label")) {
                                $(".follow-label").text("Unfollow");
                            }

                            if($(".follow-menu-header-form").find(".follow-button")) {
                                $(".follow-menu-header-form").find(".follow-button").removeClass("follow-user");
                                $(".follow-menu-header-form").find(".follow-button").addClass("followed-user");
                                $(".follow-menu-header-form").find(".follow-button").attr("value", "Followed");
                            }
                        } else {
                            url = root + "api/follow/delete.php";
                            $.ajax({
                                type: "POST",
                                url: url,
                                data: form.serialize(),
                                success: function() {
                                    followButton.removeClass("followed-user");
                                    followButton.attr("value", "Follow");

                                    if($(".follow-label")) {
                                        $(".follow-label").text("Follow");
                                    }

                                    if($(".follow-menu-header-form").find(".follow-button")) {
                                        $(".follow-menu-header-form").find(".follow-button").removeClass("followed-user");
                                        $(".follow-menu-header-form").find(".follow-button").addClass("follow-user");
                                        $(".follow-menu-header-form").find(".follow-button").attr("value", "Follow");
                                    }
                                }
                            });
                        }
                    }
                });
            } else {
                console.log("Error");
            }
        }
    });
});

$(".add-user").click(function(event) {
    event.preventDefault();
    event.stopPropagation();

    let addButton = $(this);
    let form = $(this).parent();
    let url = root + 'security/check_current_user.php';

    $.ajax({
        type: "POST",
        url: url,
        data: form.serialize(),
        success: function(response)
        {
            if(response) {
                url = root + "api/user_relation/send_request.php";
                $.ajax({
                    type: "POST",
                    url: url,
                    data: form.serialize(),
                    success: function(response)
                    {
                        if(response["error"]) {
                        }
                        else if(response["success"]) {
                            addButton.attr("value", "Cancel Request");
                        } else {
                            url = root + "api/user_relation/cancel_request.php";
                            $.ajax({
                                type: "POST",
                                url: url,
                                data: form.serialize(),
                                success: function() {
                                    addButton.attr("value", "Add");
                                    addButton.removeClass("unfriend-white-back");
                                    addButton.addClass("add-user-back");
                                }
                            });
                        }
                    }
                });
            } else {
                console.log("Error");
            }
        }
    });
})

$(".unfriend").click(function(event) {
    event.preventDefault();
    event.stopPropagation();
    let unfriend = $(this);
    let form = unfriend.parent();
    while(form.prop("tagName") != "FORM") {
        form = form.parent();
    }
    let url = root + 'security/check_current_user.php';
    $.ajax({
        type: "POST",
        url: url,
        data: form.serialize(),
        success: function(response)
        {
            if(response) {
                url = root + "api/user_relation/unfriend_relation.php";
                $.ajax({
                    type: "POST",
                    url: url,
                    data: form.serialize(),
                    success: function(response)
                    {
                        if(response["success"]) {
                            location.reload();
                        } else {
                            console.log("Error");
                        }
                    }
                });
            } else {
                console.log("Error");
            }
        }
    });
})

$(".accept-user").click(function(event) {
    event.preventDefault();
    event.stopPropagation();

    let unfriend = $(this);
    let form = unfriend.parent();
    while(form.prop("tagName") != "FORM") {
        form = form.parent();
    }

    let url = root + 'security/check_current_user.php';

    $.ajax({
        type: "POST",
        url: url,
        data: form.serialize(),
        success: function(response)
        {
            if(response) {
                url = root + "api/user_relation/accept_request.php";
                $.ajax({
                    type: "POST",
                    url: url,
                    data: form.serialize(),
                    success: function(response)
                    {
                        if(response["success"]) {
                            location.reload();
                        } else {
                            console.log("Error");
                        }
                    }
                });
            } else {
                console.log("Error");
            }
        }
    });
});

$(".decline-user").click(function(event) {
    event.preventDefault();
    event.stopPropagation();
    let unfriend = $(this);
    let form = unfriend.parent();
    while(form.prop("tagName") != "FORM") {
        form = form.parent();
    }

    let url = root + 'security/check_current_user.php';

    $.ajax({
        type: "POST",
        url: url,
        data: form.serialize(),
        success: function(response)
        {
            if(response) {
                url = root + "api/user_relation/decline_request.php";
                $.ajax({
                    type: "POST",
                    url: url,
                    data: form.serialize(),
                    success: function(response)
                    {
                        if(response["success"]) {
                            location.reload();
                        } else {
                            console.log("Error");
                        }
                    }
                });
            } else {
                console.log("Error");
            }
        }
    });
});

$(".accept-request").click(function(event) {
    event.preventDefault();
    event.stopPropagation();

    let profile_path = $(this).parent().parent().parent().find('.link-to-profile').attr('href');
    let uid = $(this).parent().find(".uid").val();
  
    url = root + "api/user_relation/accept_request.php";
    $.ajax({
        type: "POST",
        url: url,
        data: {
            current_user_id: current_user_id,
            current_profile_id: uid
        },
        success: function(response)
        {
            if(response["success"]) {
                window.location.href = profile_path;
            } else {
                console.log("Error");
            }
        }
    });   
})
$(".delete-request").click(function(event) {
    event.preventDefault();
    event.stopPropagation();

    let profile_path = $(this).parent().parent().parent().find('.link-to-profile').attr('href');
    let uid = $(this).parent().find(".uid").val();
  
    url = root + "api/user_relation/decline_request.php";
    $.ajax({
        type: "POST",
        url: url,
        data: {
            current_user_id: current_user_id,
            current_profile_id: uid
        },
        success: function(response)
        {
            if(response["success"]) {
                window.location.href = profile_path;
            } else {
                console.log("Error");
            }
        }
    });   
})
