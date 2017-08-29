$(document).ready(function () {
    var $dirContainer = $("#dirContainer");
    var dirId = $dirContainer.data("id");

    $("a.mpw-detail-delete").on("click", function () {
        var $detail = $(this).parent().parent();
        var mpwId = $(this).data("mpw-id");
        var detailId = $(this).data("detail-id");

        swal({
                title: "Jesteś pewien?",
                text: "Chcesz usunąc ten detal z wyceny?",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Tak usuń!",
                cancelButtonText: "Anuluj",
                closeOnConfirm: false
            },
            function () {
                $.ajax({
                    url: "/engine/costing/multiPart.php?action=deleteMpwItem",
                    method: "POST",
                    data: "mpw=" + mpwId + "&detail=" + detailId + "&dir=" + dirId
                }).done(function () {
                    swal("Usunięto", "Detal został usunięty z wyceny!", "success");
                    $detail.hide();
                });
            });
    });
});