let root = "/CHAT/";
let current_user_id = null;
function handle_return(data) {
    current_user_id = data;
}
function get_current(handle_return) {
    $.ajax({
        url: root + 'security/get_current_user.php',
        type: 'get',
        success: function(current_user) {
            handle_return(current_user["id"]);
        }
    });
};
get_current(handle_return);

var stillAlive = setInterval(function () {
    update_active_presence();
}, 120000);

function update_active_presence() {
    $.ajax({
        url: root + 'server/update_active_presence.php'
    });
}
