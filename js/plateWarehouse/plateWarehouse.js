var $sheetCodeInput = $("#paddf input[name='SheetCode']");

function reloadDetails(type) {
    if (typeof (type) == 'undefined') {
        type = 1;
        App.blockUI({boxed: !0});
    }
    var serialize = $("#filter").serialize();
    $.ajax({
        url: "/engine/plateWarehouse.php?act=1&type=" + type,
        type: "POST",
        data: serialize,
    }).done(function (msg) {
        if ($.fn.DataTable.isDataTable("#tab" + type + "-table")) {
            $("#tab" + type + "-table").DataTable().destroy();
        }

        $("#tab" + type + "-content").html(msg);
        $("#tab" + type + "-table").dataTable({
            "pageLength": 50,
            "order": [[4, "desc"]]
        });

        if (type < 4) {
            var ntype = type + 1;
            reloadDetails(ntype);
        } else {
            App.unblockUI();
        }
    });
}

function resetFilter() {
    var $filter = $("#filter select");

    $filter.val(0);
    $filter.selectpicker('render');
    $("#filter input").val('');

    $("#filter input[name='date']").val('');

    reloadDetails();
    $.uniform.update();
}

function loadProvider() {
    $.ajax({
        url: "/engine/class/provider.php?a=1"
    }).done(function (msg) {
        $("#plist").html(msg);
        $(".bs-select").selectpicker('refresh');
    });
}

var provider = "";
var cpm = 1;
var sheet_code_ready = false;

function getFloat(value, def) {
    var _v = parseFloat(value.replace(",", "."));
    if (_v > 0) {
        return _v;
    }

    return def;
}

var $newSheetDate = $("#newSheetDate");

function SheetCodeGenerator() {
    var _scr = true;

    var x = getFloat($("#newSheetWidth").val(), "x");
    var y = getFloat($("#newSheetHeight").val(), "y");
    var z = getFloat($("#newSheetThickness").val(), "z");

    var mm = "M";
    var yy = "Y";

    var date = moment($newSheetDate.val(), "DD-MM-YYYY");
    if (date.isValid()) {
        mm = date.months() + 1;
        if (mm < 10) {
            mm = "0" + mm;
        }

        yy = date.years();
    }

    $("#newSheetCode").val(x + "X" + y + "X" + z + "-" + mm + "" + yy);

    if (x == "x" || y == "y" || z == "z" || !date.isValid()) {
        _scr = false;
    }

    sheet_code_ready = _scr;
}

$(document).ready(function () {
    $('select[name="MaterialType"]').on('change', function () {
        var value = $(this).val();

        var $input = $('select[name="MaterialTypeName"]');
        $input.find('option[data-type="' + value + '"]').prop('hidden', false);
        $input.find('option[data-type!="' + value + '"]').prop('hidden', true);

        $input.find('option:first').prop('hidden', false).prop('selected', true);
    });

    $(".bs-select").selectpicker({iconBase: "fa", tickIcon: "fa-check"});

    $(".date-picker").datetimepicker({
        minView: 2,
        language: 'pl',
        pickerPosition: "top-left"
    });
    $("#defaultrange").daterangepicker({
            opens: App.isRTL() ? "left" : "right",
            format: "MM/DD/YYYY",
            separator: " do ",
            startDate: moment().subtract("days", 29),
            endDate: moment(),
            ranges: {
                "Dziś": [moment(), moment()],
                "Wczoraj": [moment().subtract("days", 1), moment().subtract("days", 1)],
                "Ostatnie 7 dni": [moment().subtract("days", 6), moment()],
                "Ostatnie 30 dni": [moment().subtract("days", 29), moment()],
                "Ten miesiąc": [moment().startOf("month"), moment().endOf("month")],
                "Ostatni miesiąc": [moment().subtract("month", 1).startOf("month"), moment().subtract("month", 1).endOf("month")]
            }
        },
        function (t, e) {
            $("#defaultrange input").val(t.format("YYYY-MM-DD") + " : " + e.format("YYYY-MM-DD"))
        });

    reloadDetails();
    //Filter
    $("#filter").submit(function (e) {
        e.preventDefault();
        reloadDetails();
    });

    $("#b_reset").on("click", function () {
        resetFilter();
    });

    $("#newp").on("click", function () {
        loadProvider();
        $("#mnewp").modal('show');
    });
    $("#bpadd").on("click", function () {
        App.blockUI({boxed: !0});
        $.ajax({
            url: "/engine/class/provider.php?a=2&name=" + $("#nprovider").val()
        }).done(function () {
            loadProvider();
            $("#maddp").modal('hide');
            App.unblockUI();
        });
    });

    var $padf = $("#paddf input");

    $("#npn").on("click", function () {
        SheetCodeGenerator();
        provider = $("#plist").val();
        $padf.parent().removeClass("has-error");
        $("#mnewp").modal('hide');

        var $mnewp2 = $("#mnewp2");
        $mnewp2.modal('show');
        $mnewp2.find("input").val("");
    });
    $("#cpm").on("click", "a", function () {
        cpm = parseInt($(this).attr("id"));
        $("#cpmt").html($(this).html());
    });

    $("#npn2").on("click", function () {
        $("#paddf").submit();
    });

    //CodeGenerate
    $padf.on("keyup", function () {
        SheetCodeGenerator();
    });

    $newSheetDate.on("change", function () {
        SheetCodeGenerator();
    });

    $("#paddf").on("submit", function (e) {
        e.preventDefault();

        if (sheet_code_ready == false) {
            $sheetCodeInput.parent().addClass("has-error");
            return false;
        }

        if ($('select[name="MaterialTypeName"]').val() == '') {
            $('select[name="MaterialTypeName"]').parent().addClass("has-error");
            return false;
        }

        var check = true;
        $padf.each(function (index, cobject) {
            if ($(cobject).val().length == 0 && !$(cobject).prop('readonly')) {
                check = false;
                $(cobject).parent().addClass("has-error");
            } else if ($(cobject).parent().hasClass("has-error")) {
                $(cobject).parent().removeClass("has-error");
            }
        });
        if (check == true) {
            var data = $("#paddf").serialize();
            App.blockUI({boxed: !0});
            $.ajax({
                method: "POST",
                data: data + "&pr" + provider + "&cpm=" + cpm,
                url: "/engine/plateWarehouse.php?act=2"
            }).done(function (msg) {
                App.unblockUI();
                if (msg == "e1") {
                    $sheetCodeInput.parent().addClass("has-error");
                } else if (msg == "e2") {
                    $("input[name='SheetCodeComment']").parent().addClass("has-error");
                } else {
                    toastr.success("Blacha została dodana do magazynu id: " + msg, "Dodałem blache!");

                    toastr.options = {
                        "closeButton": true,
                        "debug": false,
                        "positionClass": "toast-bottom-right",
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
                    $("#mnewp2").modal('hide');
                }
                sheet_code_ready = false;
            });
        }
    });
});