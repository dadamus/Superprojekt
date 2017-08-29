$(document).on("ready", function () {
    $("tr.detail-card-element").on("click", function () {
        var dirId = $(this).data("directory-id");
        var detailId = $(this).data("detail-id");

        location.href = "/plateMulti/detail/" + dirId + "/" + detailId + "/";
    });

    $("form#changeDesignerForm").on("submit", function (e) {
        e.preventDefault();
        App.blockUI();

        var dirId = $(this).data("dir-id");
        var userId = $("select#designerId").val();

        $.ajax({
            method: "GET",
            url: "/engine/costing/plateMultiPart.php",
            data: "action=changeDesigner&dir=" + dirId + "&user=" + userId
        }).done(function () {
            App.unblockUI();
            swal("Zmieniłem", "Projektant został zmieniony!", "success");
        });
    });

    $("a#costingBlock").on("click", function () {
        var dirId = $(this).data("dir-id");

        swal({
                title: "Jesteś pewien?",
                text: "Tej opcji nie można cofnąć!",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Tak blokuj!",
                cancelButtonText: "Anuluj",
                closeOnConfirm: false
            },
            function () {
            location.href = "/engine/costing/plateMultiPart.php?action=block&dir=" + dirId;
            });
    });
});