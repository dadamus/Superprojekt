var _to;

function refreshMail() {
    clearTimeout(_to);
    $.ajax({
        url: site_path + "/engine/chart/program.php?action=1"
    }).done(function (_response) {
        var response = JSON.parse(_response);
        if (response.length > 0) {
            $("#amd").slideDown();
            for (var key in response) {
                var e_type = response[key].type;
                var e_uid = response[key].uid;

                var ton = false;
                var ttype = "";
                if (e_type == 1) {
                    ttype = "success";
                    ton = true;
                }

                if (ton == true) {
                    toastr[ttype]("Pomyślnie zakutalizowano wycięty detal", "Zaktualizowano");
                }
            }
        }

        //Notifi update
        refreshNotifi();
    }).always(function () {
        _to = setTimeout(refreshMail, 10000);
    });
}

function refreshNotifi() {
    $.ajax({
        url: site_path + "/engine/notification.php?act=1"
    }).done(function (_resp) {
        var resp = JSON.parse(_resp);
        var size = resp.size;
        var html = resp.content;

        $("#nnl_1").html(size);
        $("#nnl_2").html(size);
        $("#nnlist").html(html);
    });
}

function loadNChart(_id) {
    App.blockUI({boxed: !0});
    $.ajax({
        url: site_path + "/engine/notification.php?act=2&eid=" + _id
    }).done(function (_resp) {
        var resp = JSON.parse(_resp);
        _email_id = _id;
        _form_type = resp.form_type;
        $("#nmo .modal-body").html(resp.content);
        $("#nmo").modal("show");

        if (_form_type == 1) {
            $('.select2').select2();
        } else {
            $('.select2').select2();
            $(".knob").knob();
        }
        App.unblockUI();
    });
}

var _form_type;
var _email_id;

$(document).ready(function () {
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "positionClass": "toast-bottom-right",
        "onclick": null,
        "showDuration": "1000",
        "hideDuration": "1000",
        "timeOut": "10000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }

    refreshMail();

    $("#nnlist").on("click", "li", function () {
        var _id = parseInt($(this).attr("id"));
        loadNChart(_id);
    });

    $("#nmo_bs").on("click", function () {
        var value, act;
        $("#nmo").modal("hide");
        App.blockUI({boxed: !0});
        if (_form_type == 1) {
            act = 3;
            value = $("#sf1n").val();
        } else if (_form_type == 2) {
            act = 4;
            var jv = {};

            $(".knob").each(function (index, element) {
                var _id = parseInt($(element).attr("id"));
                jv[_id] = $(element).val();
            });
            
            jv["operator"] = $("#sf2n").val();
            value = JSON.stringify(jv);
        }

        $.ajax({
            url: site_path + "/engine/notification.php?act=" + act + "&eid=" + _email_id + "&val=" + value
        }).done(function (msg) {
            setTimeout(function () {
                loadNChart(_email_id)
            }, 1000);
        });
    });
});