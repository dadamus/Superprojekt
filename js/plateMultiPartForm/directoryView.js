/**
 * Created by dawid on 11.07.2017.
 */

var $addNewMultiDirectory;
var plateMultiPartUrl = "/engine/costing/plateMultiPart.php";
var $multiWrapper = $("#multi-wrapper");

/**
 * @param filter
 */
function loadDirs(filter) {
    var filterLoad = "";

    if (typeof filter !== 'undefined') {
        filterLoad = "&filter=" + filter;
    }

    $.ajax({
        url: plateMultiPartUrl + "?action=getDirectory" + filterLoad
    }).done(function (response) {
        var $multiDirectoryViewContainer = $("#multiDirectoryViewContainer");
        $multiDirectoryViewContainer.html(response);
        $addNewMultiDirectory = $("#addNewMultiDirectory");

        var data = response.replace(/\n| /gi, "");
        if (data.length <= 1) {
            $addNewMultiDirectory.prop("disabled", false);
        } else {
            $addNewMultiDirectory.prop("disabled", true);
        }
        $multiDirectoryViewContainer.off();

        $multiWrapper.on("click", "tr.multiDirChoose", function () {
            chooseDirectory($(this).data("id"));
        });
    });
}

/**
 * @param name
 */
function searchDirectory(name) {
    validDirectoryName(name);
    loadDirs(name);
}

var multiDirecotrySearchTimeout;

/**
 * @param name
 */
function setMultiDirectorySearch(name) {
    clearTimeout(multiDirecotrySearchTimeout);
    multiDirecotrySearchTimeout = setTimeout(function () {
        searchDirectory(name);
    }, 500);
}

/**
 * @param $input
 */
function addErrorToInput($input)
{
    $input.parent().addClass("has-error");
}

/**
 * @param name
 * @returns {boolean}
 */
function validDirectoryName(name) {
    var parts = name.split('/');
    var $input = $("#multiDirectoryInput");

    if (parts.length !== 3) {
        addErrorToInput($input);
        return false;
    }

    if (parseInt(parts[0]) <= 0) {
        addErrorToInput($input);
        return false;
    }

    if (parseInt(parts[1]) <= 0 || parseInt(parts[1]) > 12 || !$.isNumeric(parts[1])) {
        addErrorToInput($input);
        return false;
    }

    if (parseInt(parts[2]) <= 0 || !$.isNumeric(parts[2])) {
        addErrorToInput($input);
        return false;
    }

    $input.parent().removeClass("has-error");
    return true;
}

/**
 * @param name
 */
function addNewDirectory(name) {
    if (validDirectoryName(name)) {
        $.ajax({
            method: "POST",
            data: "name=" + name,
            url: plateMultiPartUrl + "?action=addDirectory"
        }).done(function (response) {
            console.log(response);
            chooseDirectory(response);
        });
    }
}

/**
 * @returns {Array}
 */
function getDetails()
{
    var details = [];
    $("input[name='selected[]']:checked").each(function () {
        details.push($(this).val());
    });
    return details;
}

/**
 * @param id
 * @returns {boolean}
 */
function chooseDirectory(id)
{
    if (typeof id === undefined) {
        console.log("Bledne id folderu: " + id);
        return false;
    }

    $("#addToMultiCostingModal").modal('hide');

    var details = JSON.stringify(getDetails());
    var project_id = Cookies.get("plProjectId");

    $.ajax({
        method: "POST",
        data: "dir=" + id + "&details=" + details + "&project_id=" + project_id,
        url: plateMultiPartUrl + "?action=addMPWForm"
    }).done(function (response) {
        $("#multi-mpw-wrapper").html(response);
        $("#createMWPMultipartModal").modal('show');
    });
}

$(document).ready(function () {
    loadDirs();

    var $multiWrapper = $("#multi-wrapper");

    $multiWrapper.on("keyup", "#multiDirectoryInput", function () {
        setMultiDirectorySearch($(this).val());
    });

    $multiWrapper.on("click", "#addNewMultiDirectory", function () {
        addNewDirectory($("#multiDirectoryInput").val());
    });
});
