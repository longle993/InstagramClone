const urlParams = new URLSearchParams(window.location.search);

$("#second-chat-part").height($(window).height() - 55);
$("#first-chat-part").height($(window).height() - 55);
$("#chat-container").scrollTop($("#chat-container").prop("scrollHeight"));

$(".friend-chat-discussion-item-wraper").click(function() {
    $(".friend-chat-discussion-item-wraper").css("background-color", "rgb(0, 0, 0)");
    $(this).css("background-color", "rgb(30, 30, 30)");
})

let discussion_chat_opened = false;
let message_writing_notifier = 0;

$(".new-message-button").click(function() {
    $("#styled-border").css("display","block");
    $("#styled-border").animate({
        opacity: '1'
    }, 600, function() {
        window.setTimeout(function() {
            $("#styled-border").animate({
                opacity: '0'
            }, 600, function() {
                $("#styled-border").css("display","none");
            });
        }, 600);
    });
    return false;
});

$(".friend-search-input").on("change paste keyup", function() {
    let username = $(this).val();
    $.ajax({
        url: root + "layouts/chat/get_chat_friend_by_username.php",
        type: 'POST',
        data: {
            username: username
        },
        success(data) {
            $("#friends-chat-container").html("");
            $("#friends-chat-container").append(data);
            
            $(".friends-chat-item").click(function() {
                open_friend_chat_section($(this));
                console.log("Chat");
                return false;
            });
        }
    });
 });

if(urlParams.get('username')) {
    var values = {
        'sender': null,
        'receiver': null
    };
    $.ajax({
        type: "GET",
        url: root + "security/get_current_user.php",
        success: function(current_user) {
            values["sender"] = current_user["id"];
            $.ajax({
                type: "GET",
                url: root + "api/user/get_by_username.php?username=" + urlParams.get('username'),
                success: function(response) {
                    if(response["success"]) {
                        values["receiver"] = response["user"]["id"];
                        let url = root + "layouts/chat/generate_chat_container.php";
                        $.ajax({
                            type: "POST",
                            url: url,
                            data: values,
                            success: function(data) {
                                $("#no-discussion-yet").remove();
                                $("#chat-global-container").append(data);
                                
                                $("#chat-container").height($(window).height() - 200);
                                $.ajax({
                                    type: 'POST',
                                    url: root + 'api/messages/get_friend_messages.php',
                                    data: values,
                                    success: function(data) {
                                        $("#chat-container").append(data);
                                        handle_message_elements_events($(".message-global-container"));
                                        $("#chat-container").scrollTop($("#chat-container").prop("scrollHeight"));
                                    }
                                })
                                $("#send-message-button").click(function() {
                                    let chat_text_content = $('#second-chat-part').find("#chat-text-input").val();
                                    send_message(values["sender"], values["receiver"], chat_text_content);
                                });
                                discussion_chat_opened = true;
                            }
                        });
                    } else {
                        console.log("Error");
                    }
                }
            });
        }
    });
}

$(".friends-chat-item, .friend-chat-discussion-item-wraper").click(function() {
    open_friend_chat_section($(this));
    console.log("Chat");
    return false;
});

$(document).keypress(function(e) {
    let message_input = $('#second-chat-part').find("#chat-text-input");
    let isFocused = (document.activeElement === message_input[0]);
    
    let sender = $("#second-chat-part").find(".chat-sender").val();
    let receiver = $("#second-chat-part").find(".chat-receiver").val();
    let text_data = message_input.val();

    if(isFocused && e.keyCode == 13) {
        send_message(sender, receiver, text_data);
    }
});
function send_message(sender, receiver, text_data) {
    save_data_and_return_compoent(sender, receiver, text_data, function(result) {
        if(result) {
            let values = {
                "sender": sender,
                "receiver": receiver
            };
            $("#chat-container").append(result);
            $('#second-chat-part').find("#chat-text-input").val("");
            message_writing_notifier = 0;
            $.ajax({
                type: "POST",
                url: root + "api/messages/message_writing_notifier/delete.php",
                data: values
            });
            
            handle_message_elements_events($(".message-global-container").last());
            $("#chat-container").scrollTop($("#chat-container").prop("scrollHeight"));

            $(".reply-container").css("display", "none");
            $("#chat-text-input").attr("placeholder", "Message...");
            $("#chat-text-input").css("paddingLeft", "15px");
            $("#chat-text-input").focus();
        }
    });
}


function save_data_and_return_compoent(sender, receiver, message, handle_data) {
    var values = {
        'sender': sender,
        'receiver': receiver,
        'message': message
    };

    if($(".reply-container").css("display") != "none") {
        values['is_reply'] = 'yes',
        values['replied_message_id'] = $(".reply-container").parent().parent().find(".replied-message-id").val();
    }

    $.ajax({
        type: "POST",
        url: root + "api/messages/Send.php",
        data: values,
        success: function(data) {
            handle_data(data);
        }
    });
}

function handle_message_elements_events(element) {

    $(".message-global-container").on({
        mouseenter: function() {
            $(this).find(".chat-message-more-button").css("display", "block");
            $(this).find(".message-date").css("display", "block");
        },
        mouseleave: function() {
            $(this).find(".chat-message-more-button").css("display", "none");
            $(this).find(".message-date").css("display", "none");
        }
    });

    element.find(".chat-message-more-button").on( {
        click: function(event) {
            event.stopPropagation();
            event.preventDefault();

            let container = $(this).parent().parent().find(".sub-options-container");
            if(container.css("display") == "none") {
                $("#chat-container").find(".sub-options-container").css("display", "none");
                container.css("display", 'block');
            } else
                container.css("display", 'none');
        
            return false;
        }
    });

    element.find(".delete-current-user-message").click(function() {
        console.log("Delete");
        let message_id = $(this).parent().find(".message_id").val();
        let message_container = $(this);
        while(!message_container.hasClass("message-global-container")) {
            message_container = message_container.parent();
        }
        
        $.ajax({
            url: root + 'api/messages/DELETE.php',
            type: 'POST',
            data: {
                'message_id': message_id,
                'is_received': 'no'
            },
            success: function(response) {
                message_container.remove();
            }
        });

        return false;
    });

    element.find(".delete-received-message").click(function() {
        
        let message_container = $(this);
        while(!message_container.hasClass("message-global-container")) {
            message_container = message_container.parent();
        } 
        let message_id = null;
        if(message_container.hasClass("romrc")) {
            message_id = message_container.find(".message_id").val();
        } else {
            message_id = $(this).parent().find(".message_id").val();
        }
        
        $.ajax({
            url: root + 'api/messages/DELETE.php',
            type: 'POST',
            data: {
                'message_id': message_id,
                'is_received': 'yes'
            },
            success: function(response) {
                message_container.remove();
            }
        });
        console.log(message_container);

        return false;
    });

    element.find(".reply-button").click(function() {
        
        let message = '';
        let message_id = '';
        let global_container = $(this);
        while(!global_container.hasClass("message-global-container")) {
            global_container = global_container.parent();
        }
        if(global_container.hasClass("romrc")) {
            message_id = $(this).parent().find(".message_id").val();
            message = global_container.find(".received_replied_message_text").text();
            if(message.length > 12) {
                message = message.substring(0, 11) + " ..";
            }
            console.log("reply message text: " + ", id: " + message_id);
        } else {
            message_id = $(this).parent().find(".message_id").val();
            message = global_container.find(".message-text").text();
            if(message.length > 12) {
                message = message.substring(0, 11) + " ..";
            }
            console.log("normal message text: " + message + ", id: " + message_id);
        }

        $(".reply-container").find(".message-text-rep").text(message);
        $(".reply-container").find(".replied-message-id").val(message_id);
        $(".reply-container").css("display", "flex");
        $("#chat-text-input").attr("placeholder", "Reply ..");
        let padding_left = 2 + 30 + $(".reply-container").width();
        $("#chat-text-input").css("paddingLeft", padding_left);
        $("#chat-text-input").focus();

        return false;
    });

    $(".original-message-replied-container, .received-original-message-replied-container").click(function() {
        let original_message_id = $(this).find(".original_mid").val();      
        let message_container = $(this).parent();
        let height_from_bottom_to_original = 0;
        message_container = $(".message-global-container").last();
        console.log(message_container)

        while(message_container.find(".message_id").val() != original_message_id) {
            height_from_bottom_to_original += message_container.height();
            message_container = message_container.prev();
        }

        let scroll_target = document.getElementById("chat-container").scrollHeight - height_from_bottom_to_original;
        let pr = message_container.prev();
        if(pr != null) {
            scroll_target -= pr.height() + 8;
        }

        console.log(message_container);
        $("#chat-container").animate({
            scrollTop: scroll_target
        }, 2000, function() {
            message_container.animate({
                opacity: 0.6
            }, 300, function() {
                message_container.animate({
                    opacity: 1
                }, 300);
            })
        });
        console.log("Chat");
    });
}

function handle_chat_container_elements_events() {
    $(".message-input-box").find("#close-reply-container").click(function() {
        $(this).parent().css("display", "none");
        $("#chat-text-input").attr("placeholder", "Message...");
        $("#chat-text-input").css("paddingLeft", "15px");
        $("#chat-text-input").focus();
        return false;
    });
}

let receiver_user_id = null;
function waitForMessages() {
    let url = root + "server/long-polling.php";
    let values = {
        "receiver": $("#second-chat-part").find(".chat-receiver").val()
    }
    $.ajax({
        url: url,
        type: "POST",
        data: values,
        success: function(response) {
            console.log("Message");
            notification_sound_play();
            $("#chat-container").append(response);
            handle_message_elements_events($(".message-global-container").last());
            $("#chat-container").scrollTop($("#chat-container").prop("scrollHeight"));
            waitForMessages();
        }
    });
}

function track_message_writing() {
    let url = root + "server/message_writing_notifier.php";
    let values = {
        "receiver": $("#second-chat-part").find(".chat-receiver").val()
    }
    $.ajax({
        url: url,
        type: "POST",
        data: values,
        success: function(response) {
            if(response["finished"]) {
                $(".message_writing_notifier_text").css("display", "none");
            } else {
                $(".message_writing_notifier_text").css("display", "block");
            }
            track_message_writing();
        }
    });
}

function notification_sound_play() {
    let audio = new Audio(root+'public/assets/audios/tone.mp3');
    audio.play();
}
function open_friend_chat_section($element) {
    if($element.hasClass("friends-chat-item")) {
        $(".friend-chat-discussion-item-wraper").css("background-color", "rgb(30, 30, 30)");
    }
    let captured_id = $element.find(".receiver").val();
    let current_id = $element.find(".sender").val();
    var values = {
        'sender': current_id,
        'receiver': captured_id
    };

    let url = root + "layouts/chat/generate_chat_container.php";

    if(discussion_chat_opened) {
        $("#second-chat-part").remove();
    }

    $.ajax({
        type: "POST",
        url: url,
        data: values,
        success: function(data) {
            $("#no-discussion-yet").remove();
            $("#chat-global-container").append(data);
            handle_chat_container_elements_events();
            $("#chat-container").height($(window).height() - 200); 
            $.ajax({
                type: 'POST',
                url: root + 'api/messages/get_friend_messages.php',
                data: values,
                success: function(data) {
                    $("#chat-container").append(data);
                    handle_message_elements_events($(".message-global-container"));
                    $("#chat-container").scrollTop($("#chat-container").prop("scrollHeight"));
                    receiver_user_id = $element.find(".receiver").val();
                    waitForMessages();
                    track_message_writing();
                }
            });

            $("#send-message-button").click(function() {
                let chat_text_content = $('#second-chat-part').find("#chat-text-input").val();
                let chat_values = values;
                chat_values.message = chat_text_content;
                $.ajax({
                    type: "POST",
                    url: root + "api/messages/Send.php",
                    data: values,
                    success: function(data) {
                        $("#chat-container").append(data);

                        $('#second-chat-part').find("#chat-text-input").val("");
                        message_writing_notifier = 0;
                        $.ajax({
                            type: "POST",
                            url: root + "api/messages/message_writing_notifier/delete.php",
                            data: values,
                            success: function(data) {
                                console.log("Deleted");
                            }
                        });
                        handle_message_elements_events($(".message-global-container").last());

                        $("#chat-container").scrollTop($("#chat-container").prop("scrollHeight"));
                    }
                });
            });

            message_writing_notifier = 0;
            $('#second-chat-part').find("#chat-text-input").on({
                input: function() {
                    if(!message_writing_notifier) {
                        $.ajax({
                            type: "POST",
                            url: root + "api/messages/message_writing_notifier/add.php",
                            data: values,
                            success: function(data) {
                                console.log("Registered");
                            }
                        });

                        message_writing_notifier++;
                    }
                }
            })
            $('#second-chat-part').find("#chat-text-input").keyup(function() {
                if(!this.value) {
                    message_writing_notifier = 0;
                    $.ajax({
                        type: "POST",
                        url: root + "api/messages/message_writing_notifier/delete.php",
                        data: values,
                        success: function(data) {
                            message_writing_notifier = 0;
                            console.log("Deleted");
                        }
                    });
                }
            });
            discussion_chat_opened = true;
        }
    });
}

window.onresize = function() {
    $("#second-chat-part").height($(window).height() - 55);
    $("#first-chat-part").height($(window).height() - 55);
    // $("#friends-chat-container").height($(window).height() - 402);
}


//Mới
$("#edit-profile-button").parent().find(".viewer").css("maxHeight", 700);

if(urlParams.get('edit') !== null) {
    $("#edit-profile-button").parent().find(".viewer").css("display", "block");
}

$(".picture-back-color").css("color", $("#first-section").css("backgroundColor"));

let p_p_max_height = 150;
let profile_p_height = $(".profile-picture").height();
let profile_p_width = $(".profile-picture").width();
let one_hundred_perc = profile_p_height + profile_p_width;
let height_perc = profile_p_height * 100 / one_hundred_perc;
let width_perc = profile_p_width * 100 / one_hundred_perc;

$(".profile-picture").height(p_p_max_height);
let calc_width = width_perc * p_p_max_height / height_perc;
$(".profile-picture").width(calc_width);

$(".viewer").css({
    width: $("body").width(),
    height: $("body").height()
})

$(".close-viewer").click(function() {
    $(".viewer").css("display", "none");
    $("body").css("overflow-y", "auto");
    return false;
})

$("body").click(function() {
    $("body").css("overflow-y", "auto");
})

$("#edit-profile-button").on({
    click: function() {
        let viewer = $(this).parent().find(".viewer");
        viewer.css("display", "block");
        $("body").css("overflow-y", "hidden");
        return false;
    }
});

$(".profile-picture").on({
    mouseenter: function() {
        $(".shadow-profile-picture").css("opacity", "0.2");
    },
    mouseleave: function() {
        $(".shadow-profile-picture").css("opacity", "0");
    },
    click: function() {
        let viewer = $(this).parent().parent().find(".viewer");
        viewer.css("display", "block");
        viewer.find(".profile-picture-preview").attr("src", $(".profile-picture").attr("src"));

        let height = $(".profile-picture-preview").height();
        let width = $(".profile-picture-preview").width();
        
        if(height > width) {
            $(".profile-picture-preview").width($(".profile-picture-preview-container").width());
        } else {
            $(".profile-picture-preview").height($(".profile-picture-preview-container").height());
        }

        return false;
    }
});

let former_changer_dim = $(".former-picture-dim");
let former_changer_height = former_changer_dim.height();
let former_changer_width = former_changer_dim.width();

if(former_changer_height > former_changer_width) {
    former_changer_dim.width("100%");
} else {
    former_changer_dim.height("100%");
}

$("#picture-changer-container").on({
    mouseenter: function() {
        $(this).find(".former-picture-shadow, .change-image-icon").css("display", "block");
    },
    mouseleave: function() {
        $(this).find(".former-picture-shadow, .change-image-icon").css("display", "none");
    }
});

$('#change-avatar').change(function(event) {
    if(this.files && this.files[0]) {
        let avatar = $(".former-picture-dim").get(0);
        let reader = new FileReader();

        reader.onload = function(){
            avatar.src = reader.result;
        }
        reader.readAsDataURL(event.target.files[0]);
    }
    console.log("Success");
});

$(".friends-chat-item").click(function() {
    $(".viewer").css("display", "none");
})

$(".user-info-section-link").on( {
    mouseenter: function() {
        $(this).find("div p").css("textDecoration", "underline");
    },
    mouseleave: function() {
        $(this).find("div p").css("textDecoration", "none");
    }
}
);

$(".user-media-post").css("height", $(".user-media-post").css("width"))
$("#change-cover-button, #change-picture-button").click(function(event) {
    
});

$(".user-media-post").each(function(index, obj) {
    let img = $(obj).find(".user-media-post-img");

    if(img.width() >= img.height()) {
        img.css("height", "100%");
    } else {
        img.css("width", "100%");
    }
});

$(".user-media-post").click(function() {;

    let post_id = $(this).find(".pid").val();
    window.location.href = root + "post-viewer.php?pid=" + post_id;
});
