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

    //NESTING PLATE UPDATE
    $db->query("
        UPDATE plate_warehouse
        SET state = '" . $warehouseTypes[4] . "'
        WHERE SheetCode LIKE '%NEST%'
    ");

    //Jeszcze trash jak jest 0 sztuk to trash
    SheetTrash::trash();

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
    if (!empty($_POST["f_Width_Min"])) {
        $filtr .= " AND `Width` >= " . $_POST["f_Width_Min"];
    }
    if (!empty($_POST["f_Height_Min"])) {
        $filtr .= " AND `Height` >= " . $_POST["f_Height_Min"];
    }
    if (!empty($_POST["f_Width_Max"])) {
        $filtr .= " AND `Width` <= " . $_POST["f_Width_Max"];
    }
    if (!empty($_POST["f_Height_Max"])) {
        $filtr .= " AND `Height` <= " . $_POST["f_Height_Max"];
    }
    if (!empty($_POST["f_Thickness"])) {
        $filtr .= " AND `Thickness` LIKE '%" . $_POST["f_Thickness"] . "%'";
    }

    //die("SELECT * FROM `plate_warehouse` WHERE `type` = '$type' ".$filtr);
    $pselect = $db->query("
	SELECT 
	p.SheetCode,
	m.MaterialTypeName,
	p.Width,
	p.Height,
	m.Thickness,
	p.createDate,
	p.QtyAvailable
	FROM `plate_warehouse` p
	LEFT JOIN `T_material` m ON m.MaterialName = p.MaterialName
	WHERE p.state = '$type' " . $filtr);
    $data = $pselect->fetchAll(PDO::FETCH_ASSOC);

    $table = "";
    foreach ($data as $row) {
        $table .= "<tr><td></td><td>" . $row['SheetCode'] . "</td><td>" . $row['MaterialTypeName'] . "</td><td>" . $row['Width'] . "x" . $row['Height'] . "</td><td>" . $row['Thickness'] . "</td><td>" . $row['createDate'] . "</td><td>" . $row['QtyAvailable'] . "</td><td></td></tr>";
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

        $sc .= "-" . $sheetCodeComent;
    }

    $SheetCode = strtoupper($sc);
    $date = date("Y-m-d H:i:s");

    $densityQuery = $db->prepare("
        SELECT
        m.cubis as density
        FROM T_material t
        LEFT JOIN material m ON m.name = t.MaterialTypeName
        WHERE 
        t.MaterialName = :matName
    ");
    $densityQuery->bindValue(":matName", $_POST["MaterialTypeName"], PDO::PARAM_STR);
    $densityQuery->execute();

    $densityData = $densityQuery->fetch();
    $density = $densityData["density"];

    /*
     * Typ ceny
     * 1 - zl/kg
     * 2 - zl/szt netto
    */
    $cpm = intval($_GET['cpm']);

    $cena_ramka = 0;
    $koszty = 0;
    $arkusz_aktualna = 0;
    $cena_zl_kg = 0;
    $roznica_wagi = 0;

    $waga_arkusz = (float)$_POST["Weight"] / (int)$_POST['QtyAvailable'];
    $waga_program =
        (float)$_POST['Width']
        * (float)$_POST['Height']
        * (float)$_POST["Thickness"]
        * $density
        / 1000;
    $powierzchnia_ramki =
        (45 * (float)$_POST['Width']) +
        (30 * (float)$_POST['Height'] - 30);
    $cena_ramka =
        $powierzchnia_ramki
        / (float)($_POST['Width'])
        * (float)($_POST['Height'])
        * (float)($_POST['Price']);
    $koszty = (float)$_POST["AdditionalPrice"] / (int)$_POST['QtyAvailable'];
    $roznica_wagi = ($waga_arkusz - $waga_program) / $waga_program;

    if ($cpm == 1) {
        $cena_baza =
            (float)$_POST['Price']
            * (float)$_POST['Width']
            / (int)$_POST['QtyAvailable'];
        $arkusz_aktualna = $cena_baza + $cena_ramka + $koszty;
    } else if ($cpm == 2) {
        $arkusz_aktualna =
            (float)$_POST['Price']
            + $cena_ramka
            + $koszty;
        $cena_zl_kg = $arkusz_aktualna / $waga_program;
    }

    $SqlBuilder = new sqlBuilder(sqlBuilder::INSERT, "plate_warehouse");
    $SqlBuilder->bindValue("SheetCode", $SheetCode, PDO::PARAM_STR);
    $SqlBuilder->bindValue("MaterialName", $_POST["MaterialTypeName"], PDO::PARAM_STR);
    $SqlBuilder->bindValue("QtyAvailable", $_POST['QtyAvailable'], PDO::PARAM_INT);
    $SqlBuilder->bindValue("StartQty", $_POST['QtyAvailable'], PDO::PARAM_INT);
    $SqlBuilder->bindValue("GrainDirection", $_POST['GrainDirection'], PDO::PARAM_STR);
    $SqlBuilder->bindValue("Width", $_POST['Width'], PDO::PARAM_STR);
    $SqlBuilder->bindValue("Height", $_POST['Height'], PDO::PARAM_STR);
    $SqlBuilder->bindValue("SpecialInfo", $_POST['SpecialInfo'], PDO::PARAM_STR);
    $SqlBuilder->bindValue("SheetType", $_POST['SheetType'], PDO::PARAM_STR);
    $SqlBuilder->bindValue("Price", $_POST['Price'], PDO::PARAM_STR);
    $SqlBuilder->bindValue("Priority", $_POST['Priority'], PDO::PARAM_STR);
    $SqlBuilder->bindValue("date", $date, PDO::PARAM_STR);
    $SqlBuilder->bindValue("pdate", $_POST['pdate'], PDO::PARAM_STR);
    $SqlBuilder->bindValue("ndp", $_POST['ndp'], PDO::PARAM_STR);
    $SqlBuilder->bindValue("OwnerId", $_POST["OwnerId"], PDO::PARAM_INT);
    $SqlBuilder->bindValue("UserID", $_SESSION["login"], PDO::PARAM_INT);

    $SqlBuilder->bindValue("Price_kg", $cena_zl_kg, PDO::PARAM_STR);
    $SqlBuilder->bindValue("costs", $koszty, PDO::PARAM_STR);
    $SqlBuilder->bindValue("actual_weight", $waga_arkusz, PDO::PARAM_STR);
    $SqlBuilder->bindValue("program_weight", $waga_program, PDO::PARAM_STR);
    $SqlBuilder->bindValue("sheet_weight", $waga_arkusz, PDO::PARAM_STR);
    $SqlBuilder->bindValue("difference_weight", $roznica_wagi, PDO::PARAM_STR);
    $SqlBuilder->bindValue("sheet_actual_price", $arkusz_aktualna, PDO::PARAM_STR);
    $SqlBuilder->bindValue("synced", 1, PDO::PARAM_STR);

    $id = $db->lastInsertId();

    PlateWarehouseJob::NewJob(PlateWarehouseJob::JOB_NEW, $id, [
        'SheetCode' => $SheetCode,
        'MaterialName' => $_POST["MaterialTypeName"],
        'QtyAvailable'=> $_POST['QtyAvailable'],
        'GrainDirection' => $_POST['GrainDirection'],
        'Width' => $_POST['Width'],
        'Height' => $_POST['Height'],
        'SpecialInfo' => $_POST['SpecialInfo'],
        'Comment' => '',
        'SheetType' => $_POST['SheetType'],
        'SkeletonFile' => '',
        'SkeletonData' => '',
        'MD5' => '',
        'Price' => $_POST['Price'],
        'Priority' => $_POST['Priority'],
    ]);

    die($id);
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
                            <select class="bs-select form-control" multiple data-actions-box="true"
                                    name="f_SheetType[]">
                                <?php
                                $material = $db->query("SELECT `name` FROM `material` ORDER BY name DESC");
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
                            <input type="text" class="form-control" name="f_Width_Min" placeholder="X min">
                            <input type="text" class="form-control" name="f_Width_Max" placeholder="X max">
                        </div>
                        <div class="col-lg-2 col-md-12" style="margin-bottom: 3px;">
                            <input type="text" class="form-control" name="f_Height_Min" placeholder="Y min">
                            <input type="text" class="form-control" name="f_Height_Max" placeholder="Y max">
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

                        function getTab($id)
                        {
                            echo '<table 
                            class="table table-striped table-bordered table-hover dt-responsive" 
                            id="tab' . $id . '-table">
                                <thead>
                                    <tr>
                                        <th style="width: 100px">
                                            <div class="btn-group" style="position: relative; top: 0px;">
                                                <a class="btn btn-sm btn-default">Akcje</a>
                                            </div>
                                        </th>
                                        <th>SheetCode</th>
                                        <th>Rodzaj</th>
                                        <th>Wymiary</th>
                                        <th>Grubość</th>
                                        <th>Data przyjęcia</th>
                                        <th>Sztuk</th>
                                        <th>Akcje</th>
                                    </tr>
                                </thead>
                                <tbody id="tab' . $id . '-content"></tbody>
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
                                    <button data-toggle="modal" href="#maddp" class="btn btn-success"
                                            type="button">Nowy</button>
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
                                        <?php
                                        $mtnq = $db->prepare("SELECT `MaterialName`, `MaterialTypeName` FROM `T_material` ORDER BY MaterialTypeName DESC");
                                        $mtnq->execute();

                                        $mtn = $mtnq->fetchAll(PDO::FETCH_ASSOC);
                                        ?>
                                        <select name="MaterialType" class="form-control">
                                            <?php $lastOption = null; ?>
                                            <option></option>
                                            <?php foreach ($mtn as $row): ?>
                                                <?php
                                                if ($lastOption === $row["MaterialTypeName"]) {
                                                    continue;
                                                }

                                                $lastOption = $row["MaterialTypeName"];
                                                ?>
                                                <option><?= $row["MaterialTypeName"] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <select name="MaterialTypeName" class="form-control">
                                            <option data-type=""></option>
                                            <?php foreach ($mtn as $row): ?>
                                                <option data-type="<?= $row["MaterialTypeName"] ?>"
                                                        hidden><?= $row["MaterialName"] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Wymiary</td>
                                    <td><input type="text" class="form-control" name="Width" id="newSheetWidth"
                                               placeholder="Szerokość"/></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td><input type="text" class="form-control" name="Height" id="newSheetHeight"
                                               placeholder="Wysokość"/></td>
                                </tr>
                                <tr>
                                    <td>Grubość</td>
                                    <td><input type="text" class="form-control" name="Thickness"
                                               id="newSheetThickness"/></td>
                                </tr>
                                <tr>
                                    <td>Sztuk</td>
                                    <td><input type="text" class="form-control" name="QtyAvailable"/></td>
                                </tr>
                                <tr>
                                    <td>GrainDirection</td>
                                    <td><select name="GrainDirection" class="form-control">
                                            <option value="1">Horizontal</option>
                                            <option value="2">Vertical</option>
                                        </select></td>
                                </tr>
                                <tr>
                                    <td>SpecialInfo</td>
                                    <td><select name="SpecialInfo" class="form-control">
                                            <option value="0">Normal</option>
                                            <option value="1">Stal farbowana</option>
                                            <option value="2">Folia</option>
                                        </select></td>
                                </tr>
                                <tr>
                                    <td>SheetType</td>
                                    <td><select name="SheetType" class="form-control">
                                            <option>Standard</option>
                                        </select></td>
                                </tr>
                                <tr>
                                    <td>Priority</td>
                                    <td>
                                        <select name="Priority" class="form-control">
                                            <?php for ($i = 1; $i <= 9; $i++): ?>
                                                <option <?= ($i == 5 ? 'selected="selected"' : '') ?>><?= $i ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>SheetCode</td>
                                    <td>
                                        <div class="row">
                                            <div class="col-sm-12 col-lg-6">
                                                <input type="text" name="SheetCode" id="newSheetCode"
                                                       class="form-control" readonly/>
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
                                    <td>Waga paczki</td>
                                    <td><input type="text" name="Weight" class="form-control"/></td>
                                </tr>
                                <tr>
                                    <td>Cena</td>
                                    <td>
                                        <div class="input-group">
                                            <input type="text" name="Price" class="form-control"/>
                                            <div class="input-group-btn">
                                                <button type="button" class="btn btn-default" tabindex="-1" id="cpmt">
                                                    zł/kg
                                                </button>
                                                <button type="button" class="btn green dropdown-toggle"
                                                        data-toggle="dropdown" tabindex="-1"><i
                                                            class="fa fa-angle-down"></i></button>
                                                <ul class="dropdown-menu pull-right" role="menu" id="cpm">
                                                    <li><a href="javascript:;" id="1_cpm">zł/kg</a></li>
                                                    <li><a href="javascript:;" id="2_cpm">zł/szt netto</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Koszty</td>
                                    <td><input type="text" name="AdditionalPrice" class="form-control"></td>
                                </tr>
                                <tr>
                                    <td>Dzień przyjęcia</td>
                                    <td>
                                        <div class="input-group">
                                            <input class="form-control form-control-inline input-medium date-picker"
                                                   size="16" id="newSheetDate" type="text" name="pdate"
                                                   data-date-format="dd-mm-yyyy" value="">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Numer dokumentu przyjęcia</td>
                                    <td><input type="text" class="form-control" name="ndp"/></td>
                                </tr>
                                <tr>
                                    <td>Powierzony</td>
                                    <td>
                                        <select class="form-control" name="OwnerId">
                                            <option value="" selected="selected">Nie</option>
                                            <?php
                                            $clientsQuery = $db->query("SELECT id, name FROM clients ORDER BY name");
                                            $clientsData = $clientsQuery->fetchAll(PDO::FETCH_ASSOC);
                                            ?>
                                            <?php foreach ($clientsData as $client): ?>
                                                <option value="<?= $client["id"] ?>"><?= $client["id"] ?>
                                                    - <?= $client["name"] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
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

<script src="/js/plateWarehouse/plateWarehouse.js" type="text/javascript"></script>