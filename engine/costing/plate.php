<?php
$did = @$_GET["did"]; // Detail ID

$version = 1; // Wersja skryptu

if (@$_GET["a"] != null) {
    require_once dirname(__FILE__) . '/../../config.php';
    require_once dirname(__FILE__) . '/../protect.php';
    require_once dirname(__FILE__) . '/../class/material.php';
} else {
    $_COOKIE["did"] = $did;
    require_once dirname(__FILE__) . '/../class/material.php';
}

$material = new Material(); // Material init

//pobieranie nazwy firmy i projektu
$q_detail = $db->prepare("SELECT `pid`, `src` FROM `details` WHERE `id` = '$did' LIMIT 1");
$q_detail->execute();
$a_detail = $q_detail->fetch();

$dname = $a_detail["src"];
$pid = $a_detail["pid"];

$q_project = $db->prepare("SELECT `name`, `cid` FROM `projects` WHERE `id` = '$pid' LIMIT 1");
$q_project->execute();
$a_project = $q_project->fetch();

$cid = $a_project["cid"];
$pname = $a_project["name"];

$q_client = $db->prepare("SELECT `name` FROM `clients` WHERE `id` = '$cid' LIMIT 1");
$q_client->execute();
$a_client = $q_client->fetch();

$cname = $a_client["name"];


if (@$_GET["a"] == 1) { // Get material data by id
    $_id = $_GET["id"];
    $data = array("weight" => $material->weight[$_id], "price" => $material->price[$_id], "length" => $material->length[$_id], "cubic" => $material->cubic[$_id], "waste" => $material->waste[$_id]);
    die(json_encode($data)); //JSON ajax output
}
if (@$_GET["a"] == 2) {// Array input to json output
    foreach ($_POST as $key => $value) {
        if ($value == null) {
            $_POST[$key] = 0;
        }
    }
    $data = array("material" => $_POST["material"],
        "type" => $_POST["type"],
        "diemension" => $_POST["dimension"],
        "pieces" => $_POST["pieces"],
        "pod" => $_POST["pod"],
        "time" => $_POST["time"],
        "materialp" => $_POST["materialp"],
        "project" => $_POST["project"],
        "factor" => $_POST["factor"]);
    die(json_encode($data));
}

if (@$_GET["a"] == 3) { //Save
    require_once '../class/flieds2.php';

    //Checbox argument
    $atribute = array();
    if (is_array(@$_POST['atribute']) || is_object(@$_POST['atribute'])) {
        foreach ($_POST['atribute'] as $selected) {
            if ($selected == 7) {
                $atribute += array($selected => "1");
            } else {
                $atribute += array($selected => @$_POST["a" . $selected . "i1"]);
            }
        }
    }
    $atribute_e = json_encode($atribute);

    $query = $db->prepare("INSERT INTO `$cplt` (`version`, `did`, `type`, `material`, `thick`, `dimension`, `dimension2`, `handicap`, `qta`, `qtp`, `sheets`, `time`, `pierces`, `factor`, `mprice`, `ocprice`, `dprice`, `cprice`, `atribute`) "
        . "VALUES ('$version', '$did', '2', '$material', '$thick', '$dimension', '$dimension2', '$handicap', '$qta', '$qtp', '$sheets', '$time', '$pierces', '$factor', '$mp', '$cn', '$dcn', '$ccn', '$atribute_e')");
    $query->execute();

    $query = $db->prepare("UPDATE `details` SET `type` = '1' WHERE `id` = '$did'");
    $query->execute();

    $query = $db->prepare("SELECT `id` FROM `$cplt` WHERE `did` = '$did'");
    $query->execute();
    if ($query->rowCount() == 1) {
        $id = $row["id"];
        $query2 = $db->prepare("UPDATE `$cplt` SET `default` = '1' WHERE `id` = '$id'");
        $query2->execute();
    }

    insertStatus($did, $STATUS_PRICE);

    die($did);
}
if (@$_GET["a"] == 4) { //Get editing values
    $id = $_GET["id"];
    $query = $db->prepare("SELECT * FROM `$cplt` WHERE `id` = '$id'");
    $query->execute();
    $data = array();
    foreach ($query as $row) {
        $data = array("material" => $row["material"],
            "thick" => $row["thick"],
            "dimension" => $row["dimension"],
            "dimension2" => $row["dimension2"],
            "handicap" => $row["handicap"],
            "qta" => $row["qta"],
            "qtp" => $row["qtp"],
            "sheets" => $row["sheets"],
            "time" => $row["time"],
            "pierces" => $row["pierces"],
            "factor" => $row["factor"],
            "cn" => $row["ocprice"],
            "dcn" => $row["dprice"],
            "ccn" => $row["cprice"],
            "atribute" => $row["atribute"]);
    }
    die(json_encode($data));
}
if (@$_GET["a"] == 5) { //Update
    require_once '../class/flieds2.php';

    $id = $_GET["id"];

    //Checbox argument
    $atribute = array();
    if (is_array(@$_POST['atribute']) || is_object(@$_POST['atribute'])) {
        foreach ($_POST['atribute'] as $selected) {
            if ($selected == 7) {
                $atribute += array($selected => "1");
            } else {
                $atribute += array($selected => @$_POST["a" . $selected . "i1"]);
            }
        }
    }
    $atribute_e = json_encode($atribute);
    $query = $db->prepare("UPDATE `$cplt` SET `version` = '$version', `material` = '$material', `thick` = '$thick', `dimension` = '$dimension', `dimension2` = '$dimension2', `handicap` = '$handicap', `qta` = '$qta', `qtp` = '$qtp', `sheets` = '$sheets', `time` = '$time', `pierces` = '$pierces', `factor` = '$factor', `mprice` = '$mp', `ocprice` = '$cn', `dprice` = '$dcn', `cprice` = '$ccn', `atribute` = '$atribute_e' WHERE `id` = '$id'");
    $query->execute();
    die("1");
}
if (@$_GET["a"] == 6) { // Set default
    $id = $_GET["id"];
    $did = null;

    $query = $db->prepare("SELECT `did` FROM `$cplt` WHERE `id` = '$id'");
    $query->execute();

    foreach ($query as $row) {
        $did = $row["did"];
    }

    $query = $db->prepare("UPDATE `$cplt` SET `default` = '0' WHERE `did` = '$did'");
    $query->execute();

    $query = $db->prepare("UPDATE `$cplt` SET `default` = '1' WHERE `id` = '$id'");
    $query->execute();
    die();
}
?>

<div class="row">
    <div class="col-lg-12">
        <h2 class="page-title">Costing - BLACH
            <small><a href="/project/3/<?= $pid ?>/"><?= $cname ?> / <?= $pname ?></a> / <?= $dname ?></small>
        </h2>
    </div>
</div>
<div id="costingList">
    <div class="row">
        <div class="col-lg-12" style="text-align: right; margin-bottom: 10px;">
            <a href="#" id="bNew" data-toggle="modal" class="btn btn-success">Dodaj wycene</a>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="portlet box grey-silver">
                <div class="portlet-title">
                    <div style="float: left;"><h3><i class="fa fa-book"></i> Istniejące wyceny detalu: </h3></div>
                    <div class="status_bar">
                        <?= statusCosting($did) ?>
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <td>ID</td>
                            <td>Sztuk</td>
                            <td>Wymiary</td>
                            <td>Cena za detal</td>
                            <td>Domyślny</td>
                            <td></td>
                        </tr>
                        </thead>
                        <tbody style="text-align: center;">
                        <?php
                        $query = $db->prepare("SELECT `id`, `pieces`, `dimension`, `pricedetailu`, `default` FROM `$cpt` WHERE `did` = '$did'");
                        $query->execute();
                        ?>
                        <?php foreach ($query as $row): ?>
                            <?php
                            $def = "";
                            if ($row["default"] == 1) {
                                $def = '<i class="fa fa-plus"></i>';
                            }
                            ?>

                            <tr id="<?= $row["id"] ?>_did">
                                <td><?= $row["id"] ?></td>
                                <td><?= $row["pieces"] ?></td>
                                <td><?= $row["dimension"] ?></td>
                                <td><?= $row["pricedetailu"] ?></td>
                                <td style="text-align: center;"><?= $def ?></td>
                                <td style="text-align: right;">
                                    <div class="btn-group">
                                        <a class="btn btn-info dropdown-toggle" data-toggle="dropdown"
                                           href="javascript:;" aria-expanded="false">
                                            Opcje
                                            <span class="caret"></span>
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a href="#" class="bEdit">Edytuj</a>
                                            </li>
                                            <li>
                                                <a href="#" class="bDef">Domyślny</a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <?php
                            if ($query->rowCount() == 1) {
                                $id = $row["id"];
                                $query2 = $db->prepare("UPDATE `$cpt` SET `default` = '1' WHERE `id` = '$id'");
                                $query2->execute();
                            }
                            ?>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="portlet box blue-hoki">
                <div class="portlet-title">
                    <h3><i class="fa fa-cloud"></i> Auto wycena:</h3>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <td>Id</td>
                            <td>Sztuk</td>
                            <td>Wymiary</td>
                            <td>Cena sztuka</td>
                            <td>Cena komplet</td>
                            <td></td>
                        </tr>
                        </thead>
                        <tbody id="autoch">
                        <?php
                        $autoq = $db->query("
                          SELECT 
                          mpc.* 
                          FROM mpw
                          LEFT JOIN mpc ON mpc.wid = mpw.id
                          WHERE 
                          `did` = '$did' 
                          AND `src` != ''
                          ");
                        ?>
                        <?php foreach ($autoq as $mpc): ?>
                            <tr id="<?= $mpc["id"] ?>_mpc">
                                <td><?= $mpc["id"] ?></td>
                                <td><?= $mpc["d_qty"] ?></td>
                                <td><?= $mpc["wh"] ?></td>
                                <td><?= $mpc["d_last_price_n"] ?></td>
                                <td><?= $mpc["last_price_all_netto"] ?></td>
                                <td style="text-align: right;">
                                    <a class="btn btn-info"
                                       href="<?= $site_path ?>/view/601/<?= $mpc["id"] ?>/auto_costing">
                                        Pokaż
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="portlet box blue-dark">
                <div class="portlet-title">
                    <h3><i class="fa fa-cubes"></i> Multipart:</h3>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <td>Id</td>
                            <td>Materiał</td>
                            <td>Sztuk</td>
                            <td>Data utworzenia</td>
                            <td>Cena sztuka netto</td>
                            <td>Parametry</td>
                            <td>Status</td>
                            <td></td>
                        </tr>
                        </thead>
                        <tbody id="autoch">
                        <?php
                        $autoq = $db->query("
                            SELECT 
                            parts.ProgramId,
                            m.name as MaterialName,
                            d.id as dirId,
                            mpw.pieces,
                            programs.CreateDate,
                            mpw.atribute,
                            mpw.type,
                            settings.price
                            FROM plate_multiPartProgramsPart parts
                            LEFT JOIN plate_multiPartDetails partDetails ON parts.PartName = partDetails.name
                            LEFT JOIN mpw ON mpw.id = partDetails.mpw
                            LEFT JOIN material m ON mpw.material = m.id
                            LEFT JOIN plate_multiPartPrograms programs ON programs.id = parts.ProgramId
                            LEFT JOIN plate_multiPartDirectories d ON d.id = partDetails.dirId
                            LEFT JOIN plate_multiPartCostingDetailsSettings settings ON settings.directory_id = d.id AND settings.detaild_id = parts.DetailId
                            WHERE 
                            parts.DetailId = $did
                        ");
                        ?>
                        <?php foreach ($autoq as $row): ?>
                            <tr>
                                <td><?= $row["ProgramId"] ?></td>
                                <td><?= $row["MaterialName"] ?></td>
                                <td><?= $row["pieces"] ?></td>
                                <td><?= $row["CreateDate"] ?></td>
                                <td><?= $row["price"] ?>zł</td>
                                <td>
                                    <?php if (strlen($row["atribute"]) > 0): ?>
                                        <?php foreach (json_decode($row["atribute"]) as $param): ?>
                                            <?= _getChecboxText($param) ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $status = "";
                                    $text = "";
                                    switch ($row["type"]) {
                                        case OT::AUTO_WYCENA_BLACH_MULTI_KROK_1:
                                            $text = 'Brak wyceny';
                                            $status = 'default';
                                            break;

                                        case OT::AUTO_WYCENA_BLACH_MULTI_KROK_2:
                                            $text = 'Brak ramki';
                                            $status = 'warning';
                                            break;
                                    }

                                    if ($row["price"] > 0) {
                                        $text = "Wycenione";
                                        $status = "success";
                                    }
                                    ?>
                                    <span class="label label-<?= $status ?>"><?= $text ?></span>
                                </td>
                                <td>
                                    <a href="/plateMulti/<?= $row["dirId"] ?>/">
                                        <i class="fa fa-sign-in fa-2x"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="portlet box blue-chambray">
                <div class="portlet-title">
                    <h3><i class="fa fa-compass"></i> Single Time:</h3>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <td>Id</td>
                            <td>Tabela cięcia</td>
                            <td>Czas cięcia</td>
                            <td>Data</td>
                            <td></td>
                        </tr>
                        </thead>
                        <tbody id="autoch">
                        <?php
                        $autoq = $db->query("
                          SELECT
                          id,
                          LaserMatName,
                          PreTime,
                          upload_date
                          FROM plate_multiPartCostingDetails 
                          WHERE did = $did
                        ");
                        ?>
                        <?php foreach ($autoq as $row): ?>
                            <tr>
                                <td><?= $row["id"] ?></td>
                                <td><?= $row["LaserMatName"] ?></td>
                                <td><?= $row["PreTime"] ?></td>
                                <td><?= $row["upload_date"] ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row" style="margin: 0 10% 0 10%;">
        <div class="cold-md-12">
            <div class="portlet" style="margin: 0 auto; width: 80%;">
                <div class="portlet-title">
                    <h3><i class="fa fa-wechat"></i> Komentarze:</h3>
                </div>
                <div class="portlet-body">
                    <div>
                        <textarea id="comment" rows="3" class="form-control"></textarea>
                        <button type="button" class="btn blue btn-block" id="addcoment">Dodaj</button>
                    </div>
                    <div class="timeline">
                        <?php
                        $commentsq = $db->query("
                          SELECT 
                          c.*,
                          a.name as user_name
                          FROM `comments` c
                          LEFT JOIN accounts a ON a.id = c.uid
                          WHERE c.`type` = '1' 
                          AND c.`eid` = '$did' 
                          ORDER BY c.`id` DESC
                        ");
                        ?>
                        <?php foreach ($commentsq as $row): ?>
                            <div class="timeline-item">
                                <div class="timeline-badge">
                                    <div class="timeline-icon">
                                        <i class="icon-users font-green-haze"></i>
                                    </div>
                                </div>
                                <div class="timeline-body">
                                    <div class="timeline-body-arrow"></div>
                                    <div class="timeline-body-head">
                                        <div class="timeline-body-head-caption">
                                            <span class="timeline-body-alerttitle font-blue-madison"><?= $row["user_name"] ?></span>
                                            <span class="timeline-body-time font-grey-cascade"><?= $row["date"] ?></span>
                                        </div>
                                    </div>
                                    <div class="timeline-body-content">
                                <span class="font-grey-cascade">
                                    <?= $row["content"] ?>
                                </span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<form id="cForm">
    <div class="costingForm" style="display: none">
        <div class="row">
            <div class="col-lg-6">
                <div class="widget">
                    <div class="widget-header">
                        <i class="icon-bookmark"></i>
                        <h3>Part Sheet Unfold [PSU]</h3>
                    </div>
                    <div class="widget-content">
                        <table class="table">
                            <tbody>
                            <tr>
                                <td style="text-align: right;">Materiał</td>
                                <td>
                                    <select class="form-control required" name="material" id="materialSelect">
                                        <?php for ($i = 1; $i <= count($material->name); $i++): ?>
                                            <option value="<?= $i ?>"><?= $material->name[$i] ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: right;">Grubość</td>
                                <td><input type="text" name="thick" id="thick" style="width: 100%;"/></td>
                            </tr>
                            <tr>
                                <td style="text-align: right;">Wymiary</td>
                                <td>
                                    <div style="float: left"><input type="text" name="dimension11" id="dimension11"
                                                                    style="width: 100%;" class="form-control"/></div>
                                    <div style="float: left"><input type="text" name="dimension12" id="dimension12"
                                                                    style="width: 100%;" class="form-control"/></div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="widget">
                    <div class="widget-header"></div>
                    <div class="widget-content">
                        <table class="table table-striped">
                            <tbody>
                            <tr>
                                <td style="text-align: right;">Wymiar</td>
                                <td><input type="text" name="resoult1" id="resoult1" class="form-control" readonly/>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: right;">Waga</td>
                                <td><input type="text" name="weight1" id="weight1" class="form-control" readonly/></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="widget">
                    <div id="accordion1" class="panel-group">
                        <div class="panel">
                            <div class="panel-heading">
                                <a href="#collapseOneTwo" data-toggle="collapse" class="accordion-toggle collapsed">Materiał</a>
                            </div>
                            <div class="panel-collapse collapse" id="collapseOneTwo" style="height: 0px;">
                                <div class="panel-body">
                                    <table class="table">
                                        <tbody>
                                        <tr>
                                            <td>[g/mm^3]</td>
                                            <td><input type="text" name="cubic" id="cubic" class="form-control"/></td>
                                        </tr>
                                        <tr>
                                            <td>Price</td>
                                            <td><input type="text" name="cmp" id="cmp" class="form-control"/></td>
                                        </tr>
                                        <tr>
                                            <td>Remnant</td>
                                            <td><input type="text" name="remnant" id="remnant" class="form-control"/>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="widget">
                    <div class="widget-header">
                        <i class="icon-bookmark"></i>
                        <h3>Part Unfold [PU]</h3>
                    </div>
                    <div class="widget-content">
                        <table class="table">
                            <tbody>
                            <tr>
                                <td style="text-align: right;">Wymiary</td>
                                <td><input type="text" name="dimension2" id="dimension2" style="width: 100%;"
                                           class="form-control"/></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="widget">
                    <div class="widget-header"></div>
                    <div class="widget-content">
                        <table class="table table-striped">
                            <tbody>
                            <tr>
                                <td style="text-align: right;">Waga</td>
                                <td><input type="text" name="weight2" id="weight2" class="form-control" readonly/></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="widget">
                    <div class="widget-header">
                        <i class="icon-bookmark"></i>
                        <h3>Material Cost per Detail</h3>
                    </div>
                    <div class="widget-content">
                        <table class="table">
                            <tbody>
                            <tr>
                                <td style="text-align: right;">Handicap</td>
                                <td><input type="text" name="handicap" id="handicap" style="width: 100%;" value="100"
                                           class="form-control mask" data-inputmask="'mask':'999%', 'greedy': 'false'"/>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="widget">
                    <div class="widget-content">
                        <table class="table">
                            <tbody>
                            <tr>
                                <td style="text-align: right;">qt all</td>
                                <td><input type="number" name="qta" id="qta" style="width: 100%;"/></td>
                            </tr>
                            <tr>
                                <td style="text-align: right;">qt program</td>
                                <td><input type="number" name="qtp" id="qtp" style="width: 100%;"/></td>
                            </tr>
                            <tr>
                                <td style="text-align: right;">Sheets pc</td>
                                <td><input type="number" name="sheets" id="sheets" style="width: 100%;" value="1"/></td>
                            </tr>
                            <tr>
                                <td style="text-align: right;">ABE Time</td>
                                <td><input type="text" name="time" id="time" style="width: 100%;"
                                           class="form-control mask" data-inputmask=" 'mask': '99:99:99'"/></td>
                            </tr>
                            <tr>
                                <td style="text-align: right;">Pierces</td>
                                <td><input type="text" name="pierces" id="pierces" style="width: 100%;" value="0"/></td>
                            </tr>
                            <tr>
                                <td style="text-align: right;">Factor</td>
                                <td><input type="text" name="factor" id="factor" style="width: 100%;" value="1.00"/>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="widget">
                    <div class="widget-header">
                    </div>
                    <div class="widget-content">
                        <table class="table table-striped">
                            <tbody>
                            <tr>
                                <td style="text-align: right;">Material [PSU]</td>
                                <td><input type="text" name="mpsu" id="mpsu" class="form-control" readonly/></td>
                            </tr>
                            <tr>
                                <td style="text-align: right;">Material [PU]</td>
                                <td><input type="text" name="mpu" id="mpu" class="form-control" readonly/></td>
                            </tr>
                            <tr>
                                <td style="text-align: right;">Remnant value</td>
                                <td><input type="text" name="rv" id="rv" class="form-control" readonly/></td>
                            </tr>
                            <tr>
                                <td style="text-align: right;">Material price</td>
                                <td><input type="text" name="mp" id="mp" class="form-control" readonly/></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="widget">
                    <div class="widget-content">
                        <table class="table">
                            <thead>
                            <tr>
                                <td></td>
                                <td>Nazwa</td>
                                <td>zł/szt</td>
                                <td>zł/komplet</td>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td><input type="checkbox" name="atribute[]" value="1" class="form-control"
                                           style="width: 20px; height: 20px;"/></td>
                                <td>Gięcie</td>
                                <td><input type="text" name="a1i1" id="a1i1" class="form-control ai"/></td>
                                <td><input type="text" name="a1i2" id="a1i2" class="form-control aik"/></td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" name="atribute[]" value="2" class="form-control"
                                           style="width: 20px; height: 20px;"/></td>
                                <td>Projekt</td>
                                <td><input type="text" name="a2i1" id="a2i1" class="form-control ai"/></td>
                                <td><input type="text" name="a2i2" id="a2i2" class="form-control aik"/></td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" name="atribute[]" value="3" class="form-control"
                                           style="width: 20px; height: 20px;"/></td>
                                <td>Spawanie</td>
                                <td><input type="text" name="a3i1" id="a3i1" class="form-control ai"/></td>
                                <td><input type="text" name="a3i2" id="a3i2" class="form-control aik"/></td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" name="atribute[]" value="4" class="form-control"
                                           style="width: 20px; height: 20px;"/></td>
                                <td>Malowanie</td>
                                <td><input type="text" name="a4i1" id="a4i1" class="form-control ai"/></td>
                                <td><input type="text" name="a4i2" id="a4i2" class="form-control aik"/></td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" name="atribute[]" value="5" class="form-control"
                                           style="width: 20px; height: 20px;"/></td>
                                <td>Ocynkowanie</td>
                                <td><input type="text" name="a5i1" id="a5i1" class="form-control ai"/></td>
                                <td><input type="text" name="a5i2" id="a5i2" class="form-control aik"/></td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" name="atribute[]" value="6" class="form-control"
                                           style="width: 20px; height: 20px;"/></td>
                                <td>Gwintowanie</td>
                                <td><input type="text" name="a6i1" id="a6i1" class="form-control ai"/></td>
                                <td><input type="text" name="a6i2" id="a6i2" class="form-control aik"/></td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" name="atribute[]" value="7" class="form-control"
                                           style="width: 20px; height: 20px;"/></td>
                                <td>Common Cut</td>
                                <td></td>
                                <td></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="widget">
                    <div class="widget-content">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <td>Name</td>
                                <td>Netto</td>
                                <td>Brutto</td>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td style="text-align: right;">Only CUT</td>
                                <td><input type="text" name="cn" id="cn" class="form-control" readonly/></td>
                                <td><input type="text" name="cb" id="cb" class="form-control" readonly/></td>
                            </tr>
                            <tr>
                                <td style="text-align: right;">Detail Cost</td>
                                <td><input type="text" name="dcn" id="dcn" class="form-control"/></td>
                                <td><input type="text" name="dcb" id="dcb" class="form-control"/></td>
                            </tr>
                            <tr>
                                <td style="text-align: right;">Complet Cost</td>
                                <td><input type="text" name="ccn" id="ccn" class="form-control"/></td>
                                <td><input type="text" name="ccb" id="ccb" class="form-control"/></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3">
                <div class="widget">
                    <div class="btn-group">
                        <button class="btn btn-success" type="submit">Zapisz</button>
                        <button class="btn btn-default" type="button" id="cancel">Anuluj</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script type="text/javascript">
    var edit = false;
    var editi = 0;
    var PROD_FACTOR = 5.4;
    var SHEET_CHANGE = 15;
    var cbv = [-1, -1, -1, -1, -1, -1, -1, -1]; //Check box values :)

    function Round(n, k) // Round function
    {
        var factor = Math.pow(10, k + 1);
        n = Math.round(Math.round(n * factor) / 10);
        return n / (factor / 10);
    }

    function countCCN(dcn) { //Calculate complite cost
        $("#ccn").val(Round(parseFloat(dcn) * parseInt($("#qta").val()), 2) + " zł");
        $("#ccb").val(Round(parseFloat($("#ccn").val()) * 1.23, 2));
    }

    function countDCN(ccn) { //Calculate detail cost
        $("#dcn").val(Round(parseFloat(ccn) / parseInt($("#qta").val()), 2) + " zł");
        $("#dcb").val(Round(parseFloat($("#dcn").val()) * 1.23, 2) + " zł");
    }

    function countData() { // Główna funckja liczaca!
        var iTime = $("#time").val().split(":");
        var _time = (parseInt(iTime[0]) * 3600) + (parseInt(iTime[1]) * 60) + (parseInt(iTime[2]) * 1); //s

        //Part Sheet Unfold
        var dimension1 = parseFloat($("#dimension11").val()) * parseFloat($("#dimension12").val());

        $("#resoult1").val(dimension1 + " mm2");
        $("#weight1").val(Round(dimension1 * parseFloat($("#thick").val()) * parseFloat($("#cubic").val()), 2) + " g/detal");

        //Part Unfold 
        var dimension2 = parseFloat($("#dimension2").val());

        $("#weight2").val(Round(dimension2 * parseFloat($("#thick").val()) * parseFloat($("#cubic").val()), 2) + " g/detal");

        //Material Cost per Detail
        $("#mpsu").val(Round((parseFloat($("#weight1").val()) / 1000) * parseFloat($("#cmp").val()), 2) + " zł");
        $("#mpu").val(Round((parseFloat($("#weight2").val()) / 1000) * parseFloat($("#cmp").val()), 2) + " zł");
        $("#rv").val(Round((parseFloat($("#weight1").val()) - parseFloat($("#weight2").val())) / 1000 * parseFloat($("#remnant").val()), 2) + " zł");
        $("#mp").val(Round(parseFloat($("#mpsu").val()) - (parseFloat($("#rv").val()) * parseFloat($("#handicap").val()) / 100), 2) + " zł");

        //No group name
        if (parseInt($("#qta").val()) < parseInt($("#qtp").val())) {
            $("#qtp").val($("#qta").val());
            alert("qt program nie moze byc wieksze niz gt all");
        }

        $("#cn").val(Round((_time / 60 * PROD_FACTOR) / parseInt($("#qtp").val()) + (parseInt($("#sheets").val()) * SHEET_CHANGE / parseInt($("#qta").val())), 2) + " zł");
        $("#cb").val(Round(parseFloat($("#cn").val()) * 1.23, 2) + " zł");
        var _dcn = parseFloat($("#cn").val()) * parseFloat($("#factor").val()) + parseFloat($("#mp").val());

        //detail price = checkbox price + detail price
        var cprice = 0;
        for (i = 0; i <= 7; i++) {
            if (cbv[i] > -1) {
                cprice += parseFloat(cbv[i]);
            }
        }
        _dcn += cprice;

        $("#dcn").val(Round(_dcn, 2) + " zł");
        $("#dcb").val(Round(parseFloat($("#dcn").val()) * 1.23, 2));
        $("#ccn").val(Round(parseFloat($("#dcn").val()) * parseInt($("#qta").val()), 2) + " zł");
        $("#ccb").val(Round(parseFloat($("#ccn").val()) * 1.23, 2) + " zł");
    }

    function countMaterial() { // Kalkulator materialu 
        $("#mzp").val(parseFloat($("#mWeight").val()) * parseFloat($("#mPrice").val()));
        $("#mzm").val(Math.ceil((parseFloat($("#mWeight").val()) * parseFloat($("#mPrice").val())) / (parseFloat($("#mLength").val()) / 1000)));
    }

    $(document).ready(function () {
        //STATUS CHANGE 
        $.getScript("/js/status.js");

        //Comment add
        $("#addcoment").on("click", function () {
            var comment = $("#comment").val();
            if (comment != "" || comment != null) {
                $.ajax({
                    url: '<?php echo $site_path; ?>/engine/addcomment.php?type=1&eid=<?php echo $did; ?>&content=' + comment
                }).done(function () {
                    location.reload();
                });
            }
        });

        $(".bEdit").on("click", function () { // Edit buttion
            edit = true;
            editi = parseInt($(this).parent().parent().parent().parent().parent().attr("id"));
            $("#costingList").fadeOut("fast");
            $.ajax({
                url: '<?php echo $site_path; ?>/engine/costing/plate.php?a=4&id=' + editi
            }).done(function (_data) {
                $(".costingForm").fadeIn();
                var data = jQuery.parseJSON(_data);
                var atribute = jQuery.parseJSON(data.atribute);

                //SELECT ITEM
                $('#materialSelect option').removeAttr('selected').filter('[value=' + data.material + ']').attr('selected', true);
                getMaterial(data.material);

                //INPUT
                $("#thick").val(data.thick);

                var dimension = data.dimension.split("x");
                $("#dimension11").val(dimension[0]);
                $("#dimension12").val(dimension[1]);

                $("#dimension2").val(data.dimension2);
                $("#handicap").val(data.handicap);
                $("#qta").val(data.qta);
                $("#qtp").val(data.qtp);
                $("#sheets").val(data.sheets);
                $("#time").val(data.time);
                $("#pierces").val(data.pierces);
                $("#factor").val(data.factor);

                countData();
                $("#cn").val(data.cn + " zł");
                $("#cb").val(Round(parseFloat(data.cn) * 1.23, 2) + " zł");

                $("#dcn").val(data.dcn + " zł");
                $("#dcb").val(Round(parseFloat(data.dcn) * 1.23, 2) + " zł");

                $("#ccn").val(data.ccn + " zł");
                $("#ccb").val(Round(parseFloat(data.ccn) * 1.23, 2) + " zł");

                //Chebckbox atribute
                for (i = 1; i < 8; i++) {
                    if (atribute[i] > 0) {
                        $("input:checkbox[value='" + i + "']").prop('checked', true);
                        $("#a" + i + "i1").val(parseFloat(atribute[i]));
                        $("#a" + i + "i2").val(Round(parseFloat(atribute[i]) * parseInt($("#qta").val()), 2));
                        cbv[i] = parseFloat(atribute[i]);
                    }
                }
            });
        });
        $("#cancel").on("click", function () { //Cancel button
            $(".costingForm").css("opacity", "0.5");
            if (confirm("Na pewno chcesz wyjść?")) {
                edit = false;
                $(".costingForm").fadeOut("fast", function () {
                    location.reload();
                });
            } else {
                $(".costingForm").css("opacity", "1");
            }
        });

        $(".bDef").on("click", function () { // Def buttion
            var _id = parseInt($(this).parent().parent().parent().parent().parent().attr("id"));
            $.ajax({
                url: '<?php echo $site_path; ?>/engine/costing/plate.php?a=6&id=' + _id
            }).done(function () {
                location.reload();
            });
        });

        $('.mask').inputmask();
        $("#bNew").on("click", function () {
            $("#costingList").fadeOut("fast", function () {
                $(".costingForm").fadeIn();
            });
        });

        //Form VALIATION
        function getMaterial(_id) {
            $.ajax({
                url: '<?php echo $site_path; ?>/engine/costing/plate.php?a=1&id=' + _id
            }).done(function (msg) {
                var response = jQuery.parseJSON(msg);
                $("#cubic").val(response.cubic);
                $("#cmp").val(response.price);
                $("#remnant").val(response.waste);
                countData();
            });
        }

        getMaterial(1); // Getin def value of material
        $("#materialSelect").change(function () {
            var _id = $(this).val();
            getMaterial(_id);
        });

        $('#mType').on('change', function () {
            var value = parseInt($(this).find(":selected").val());
            if (value == 1) {
                $("#diemension3").prop("type", "hidden");
                $("#diemension3").val("0");
            } else {
                $("#diemension3").prop("type", "text");
            }
        });

        $("input").change(function () {
            if ($(this).hasClass("ai")) { // Checkbox atribute input one piece
                var _id = parseInt($(this).attr("id").match(/\d/g));
                $("#a" + _id + "i2").val(Round(parseFloat($(this).val()) * parseInt($("#qta").val()), 2));
                if ($("input:checkbox[value='" + _id + "']").is(':checked')) {
                    cbv[_id] = parseFloat($(this).val());
                }
            }
            if ($(this).hasClass("aik")) { // Checkbox atribute input all piece
                var _id = parseInt($(this).attr("id").match(/\d/g));
                $("#a" + _id + "i1").val(Round(parseFloat($(this).val()) / parseInt($("#qta").val()), 2));
                if ($("input:checkbox[value='" + _id + "']").is(':checked')) {
                    cbv[_id] = parseFloat($("#a" + _id + "i1").val());
                }
            }
            if ($(this).attr("id") == "qta") { // Refresh
                $(".aik").each(function () {
                    var _id = parseInt($(this).attr("id").match(/\d/g));
                    if ($(this).val() !== null && $(this).val() !== "") {
                        $(this).val(Round(parseFloat($("#a" + _id + "i1").val()) * parseInt($("#qta").val()), 2));
                    }
                });
            }
            if ($(this).is(":checkbox")) {
                var _id = $(this).val();
                if ($(this).is(':checked')) {
                    cbv[_id] = parseFloat($("#a" + _id + "i1").val());
                } else {
                    cbv[_id] = -1;
                }
            }

            //Other inputs
            if ($(this).attr("id") == "dcn") {
                countCCN($(this).val());
                $("#dcb").val(Round(parseFloat($("#dcn").val()) * 1.23, 2));
            } else if ($(this).attr("id") == "dcb") {
                $("#dcn").val(Round(parseFloat($("#dcb").val()) / 1.23, 2));
                countCCN($("#dcn").val());
            } else if ($(this).attr("id") == "ccn") {
                countDCN($(this).val());
                $("#ccb").val(Round(parseFloat($("#ccn").val()) * 1.23, 2));
            } else if ($(this).attr("id") == "ccb") {
                $("#ccn").val(Round(parseFloat($("#ccb").val()) / 1.23, 2));
                countDCN($("#ccn").val());
            } else {
                countData();
                countMaterial();
            }
        });
        $("#cForm").submit(function (event) {
            event.preventDefault();

            var complite = true;
            $("#cForm").find("input").each(function () {
                if (!$(this).hasClass("ai") && !$(this).hasClass("aik")) {
                    if ($(this).val() == "") {
                        complite = false;
                    } else if ($(this).val() == null) {
                        complite = false;
                    }
                }
            });
            if (complite) {
                var action = 3;
                if (edit) {
                    action = 5 + "&id=" + editi;
                }
                $.ajax({
                    url: '<?php echo $site_path; ?>/engine/costing/plate.php?a=' + action + '&did=<?php echo $did; ?>',
                    method: 'POST',
                    data: $("#cForm").serialize()
                }).done(function () {
                    location.reload();
                });
            } else {
                alert("Uzupełnij wszystkie pola!");
            }
        });
    });
</script>