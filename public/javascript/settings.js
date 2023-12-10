$(".button-with-suboption").click(function() {
    let contains = $(this).find(".button-subotions-container").length > 0;

    if(contains) {
        if($(this).find(".button-subotions-container").css("display") == "none") {
            $(this).find(".button-subotions-container").css("display", "block");
            let back = root+"public/assets/images/icons/down-arrow.png";
            $(this).find(".has-suboption").css("backgroundImage","url('"+back+"')");
        }
        else {
            let back = root+"public/assets/images/icons/right-arrow.png";
            $(this).find(".has-suboption").css("backgroundImage","url('"+back+"')");
            $(this).find(".button-subotions-container").css("display", "none");
        }
        return false;
    }
});
$("#assets-wrapper").on({
    mouseenter: function() {
        $("#assets-wrapper").css("backgroundColor", "rgb(50,50,50)");
    },
    mouseleave: function() {
        $("#assets-wrapper").css("backgroundColor", "rgb(45,45,45)");
    }
});
$("#avatar-input").change(function(event) {
    if (this.files && this.files[0]) {
        let picture = $("#setting-picture").get(0);
        let reader = new FileReader();

        reader.onload = function(){
            picture.src = reader.result;
        }
        reader.readAsDataURL(event.target.files[0]);
    }
});
function imageIsLoaded(e) {
    $('#myImg').attr('src', e.target.result);
    $('#yourImage').attr('src', e.target.result);
};
$(".logout-button").click(function() {
    $("#logout-form").submit();
});