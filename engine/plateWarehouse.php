<?php
$a = @$_GET["act"];
if (!empty($a)) {
    require_once dirname(__FILE__) . '/../config.php';
    require_once dirname(__FILE__) . '/protect.php';
    require_once dirname(__FILE__) . '/class/notification.php';
}
if ($a == 1) {
	$warehouseTypes = [
			0 => "default",
			1 => "default",
			2 => "reservation",
			3 => "deleted",
			4 => "other"
	];

    $type = $warehouseTypes[@$_GET["type"]];

    //FILTR
    $filtr = "";

    if (!empty($_POST["f_SheetCode"])) {
        $filtr .= " AND `SheetCode` LIKE '%" . $_POST["f_SheetCode"] . "%'";
    }
    if (!empty($_POST["f_SheetType"])) {
        foreach ($_POST["f_SheetType"] as $mtype) {
            $filtr .= " AND `MaterialTypeName` = '$mtype'";
        }
    }
    if ($_POST["f_date"] != "") {
        $rdate = str_replace(' ', '', $_POST["f_date"]);
        $edate = explode(':', $rdate);
        if (count($edate) == 2) {
            $filtr .= " AND `date` >= '" . $edate[0] . " 00:00:00' AND `date` <= '" . $edate[1] . " 24:60:60'";
        }
    }
    if (!empty($_POST["f_Width"])) {
        $filtr .= " AND `Width` LIKE '%" . $_POST["f_Width"] . "%'";
    }
    if (!empty($_POST["f_Height"])) {
        $filtr .= " AND `Height` LIKE '%" . $_POST["f_Height"] . "%'";
    }
    if (!empty($_POST["f_Thickness"])) {
        $filtr .= " AND `Thickness` LIKE '%" . $_POST["f_Thickness"] . "%'";
    }

    //die("SELECT * FROM `plate_warehouse` WHERE `type` = '$type' ".$filtr);
    $pselect = $db->query("
	SELECT * 
	FROM `plate_warehouse` 
	LEFT JOIN `T_material` ON `T_material`.MaterialName = `plate_warehouse`.MaterialName
	WHERE `state` = '$type' " . $filtr);
	$data = $pselect->fetchAll(PDO::FETCH_ASSOC);

    $table = "";
    foreach($data as $row) {
        $table .= "<tr><td>" . $row['SheetCode'] . "</td><td>" . $row['MaterialTypeName'] . "</td><td>" . $row['Width'] . "x" . $row['Height'] . "</td><td>" . $row['Thickness'] . "</td><td>" . $row['createDate'] . "</td><td>" . $row['QtyAvailable'] . "</td></tr>";
    }
    die($table);
} else if ($a == 2) { //Insert new plate
    //SheetCode check
    $sc = $db->query("SELECT `id` FROM `plate_warehouse` WHERE `SheetCode` = '" . $_POST["SheetCode"] . "'");
    if ($fsc = $sc->fetch()) {
        die("e1");
    }

    $sc = str_replace([".", ","], ["P", "P"], $_POST["SheetCode"]);

    $sheetCodeComent = strtoupper($_POST["SheetCodeComment"]);
    if (strlen($sheetCodeComent) > 0) {
        if (!ctype_alnum($sheetCodeComent)) {
            die("e2");
        }

        $sc .= "-".$sheetCodeComent;
    }

    $SheetCode = strtoupper($sc);
    $date = date("Y-m-d H:i:s");
    $db->query("INSERT INTO `plate_warehouse` (`SheetCode`, `MaterialName`, `QtyAvailable`, `GrainDirection`, `Width`, `Height`, `SpecialInfo`, `SheetType`, `Price`, `Priority`, `Thickness`, `MaterialTypeName`, `type`, `date`, `pdate`, `ndp`) VALUES ('$SheetCode', '', '" . $_POST['QtyAvailable'] . "', '" . $_POST['GrainDirection'] . "', '" . $_POST['Width'] . "', '" . $_POST['Height'] . "', '" . $_POST['SpecialInfo'] . "', '" . $_POST['SheetType'] . "', '" . $_POST['Price'] . "', '" . $_POST['Priority'] . "', '" . $_POST['Thickness'] . "', '" . $_POST['MaterialTypeName'] . "', '1', '$date', '" . $_POST['pdate'] . "', '" . $_POST['ndp'] . "')");
    die($db->lastInsertId());
}
?>

<div class="row">
    <div class="col-lg-10 col-md-10 col-sm-10">
        <h2 class="page-title">Magazyn blach</h2>
    </div>
    <div class="col-lg-2 col-md-2 col-sm-2" style="padding-top: 15px; text-align: right;">
        <a class="btn btn-outline green" href="javascript:;" id="newp"><i class="fa fa-plus-circle"></i> Dodaj</a>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="portlet box blue-hoki">
            <div class="portlet-title">
                <div class="caption">
                    Filtr
                </div>
            </div>
            <div class="portlet-body">
                <form id="filter" action="?">
                    <div class="row">
                        <div class="col-lg-2 col-md-12" style="margin-bottom: 3px;">
                            <input type="text" class="form-control" name="f_SheetCode" placeholder="SheetCode"/>
                        </div>
                        <div class="col-lg-2 col-md-12" style="margin-bottom: 3px;">
                            <select class="bs-select form-control" multiple data-actions-box="true" name="f_SheetType[]">
                                <?php
                                $material = $db->query("SELECT `name` FROM `material`");
                                foreach ($material as $row) {
                                    echo '<option>' . $row["name"] . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-12" style="margin-bottom: 3px;">
                            <div id="defaultrange">
                                <input type="text" name="f_date" class="form-control" placeholder="Data">
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-12" style="margin-bottom: 3px;">
                            <input type="text" class="form-control" name="f_Width" placeholder="Szerokość">
                        </div>
                        <div class="col-lg-2 col-md-12" style="margin-bottom: 3px;">
                            <input type="text" class="form-control" name="f_Height" placeholder="Wysokość">
                        </div>
                        <div class="col-lg-2 col-md-12" style="margin-bottom: 3px;">
                            <input type="text" class="form-control" name="f_Thickness" placeholder="Grubość">
                        </div>
                    </div>
                    <div class="row" style="text-align: right; padding-right: 15px; margin-top: 5px;">
                        <button type="submit" class="btn green"><i class="fa fa-filter"></i> Filtruj</button>
                        <button type="button" class="btn white" id="b_reset"><i class="fa fa-eraser"></i> Reset</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="portlet light bordered">
            <div class="portlet-body">
                <div class="tabbable-custom nav-justified">
                    <ul class="nav nav-tabs nav-justified">
                        <li class="active">
                            <a href="#tab1" data-toggle="tab" aria-expanded="true">Magazyn</a>
                        </li>
                        <li>
                            <a href="#tab2" data-toggle="tab" aria-expanded="true">Rezerwacje</a>
                        </li>
                        <li>
                            <a href="#tab3" data-toggle="tab" aria-expanded="true">Kosz</a>
                        </li>
                        <li>
                            <a href="#tab4" data-toggle="tab" aria-expanded="true">Inne</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <?php

                        function getTab($id) {
                            echo '<table class="table table-striped table-bordered table-hover dt-responsive" id="tab' . $id . '-table"><thead><tr><th>SheetCode</th><th>Rodzaj</th><th>Wymiary</th><th>Grubość</th><th>Data przyjęcia</th><th>Sztuk</th></tr></thead><tbody id="tab' . $id . '-content"></tbody>
                            </table>';
                        }
                        ?>
                        <div class="tab-pane active" id="tab1">
                            <?php
                            getTab(1);
                            ?>
                        </div>
                        <div class="tab-pane" id="tab2">
                            <?php
                            getTab(2);
                            ?>
                        </div>
                        <div class="tab-pane" id="tab3">
                            <?php
                            getTab(3);
                            ?>
                        </div>
                        <div class="tab-pane" id="tab4">
                            <?php
                            getTab(4);
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="mnewp" class="modal fade modal-scroll modal-overflow" tabindex="-1" data-width="760">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4 class="modal-title">Nowa blacha</h4>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            Wybierz dostawce
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <div id="atab1">
                            <div class="input-group">
                                <select class="bs-select form-control" data-live-search="true" id="plist"></select>
                                <span class="input-group-btn">
                                    <button data-toggle="modal" href="#maddp" class="btn btn-success" type="button">Nowy</button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn green" id="npn">Dalej</button>
        <button type="button" data-dismiss="modal" class="btn btn-outline dar">Zamknij</button>
    </div>
</div>
<div id="mnewp2" class="modal fade modal-scroll" tabindex="-1" data-width="760" data-replace="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4 class="modal-title">Nowa blacha</h4>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            Wybierz dostawce
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <form id="paddf" action="?">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td>Blacha</td>
                                        <td>
                                            <select name="MaterialTypeName" class="form-control"><?php
                                                $mtn = $db->query("SELECT `id`, `name` FROM `material`");
                                                foreach ($mtn as $row) {
                                                    echo '<option>' . $row["name"] . '</option>';
                                                }
                                                ?></select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Wymiary</td>
                                        <td><input type="text" class="form-control" name="Width" id="newSheetWidth" placeholder="Szerokość"/></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td><input type="text" class="form-control" name="Height" id="newSheetHeight" placeholder="Wysokość"/></td>
                                    </tr>
                                    <tr>
                                        <td>Grubość</td>
                                        <td><input type="text" class="form-control" name="Thickness" id="newSheetThickness" /></td>
                                    </tr>
                                    <tr>
                                        <td>Sztuk</td>
                                        <td><input type="text" class="form-control" name="QtyAvailable"/></td>
                                    </tr>
                                    <tr>
                                        <td>GrainDirection</td>
                                        <td><select name="GrainDirection" class="form-control"><option value="1">Horizontal</option><option value="2">Vertical</option></select></td>
                                    </tr>
                                    <tr>
                                        <td>SpecialInfo</td>
                                        <td><select name="SpecialInfo" class="form-control"><option value="0">Normal</option><option value="1">Stal farbowana</option><option value="2">Folia</option></select></td>
                                    </tr>
                                    <tr>
                                        <td>SheetType</td>
                                        <td><select name="SheetType" class="form-control"><option>Standard</option></select></td>
                                    </tr>
                                    <tr>
                                        <td>Priority</td>
                                        <td><select name="Priority" class="form-control"><option>1</option></select></td>
                                    </tr>
                                    <tr>
                                        <td>SheetCode</td>
                                        <td>
                                            <div class="row">
                                                <div class="col-sm-12 col-lg-6">
                                                    <input type="text" name="SheetCode" id="newSheetCode" class="form-control" readonly/>
                                                </div>
                                                <div class="col-sm-12 col-lg-1" style="text-align: center">
                                                    <span style="font-size: xx-large">-</span>
                                                </div>
                                                <div class="col-sm-12 col-lg-5">
                                                    <input type="text" name="SheetCodeComment" class="form-control"/>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Waga</td>
                                        <td><input type="text" name="Weight" class="form-control"/></td>
                                    </tr>
                                    <tr>
                                        <td>Cena</td>
                                        <td>
                                            <div class="input-group">
                                                <input type="text" name="Price" class="form-control"/>
                                                <div class="input-group-btn">
                                                    <button type="button" class="btn btn-default" tabindex="-1" id="cpmt">zł/kg</button>
                                                    <button type="button" class="btn green dropdown-toggle" data-toggle="dropdown" tabindex="-1"><i class="fa fa-angle-down"></i></button>
                                                    <ul class="dropdown-menu pull-right" role="menu" id="cpm">
                                                        <li><a href="javascript:;" id="1_cpm">zł/kg</a></li>
                                                        <li><a href="javascript:;" id="2_cpm">zł/arkusz</a></li>
                                                        <li><a href="javascript:;" id="3_cpm">zł/paczka</a></li>
                                                        <li><a href="javascript:;" id="4_cpm">zł/1mm^2</a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Dzień przyjęcia</td>
                                        <td>
                                            <div class="input-group">
                                                <input class="form-control form-control-inline input-medium date-picker" size="16" id="newSheetDate" type="text" name="pdate" data-date-format="dd-mm-yyyy" value="">
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Numer dokumentu przyjęcia</td>
                                        <td><input type="text" class="form-control" name="ndp"/></td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn green" id="npn2">Zapisz</button>
        <button type="button" data-dismiss="modal" class="btn btn-outline dar">Zamknij</button>
    </div>
</div>
<div id="maddp" class="modal fade" tabindex="-1" data-width="760">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4 class="modal-title">Nowy dostawca</h4>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            Wybierz dostawce
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <input type="text" name="nprovider" class="form-control" id="nprovider" placeholder="Nazwa"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn green" id="bpadd">Dodaj</button>
        <button type="button" data-dismiss="modal" class="btn btn-outline dar">Zamknij</button>
    </div>
</div>


<script type="text/javascript">
    var $sheetCodeInput = $("#paddf input[name='SheetCode']");

    function reloadDetails(type)
    {
        if (typeof (type) == 'undefined') {
            type = 1;
            App.blockUI({boxed: !0});
        }
        var serialize = $("#filter").serialize();
        $.ajax({
            url: "<?php echo $site_path; ?>/engine/plateWarehouse.php?act=1&type=" + type,
            type: "POST",
            data: serialize,
        }).done(function (msg) {
            if ($.fn.DataTable.isDataTable("#tab" + type + "-table"))
            {
                $("#tab" + type + "-table").DataTable().destroy();
            }

            $("#tab" + type + "-content").html(msg);
            $("#tab" + type + "-table").dataTable({
                "pageLength": 50
            });

            if (type < 4) {
                var ntype = type + 1;
                reloadDetails(ntype);
            } else
            {
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
            url: "<?php echo $site_path; ?>/engine/class/provider.php?a=1"
        }).done(function (msg) {
            $("#plist").html(msg);
            $(".bs-select").selectpicker('refresh');
        });
    }

    var provider = "";
    var cpm = 1;
    var sheet_code_ready = false;

    function getFloat(value, def)
    {
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
        if (date.isValid())
        {
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
        $(".bs-select").selectpicker({iconBase: "fa", tickIcon: "fa-check"});

        $(".date-picker").datetimepicker({
            minView : 2,
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
                "Ostatni miesiąc": [moment().subtract("month", 1).startOf("month"), moment().subtract("month", 1).endOf("month")]},
            minDate: "01/01/2012",
            maxDate: "12/31/2018"},
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
                url: "<?php echo $site_path; ?>/engine/class/provider.php?a=2&name=" + $("#nprovider").val()
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
                    url: "<?php echo $site_path; ?>/engine/plateWarehouse.php?act=2"
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
</script>