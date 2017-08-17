/**
 * Created by dawid on 15.07.2017.
 */

var plateMultiPartUrl = "/engine/costing/plateMultiPart.php";
var versions;

function mpwMultiLoad() {
    versions = JSON.parse($("input[name='mpw_versions']").val());
}

/**
 * @returns {boolean}
 */
function checkMpwReady() {
    var done = true;
    $("#multi-mpw-wrapper table input").each(function () {
        if ($(this).val() === null || $(this).val() === "") {
            if ($(this).attr("required") === "required") {
                done = false;
            }
        }
    });

    var version = $("#multi-mpw-wrapper select[name='version']").val();
    if (version == 0) {
        done = false;
    }

    if (done) {
        activateSubmit();
    }
    return done;
}

/**
 * @param version
 * @param detailId
 * @returns {boolean}
 */
function searchForDetail(version, detailId) {
    var version_data = versions[version];
    for (var key in version_data) {
        var detail = version_data[key];

        if (detail.id == detailId) {
            return true;
        }
    }

    return false;
}

function versionCheck() {
    var version = $("#multi-mpw-wrapper select[name='version']").val();
    if (version == 0) {
        return true;
    }

    var $multiMPWVersion = $("#multiMPWVersion");
    var $multiMPWVersionWrapper = $("#multiMPWVersionWrapper");

    var details = JSON.parse($("#mpw_details").val());

    var response = true;

    if (versions["V" + version].length != details.length) {
        $multiMPWVersionWrapper.html("");

        for (var key in details) {
            var detail_id = details[key];
            console.log("Szukam: " + detail_id);
            if (!searchForDetail("V" + version, detail_id)) {
                $multiMPWVersionWrapper.append("<tr><td>" + detail_id + "</td></tr>");
                response = false;
            }
        }
    }

    if (!response) {
        $multiMPWVersion.fadeIn();
    } else {
        $multiMPWVersion.fadeOut();
    }

    return response;
}

function activateSubmit() {
    $("#multi-mpw-wrapper input[type='button']").prop("disabled", false);
}

$(document).ready(function () {
    var $multimpwwrapper = $("#multi-mpw-wrapper");
    $multimpwwrapper.on("keyup", "input", function () {
        checkMpwReady();
    });

    $("#multi-mpw-wrapper select[name='version']").on("change", function () {
        checkMpwReady();
    });

    $("#createMWPMultipartModal").on("click", "#submitMultiMPW", function (event) {
        event.preventDefault();

        if (!checkMpwReady()) {
            return false;
        }

        if (!versionCheck()) {
            return false;
        }

        $("#multi-mpw-wrapper input[type='button']").prop("disabled", true);

        $.ajax({
            method: "POST",
            data: $("#multiMPWCreate").serialize(),
            url: plateMultiPartUrl + "?action=addMPW"
        }).done(function (response) {
            if (response === "ok") {
                $("#createMWPMultipartModal").modal('hide');
                toastr.options = {
                    "closeButton": true,
                    "debug": false,
                    "positionClass": "toast-top-right",
                    "onclick": null,
                    "showDuration": "1000",
                    "hideDuration": "1000",
                    "timeOut": "5000",
                    "extendedTimeOut": "1000",
                    "showEasing": "swing",
                    "hideEasing": "linear",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut"
                }
                toastr.success("Dodane!", "Auto wycena");
            }
        });
    });
});