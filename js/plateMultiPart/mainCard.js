$(document).on("ready", function () {
    $("tr.detail-card-element").on("click", function () {
        var dirId = $(this).data("directory-id");
        var detailId = $(this).data("detail-id");

        location.href = "/plateMulti/detail/" + dirId + "/" + detailId + "/";
    });
});