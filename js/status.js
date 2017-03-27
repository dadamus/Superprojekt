//STATUS CHANGE 
$(".status_bar").on("click", ".sb", function () {
    var status = parseInt($(this).parent().attr("id"));
    var selected = Cookies.get("plDetailId");
            var action;
    var _id = this;
    if ($(this).hasClass("btn-success")) {
        action = 2;
    } else {
        action = 1;
    }
    $.ajax({
        method: "POST",
        url: "/engine/status.php?sa=" + action,
        data: {selected: selected, status: status}
    }).done(function (msg) {
        var action = parseInt(msg);
        switch (action) {
            case 1:
                $(_id).addClass("btn-success");
                break;
            case 2:
                $(_id).removeClass("btn-success")
                break;
            case 3:
                alert("Statusu nie można zmienić manualnie!");
                break;
        }
    });
});