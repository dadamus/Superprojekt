$(document).ready(function () {
    $(".ai").on("keyup", function () {
        var id = $(this).data("id");
        var value = $(this).val();
        if (parseFloat(value) > 0) {
            $("#a" + id + "i2").val(partQuantity * parseFloat(value));
        }
    }).on("clck", function () {
        if (parseFloat($(this).val()) === 0) {
            $(this).val("");
        }
    });
    $(".aik").on("keyup", function () {
        var id = $(this).data("id");
        var value = $(this).val();
        if (parseFloat(value) > 0) {
            $("#a" + id + "i1").val(parseFloat(value) / partQuantity);
        }
    }).on("clck", function () {
        if (parseFloat($(this).val()) === 0) {
            $(this).val("");
        }
    });

    $("#saveButton").on("click", function () {
        $.ajax({
            url: "?",
            method: "POST",
            data: $("#count").serialize() + "&save=true"
        }).done(function () {
            location.reload();
        });
    });
});