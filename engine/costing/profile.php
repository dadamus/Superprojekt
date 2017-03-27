<?php
$did = @$_GET["did"]; // Detail ID

$version = 1; // Wersja skryptu
$edit = false;

function manualInsertMpw($id, $did) {
    global $db;

    $date = date("Y-m-d H:i:s");
    $qpc = $db->query("SELECT `material`, `version`, `pieces`, `atribute` FROM `profile_costing` WHERE `id` = '$id'");
    $pc = $qpc->fetch();
    $qmmpw = $db->query("SELECT `id`, `code` FROM `mpw` WHERE `did` = '$did' AND `type` = '3'");
    $mpwud = false;

    $atributes = json_decode($pc["atribute"], true);
    $atr_t_j = array();
    foreach ($atributes as $name => $val) {
        array_push($atr_t_j, $name);
    }
    $jatr = json_encode($atr_t_j);

    foreach ($qmmpw as $mpw) {
        if ($mpw["code"] == "manual") {

            $db->query("UPDATE `mpw` SET `mcp` = '$id', `date` = '$date', `material` = '" . $pc["material"] . "', `version` = '" . $pc["version"] . "', `pieces` = '" . $pc["pieces"] . "', `atribute` = '$jatr' WHERE `id` = '" . $mpw["id"] . "'");
            $mpwud = true;
            break;
        } else {
            continue;
        }
    }
    if ($mpwud == false) {
        $qpid = $db->query("SELECT `pid` FROM `details` WHERE `id` = '$did'");
        $fpid = $qpid->fetch();
        $pid = $fpid["pid"];
        $db->query("INSERT INTO `mpw` (`mcp`, `pid`, `did`, `material`, `version`, `pieces`, `atribute`, `code`, `type`, `date`) VALUES ('$id', '$pid', '$did', '" . $pc["material"] . "', '" . $pc["version"] . "', '" . $pc["pieces"] . "', '$jatr', 'manual', '3', '$date')");
    }
}

if (@$_GET["a"] != null) {
    require_once dirname(__FILE__) . '/../../config.php';
    require_once dirname(__FILE__) . '/../protect.php';
    require_once dirname(__FILE__) . '/../class/material.php';
} else {
    $_COOKIE["did"] = $did;
    require_once dirname(__FILE__) . '/../class/material.php';
}

$material = new Material(); // Material init

if (@$_GET["a"] == 1) { // Get material data by id
    $_id = $_GET["id"];
    $data = array("weight" => $material->weight[$_id], "price" => $material->price[$_id], "length" => $material->length[$_id]);
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
    require_once '../class/fields.php';

    //Checbox argument
    $atribute = array();
    $watribute = array();
    if (is_array(@$_POST['atribute']) || is_object(@$_POST['atribute'])) {
        foreach ($_POST['atribute'] as $selected) {
            if ($selected == 7) {
                $atribute += array($selected => "1");
            } else {
                $atribute += array($selected => @$_POST["a" . $selected . "i1"]);
            }
            array_push($watribute, $selected);
        }
    }
    if (@$_POST["dversion"] == null) {
        die("Wersja nie może być pusta");
    }

    $atribute_e = json_encode($atribute);
    $query = $db->prepare("INSERT INTO `$cpt` (`version`, `did`, `material`, `pieces`, `dimension`, `type`, `pod`, `time`, `materialp`, `project`, `factor`, `pricedetail`, `pricedetailu`, `pricen`, `materialq`, `priceset`, `profilea`, `profilep`, `allotime`, `atribute`) "
            . "VALUES ('$version', '$did', '$material', '$pieces', '$dimension', '$type', '$pod', '$time', '$materialp', '$project', '$factor', '$pricedetail', '$pricedetailu', '$pricen', '$materialq', '$priceset', '$profilea', '$profilep', '$allotime', '$atribute_e')");
    $query->execute();
    $idc = $db->lastInsertId();

    $query = $db->prepare("UPDATE `details` SET `type` = '2' WHERE `id` = '$did'");
    $query->execute();

    $query = $db->prepare("SELECT `id` FROM `$cpt` WHERE `did` = '$did'");
    $query->execute();
    if ($query->rowCount() == 1) {
        $id = $row["id"];
        $query2 = $db->prepare("UPDATE `$cpt` SET `default` = '1' WHERE `id` = '$id'");
        $query2->execute();

        manualInsertMpw($id, $did);
    }

    insertStatus($did, $STATUS_PRICE);

    die($did);
}
if (@$_GET["a"] == 4) { //Get editing values
    $edit = true;
    $id = $_GET["id"];
    $query = $db->prepare("SELECT * FROM `$cpt` WHERE `id` = '$id'");
    $query->execute();
    $data = array();
    foreach ($query as $row) {
        $data = array("material" => $row["material"],
            "type" => $row["type"],
            "dimension" => $row["dimension"],
            "pieces" => $row["pieces"],
            "pod" => $row["pod"],
            "time" => $row["time"],
            "materialp" => $row["materialp"],
            "project" => $row["project"],
            "factor" => $row["factor"],
            "pricedetailu" => $row["pricedetailu"],
            "atribute" => $row["atribute"]);
    }
    die(json_encode($data));
}
if (@$_GET["a"] == 5) { //Update
    require_once '../class/fields.php';

    $id = $_GET["id"];

    //Checbox argument
    $atribute = array();
    $watribute = array();
    if (is_array(@$_POST['atribute']) || is_object(@$_POST['atribute'])) {
        foreach ($_POST['atribute'] as $selected) {
            if ($selected == 7) {
                $atribute += array($selected => "1");
            } else {
                $atribute += array($selected => @$_POST["a" . $selected . "i1"]);
            }
            array_push($watribute, $selected);
        }
    }
    $atribute_e = json_encode($atribute);

    $query = $db->prepare("UPDATE `$cpt` SET `version` = '$version', `atribute` = '$atribute_e', `material` = '$material', `pieces` = '$pieces', `dimension` = '$dimension', `type` = '$type', `pod` = '$pod', `time` = '$time', `materialp` = '$materialp', `project` = '$project', `factor` = '$factor', `pricedetail` = '$pricedetail' , `pricedetailu` = '$pricedetailu', `pricen` = '$pricen', `materialq` = '$materialq', `priceset` ='$priceset', `profilea` = '$profilea', `profilep` = '$profilep', `allotime` = '$allotime' WHERE `id` = '$id'");
    $query->execute();

    $qdid = $db->query("SELECT `did` FROM `profile_costing` WHERE `id` = '$id'");
    $fdid = $qdid->fetch();
    $did = $fdid["did"];

    manualInsertMpw($id, $did);
    die("1");
}
if (@$_GET["a"] == 6) { // Set default
    $id = $_GET["id"];
    $did = null;

    $query = $db->prepare("SELECT `did` FROM `$cpt` WHERE `id` = '$id'");
    $query->execute();

    foreach ($query as $row) {
        $did = $row["did"];
    }

    manualInsertMpw($id, $did);

    $query = $db->prepare("UPDATE `$cpt` SET `default` = '0' WHERE `did` = '$did'");
    $query->execute();

    $query = $db->prepare("UPDATE `$cpt` SET `default` = '1' WHERE `id` = '$id'");
    $query->execute();
    die();
}
?>
<div class="row">
    <div class="col-lg-12">
        <h2 class="page-title">Costing - PROFILU <small><?php echo '<a href="' . $site_path . '/index.php?site=4&plist=' . $_COOKIE["plProjectId"] . '">' . $_COOKIE["cname"] . " / " . $_COOKIE["pname"] . "</a> / " . $_COOKIE["dname"]; ?></small></h2>
    </div>
</div>
<div id ="costingList">
    <div class="row">
        <div class="col-lg-9"></div>
        <div class="col-lg-3">
            <div class="widget">
                <div class="widget-content" style="text-align: center;">
                    <a href="#" id="bNew" data-toggle="modal" class="btn btn-success">Dodaj wycene</a>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="widget">
                <div class="widget-header"> 
                    <div style="float: left;"><h3><i class="fa fa-book"></i> Istniejące wyceny detalu: </h3></div>
                    <div class="status_bar">
                        <?php
                        echo statusCosting($did);
                        ?>
                    </div>
                </div>
                <div class="widget-content">
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

                            foreach ($query as $row) {
                                $def = "";
                                if ($row["default"] == 1) {
                                    $def = '<i class="fa fa-plus"></i>';
                                }

                                echo '<tr id="' . $row["id"] . '_did"><td>' . $row["id"] . '</td><td>' . $row["pieces"] . '</td><td>' . $row["dimension"] . '</td><td>' . $row["pricedetailu"] . '</td><td style="text-align: center;">' . $def . '</td><td style="text-align: right;"><div class="btn-group"><a class="btn btn-info dropdown-toggle" data-toggle="dropdown" href="javascript:;" aria-expanded="false">Opcje<span class="caret"></span></a><ul class="dropdown-menu"><li><a href="#" class="bEdit">Edytuj</a></li><li><a href="#" class="bDef">Domyślny</a></li></ul></div></td></tr>';
                                if ($query->rowCount() == 1) {
                                    $id = $row["id"];
                                    $query2 = $db->prepare("UPDATE `$cpt` SET `default` = '1' WHERE `id` = '$id'");
                                    $query2->execute();
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="widget">
                <div class="widget-header">
                    <h3><i class="fa fa-cloud"></i> Auto wycena:</h3>
                </div>
                <div class="widget-content">
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
                        <tbody id="autoch" style="text-align: center;">
                            <?php
                            $autoq = $db->query("SELECT `id` FROM `mpw` WHERE `did` = '$did' AND `src` != ''");
                            foreach ($autoq as $row) {
                                //SELECT MP
                                $wid = $row["id"];
                                $mpcq = $db->query("SELECT * FROM `mpc` WHERE `wid` = '$wid'");
                                $mpc = $mpcq->fetch();

                                echo '<tr id="' . $mpc["id"] . '_mpc">';
                                echo '<td>' . $mpc["id"] . '</td>';
                                echo '<td>' . $mpc["d_qty"] . '</td>';
                                echo '<td>' . $mpc["wh"] . '</td>';
                                echo '<td>' . $mpc["d_last_price_n"] . '</td>';
                                echo '<td>' . $mpc["last_price_all_netto"] . '</td>';
                                echo '<td style="text-align: right;"><a class="btn btn-info" href="' . $site_path . '/view/601/' . $mpc["id"] . '/auto_costing">Pokaż</a></td>';
                                echo '</tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row" style="margin: 0 10% 0 10%;">
        <div class="cold-md-12">
            <div class="widget" style="margin: 0 auto; width: 80%;">
                <div class="widget-header">
                    <h3><i class="fa fa-wechat"></i> Komentarze:</h3>
                </div>
                <div class="widget-content">
                    <div>
                        <textarea id="comment" rows="3" class="form-control" id="comment"></textarea>
                        <button type="button" class="btn blue btn-block" id="addcoment">Dodaj</button>
                    </div>
                    <div class="timeline">
                        <?php
                        $commentsq = $db->query("SELECT * FROM `comments` WHERE `type` = '1' AND `eid` = '$did' ORDER BY `id` DESC");
                        foreach ($commentsq as $row) {
                            $uid = $row["uid"];
                            $uq = $db->query("SELECT `name` FROM `accounts` WHERE `id` = '$uid'");
                            $uf = $uq->fetch();
                            $user = $uf["name"];

                            echo '<div class="timeline-item">
                        <div class="timeline-badge">
                            <div class="timeline-icon">
                                <i class="icon-users font-green-haze"></i>
                            </div>
                        </div>
                        <div class="timeline-body">
                            <div class="timeline-body-arrow"></div>
                            <div class="timeline-body-head">
                                <div class="timeline-body-head-caption">
                                    <span class="timeline-body-alerttitle font-blue-madison">' . $user . '</span>
                                    <span class="timeline-body-time font-grey-cascade">' . $row["date"] . '</span>
                                </div>
                            </div>
                            <div class="timeline-body-content">
                                <span class="font-grey-cascade">
                                    ' . $row["content"] . '
                                </span>
                            </div>
                        </div>
                    </div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<form id="cForm">
    <div class="row costingForm" style="display: none">
        <div class="col-md-2">
            <div class="stats-heading">Materiał</div>
            <div class="stats-body-alt"> 
                <div class="text-center">
                    <select class="form-control required" name="material" id="materialSelect">
                        <?php
                        for ($i = 1; $i <= count($material->name); $i++) {
                            echo '<option value="' . $i . '">' . $material->name[$i] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="stats-footer"></div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stats-heading">Typ</div>
            <div class="stats-body-alt"> 
                <div class="text-center">
                    <select name="type" id="mType" class="form-control">
                        <option value="0">Profil</option>
                        <option value="1">Rura</option>
                        <option value="2">Ceownik</option>
                        <option value="3">Kątownik</option>
                    </select>
                </div>
                <div class="stats-footer"></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-heading">Wymiar</div>
            <div class="stats-body-alt"> 
                <div class="text-center">
                    <input type="text" name="dimension1" id="diemension1" class="form-control" style="text-align: center; width: 33%; float: left;"/>
                    <input type="text" name="dimension2" id="diemension2" class="form-control" style="text-align: center; width: 33%; float: left;"/>
                    <input type="text" name="dimension3" id="diemension3" class="form-control" style="text-align: center; width: 33%; float: left;"/>
                    <div style="clear: both"></div>
                </div>
                <div class="stats-footer"></div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stats-heading">Liczba sztuk</div>
            <div class="stats-body-alt"> 
                <div class="text-center">
                    <input type="number" name="pieces" id="pieces" class="form-control"/>
                </div>
                <div class="stats-footer"></div>
            </div>
        </div>
        <div class="col-md-2" id="versiondiv">
            <div class="stats-heading">Wersja detalu</div>
            <div class="stats-body-alt">
                <div class="text-center">
                    <select name="dversion" id="dversion" class="form-control">      
                        <?php
                        $pidq = $db->query("SELECT `pid` FROM `details` WHERE `id` = '$did'");
                        $pidf = $pidq->fetch();
                        $pid = $pidf["pid"];

                        $srcq = $db->query("SELECT `src` FROM `projects` WHERE `id` = '$pid'");
                        $srcf = $srcq->fetch();
                        $src = $srcf["src"];

                        $version = array();
                        foreach (glob($src . "/*", GLOB_ONLYDIR) as $dir) {
                            $v = filter_var(basename($dir), FILTER_SANITIZE_NUMBER_INT);
                            if (basename($dir) == "V" . $v || basename($dir) == "v" . $v) {
                                if (array_search($v, $version) === false && in_array($v, $version) === false) {
                                    array_push($version, $v);
                                }
                            }
                        }
                        arsort($version);

                        foreach ($version as $v) {
                            echo '<option value = "' . $v . '">' . $v . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="stats-footer"></div>
            </div>
        </div>
    </div>
    <div class="row costingForm" style="display: none">
        <div class="col-lg-12">
            <div class="widget" style="margin: 20px 0 20px 0">
                <div id="acordion1" class="panel-group">
                    <div class="panel">
                        <div class="panel-heading">
                            <a href="#MaterialForm" data-parent="#acordion1" data-toggle="collapse" class="acordion-toggle collapsed">Opcje materiału</a>
                        </div>
                        <div class="panel-collapse collapse" id="MaterialForm" style="height: 0px;">
                            <div class="panel-body">
                                <div class="col-md-2">
                                    <div class="stats-heading">Waga</div>
                                    <div class="stats-body-alt"> 
                                        <div class="text-center">
                                            <input type="text" id="mWeight" name="mweight" class="form-control"/>
                                        </div>
                                        <div class="stats-footer" style="text-align: center; font-size: x-small;">kg</div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="stats-heading">Cena</div>
                                    <div class="stats-body-alt"> 
                                        <div class="text-center">
                                            <input type="text" id="mPrice" name="mprice" class="form-control"/>
                                        </div>
                                        <div class="stats-footer" style="text-align: center; font-size: x-small;">zł/kg</div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="stats-heading">Długość</div>
                                    <div class="stats-body-alt"> 
                                        <div class="text-center">
                                            <input type="text" id="mLength" name="mlength" class="form-control"/>
                                        </div>
                                        <div class="stats-footer" style="text-align: center; font-size: x-small;">mm</div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="stats-heading">zł/profil</div>
                                    <div class="stats-body-alt"> 
                                        <div class="text-center">
                                            <input type="text" id="mzp" name="mzp" class="form-control" value="0" readonly="readonly"/>
                                        </div>
                                        <div class="stats-footer" style="text-align: center; font-size: x-small;">zł/profil</div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="stats-heading">zł/m</div>
                                    <div class="stats-body-alt"> 
                                        <div class="text-center">
                                            <input type="text" id="mzm" name="mzm" class="form-control" value="0" readonly="readonly"/>
                                        </div>
                                        <div class="stats-footer" style="text-align: center; font-size: x-small;">zł/m</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row costingForm" style="display: none">
        <div class="col-lg-6">
            <div class="widget">
                <div class="widget-content">
                    <table class="table">
                        <tbody>
                            <tr><td style="text-align: right;">Szt/profil</td><td><input type="number" name="pod" id="pod" class="form-control" /></td></tr>
                            <tr><td style="text-align: right;">Czas</td><td><input type="text" name="time" id="time" class="form-control mask" data-inputmask=" 'mask': '99:99:99' "/></td></tr>
                            <tr><td style="text-align: right;">Materiał</td><td><input type="number" name="materialp" id="materialp" class="form-control"/></td></tr>
                            <tr><td style="text-align: right;">Współczynnik</td><td><input type="text" name="factor" id="factor" value="1.00" class="form-control mask" data-inputmask=" 'mask': '9.99' "/></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
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
                                <td><input type="checkbox" name="atribute[]" value="1" style="width: 20px; height: 20px;"/></td>
                                <td>Gięcie</td>
                                <td><input type="text" name="a1i1" id="a1i1" class="form-control ai"/></td>
                                <td><input type="text" name="a1i2" id="a1i2" class="form-control aik"/></td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" name="atribute[]" value="2" class="form-control" style="width: 20px; height: 20px;"/></td>
                                <td>Projekt</td>
                                <td><input type="text" name="a2i1" id="a2i1" class="form-control ai"/></td>
                                <td><input type="text" name="a2i2" id="a2i2" class="form-control aik"/></td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" name="atribute[]" value="3" class="form-control" style="width: 20px; height: 20px;"/></td>
                                <td>Spawanie</td>
                                <td><input type="text" name="a3i1" id="a3i1" class="form-control ai"/></td>
                                <td><input type="text" name="a3i2" id="a3i2" class="form-control aik"/></td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" name="atribute[]" value="4" class="form-control" style="width: 20px; height: 20px;"/></td>
                                <td>Malowanie</td>
                                <td><input type="text" name="a4i1" id="a4i1" class="form-control ai"/></td>
                                <td><input type="text" name="a4i2" id="a4i2" class="form-control aik"/></td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" name="atribute[]" value="5" class="form-control" style="width: 20px; height: 20px;"/></td>
                                <td>Ocynkowanie</td>
                                <td><input type="text" name="a5i1" id="a5i1" class="form-control ai"/></td>
                                <td><input type="text" name="a5i2" id="a5i2" class="form-control aik"/></td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" name="atribute[]" value="6" class="form-control" style="width: 20px; height: 20px;"/></td>
                                <td>Gwintowanie</td>
                                <td><input type="text" name="a6i1" id="a6i1" class="form-control ai"/></td>
                                <td><input type="text" name="a6i2" id="a6i2" class="form-control aik"/></td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" name="atribute[]" value="7" class="form-control" style="width: 20px; height: 20px;"/></td>
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
                                <td>Nazwa</td>
                                <td>Wartość (NETTO)</td>
                                <td>Brutto</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td style="text-align: right;">Costing Cena Detal</td><td><input type="text" name="priceDetailN" id="priceDetailN" class="form-control" readonly/></td><td><input type="text" name="priceDetailB" id="priceDetailB" class="form-control" readonly/></td></tr>
                            <tr><td style="text-align: right;">Cena N</td><td><input type="text" name="priceN" id="priceN" class="form-control" readonly/></td><td>-</td></tr>
                            <tr><td style="text-align: right;">Wartość materiału</td><td><input type="text" name="materialQ" id="materialQ" class="form-control" readonly/></td><td>-</td></tr>
                            <tr><td style="text-align: right;">Wartość kompletu</td><td><input type="text" name="priceSetN" id="priceSetN" class="form-control" readonly/></td><td><input type="text" name="priceSetB" id="priceSetB" class="form-control" readonly/></td></tr>
                            <tr><td style="text-align: right;">Ilość profili</td><td><input type="text" name="profileA" id="profileA" class="form-control" readonly/></td><td>-</td></tr>
                            <tr><td style="text-align: right;">Wartość profilu</td><td><input type="text" name="profileP" id="profileP" class="form-control" readonly/></td><td>-</td></tr>
                            <tr><td style="text-align: right;">Łączny czas cięcia</td><td><input type="text" name="alloTime" id="alloTime" class="form-control" readonly/></td><td>-</td></tr>
                            <tr><td style="text-align: right;">Cena Detal</td><td><input type="text" name="priceDetailuN" id="priceDetailuN" class="form-control"/></td><td><input type="text" name="priceDetailuB" id="priceDetailuB" class="form-control"/></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row costingForm" style="display: none;">
        <div class="col-lg-10"></div>
        <div class="col-lg-2">
            <div class="widget">
                <div class="btn-group">
                    <button class="btn btn-success" type="submit">Zapisz</button>
                    <button class="btn btn-default" type="button" id="cancel">Anuluj</button>
                </div>
            </div>
        </div>
    </div>
</form>

<script type="text/javascript">
    var edit = false;
    var editi = 0;
    var cbv = [-1, -1, -1, -1, -1, -1, -1, -1]; //Check box values :)

    function Round(n, k) // Round function
    {
        var factor = Math.pow(10, k + 1);
        n = Math.round(Math.round(n * factor) / 10);
        return n / (factor / 10);
    }

    function countData() { // Główna funckja liczaca!
        var iTime = $("#time").val().split(":");
        var _time = (parseInt(iTime[0]) * 3600) + (parseInt(iTime[1]) * 60) + (parseInt(iTime[2]) * 1); //s
        var _materialP = parseFloat($("#materialp").val()) / parseFloat($("#pod").val());
        var _detailPrice = ((_time / parseFloat($("#pod").val()) / 60) * 6) * parseFloat($("#factor").val()) + _materialP;

        //detail price = checkbox price + detail price
        var cprice = 0;
        for (i = 0; i < 8; i++) {
            if (cbv[i] > -1) {
                cprice += parseFloat(cbv[i]);
            }
        }
        _detailPrice += cprice;

        if ($("#priceDetailN").val() == Round(_detailPrice, 2)) {
        } else {
            $("#priceDetailN").val(Round(_detailPrice, 2) + " zł");
            $("#priceDetailB").val(Round(_detailPrice * 1.23, 2) + " zł");
            $("#priceDetailuN").val(Round(_detailPrice, 2) + " zł");
            $("#priceDetailuB").val(Round(_detailPrice * 1.23, 2) + " zł");
            $("#priceN").val(Round(_detailPrice / parseFloat($("#factor").val()), 2) + " zł");
            $("#materialQ").val(Round(_materialP, 2) + " zł");
            var _priceDetail = Round(_detailPrice * parseInt($("#pieces").val()), 2)
            $("#priceSetN").val(_priceDetail + " zł");
            $("#priceSetB").val(Round(_priceDetail * 1.23, 2) + " zł");
            var _profileA = Math.ceil(parseInt($("#pieces").val()) / parseInt($("#pod").val()));
            $("#profileA").val(_profileA);
            $("#profileP").val(_profileA * parseInt($("#materialp").val()) + " zł");
            $("#alloTime").val(Round(_time / parseInt($("#pod").val()) * parseInt($("#pieces").val()) / 60, 2));
        }
    }

    function countMaterial() { // Kalkulator materialu 
        $("#mzp").val(parseFloat($("#mWeight").val()) * parseFloat($("#mPrice").val()));
        $("#mzm").val(Math.ceil((parseFloat($("#mWeight").val()) * parseFloat($("#mPrice").val())) / (parseFloat($("#mLength").val()) / 1000)));
    }

    $(document).ready(function () {
        //STATUS CHANGE 
        $.getScript("<?php echo $site_path; ?>/js/status.js");

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
                url: '<?php echo $site_path; ?>/engine/costing/profile.php?a=4&id=' + editi
            }).done(function (_data) {
                $(".costingForm").fadeIn();
                var data = jQuery.parseJSON(_data);
                var atribute = jQuery.parseJSON(data.atribute);

                //SELECT ITEM
                $('#materialSelect option').removeAttr('selected').filter('[value=' + data.material + ']').attr('selected', true);
                $('#mType option').removeAttr('selected').filter('[value=' + data.type + ']').attr('selected', true);
                if (data.type == 1) {
                    $("#diemension3").prop("type", "hidden");
                    $("#diemension3").val("0");
                }

                //INPUT
                $("#time").val(data.time);
                $("#materialp").val(data.materialp);
                $("#pod").val(data.pod);
                $("#factor").val(data.factor);
                $("#project").val(data.project);
                $("#pieces").val(data.pieces);
                var dimension = data.dimension.split("x");
                $("#diemension1").val(dimension[0]);
                $("#diemension2").val(dimension[1]);
                $("#diemension3").val(dimension[2]);
                countData();
                $("#priceDetailuN").val(data.pricedetailu);
                $("#priceDetailuB").val(Round(parseFloat(data.pricedetailu) * 1.23, 2));

                //Chebckbox atribute
                for (i = 1; i < 8; i++) {
                    if (atribute[i] > 0) {
                        $("input:checkbox[value='" + i + "']").prop('checked', true);
                        $.uniform.update();
                        $("#a" + i + "i1").val(parseFloat(atribute[i]));
                        $("#a" + i + "i2").val(Round(parseFloat(atribute[i]) * parseInt($("#pieces").val()), 2));
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
                url: '<?php echo $site_path; ?>/engine/costing/profile.php?a=6&id=' + _id
            }).done(function (msg) {
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
                url: '<?php echo $site_path; ?>/engine/costing/profile.php?a=1&id=' + _id
            }).done(function (msg) {
                var response = jQuery.parseJSON(msg);
                $("#mWeight").val(response.weight);
                $("#mPrice").val(response.price);
                $("#mLength").val(response.length);
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
        $("#cForm").submit(function (event) {
            var complite = true;
            $("#cForm").find("input").each(function () {
                if (!$(this).hasClass("ai") && !$(this).hasClass("aik"))
                {
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
                    url: '<?php echo $site_path; ?>/engine/costing/profile.php?a=' + action + '&did=<?php echo $did; ?>',
                    method: 'POST',
                    data: $("#cForm").serialize()
                }).done(function () {
                    location.reload();
                });
            } else {
                alert("Uzupełnij wszystkie pola!");
            }
            event.preventDefault();
        });
        $("input").change(function () {
            if ($(this).hasClass("ai")) { // Checkbox atribute input one piece
                var _id = parseInt($(this).attr("id").match(/\d/g));
                $("#a" + _id + "i2").val(Round(parseFloat($(this).val()) * parseInt($("#pieces").val()), 2));
                if ($("input:checkbox[value='" + _id + "']").is(':checked')) {
                    cbv[_id] = parseFloat($(this).val());
                }
            }
            if ($(this).hasClass("aik")) { // Checkbox atribute input all piece
                var _id = parseInt($(this).attr("id").match(/\d/g));
                $("#a" + _id + "i1").val(Round(parseFloat($(this).val()) / parseInt($("#pieces").val()), 2));
                if ($("input:checkbox[value='" + _id + "']").is(':checked')) {
                    cbv[_id] = parseFloat($("#a" + _id + "i1").val());
                }
            }
            if ($(this).attr("id") == "pieces") { // Refresh
                $(".aik").each(function () {
                    var _id = parseInt($(this).attr("id").match(/\d/g));
                    if ($(this).val() !== null && $(this).val() !== "") {
                        $(this).val(Round(parseFloat($("#a" + _id + "i1").val()) * parseInt($("#pieces").val()), 2));
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
            if ($(this).attr("id") == "priceDetailuN" || $(this).attr("id") == "priceDetailuB") {
                if ($(this).attr("id") == "priceDetailuN") {
                    $("#priceDetailuB").val(Round(parseFloat($("#priceDetailuN").val()) * 1.23, 2));
                } else {
                    $("#priceDetailuN").val(Round(parseFloat($("#priceDetailuB").val()) / 1.23, 2));
                }
            } else {
                countData();
                countMaterial();
            }
        });
    });
</script>