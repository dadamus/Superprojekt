/**
 * Created by dawid on 03.07.2017.
 */

var reload_timer;
var $form = $("#plateSinglePartForm");
var costingId = $form.data("id");

var edited = [];

function blockSite() {
    App.blockUI({boxed: !0});
}
function unblockSite() {
    App.unblockUI();
}

function attributeConsumer(data, part_count) {
    var a_id = 1;
    console.log(data);
    for (var a in data) {
        $("input[name='a" + a_id + "i2']").val(data[a].value * part_count);
        console.log("a" + a_id + "i2 = " + (data[a].value * part_count));
        a_id++;
    }
}

function reloadData()
{
    blockSite();

    $form.find("input").each(function() {
        var name = $(this).attr("id");
        var inputType = $(this).attr("type");
        if (edited[name] === undefined && inputType !== "checkbox") {
            $(this).val(0);
        }
    });

    $.ajax({
        method: "POST",
        data: $form.serialize(),
        url: site_path + "/engine/costing/plateSinglePartForm.php?a=calculate&costingId=" + costingId
    }).done(function (response) {
        var data = JSON.parse(response);

        for (var key in data) {
            var prop = data[key];

            if (key === "attribute") {
                continue;
            }

            for (var name in prop) {
                var value = prop[name];
                console.log("#" + key + "[" + name + "] = " + value);
                $("#" + key + "\\[" + name + "\\]").val(value);
            }
        }

        unblockSite();
        attributeConsumer(data["attribute"], data["inputData"]["part_count"]); //Attribute consumer
    });
}

$(document).ready(function() {
    $form.on("change", "input", function () {
        var add_new = true;
        var name = $(this).attr("id");
        for (var input in edited) {
            if (input === name) {
                add_new = false;
                break;
            }
        }

        if (add_new === true) {
            edited[name] = $(this).val();
        }

        clearTimeout(reload_timer);
        reload_timer = setTimeout(function() {
            reloadData();
        }, 1000);
    });
    
    $("#saveCosting").on("click", function () {
        blockSite();
        $.ajax({
            method: "POST",
            data: $form.serialize(),
            url: site_path + "/engine/costing/plateSinglePartForm.php?a=save&costingId=" + costingId
        }).done(function(response) {
            unblockSite();
        });
    });
});