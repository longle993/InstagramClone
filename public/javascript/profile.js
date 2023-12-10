const urlParams = new URLSearchParams(window.location.search);

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
    $("body").css("overflow-y", "scroll");
    return false;
})

$("body").click(function() {
    $("body").css("overflow-y", "scroll");
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

$(".profile-picture-preview, .profile-cover-picture-preview, #edit-profile-container, #cover-changer-container").click(function(event) {
    event.stopPropagation();
    
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

$(".viewer").click(function() {
    $(this).css("display", "none");
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