<?php

require_once dirname(__FILE__) . '/mpc_inputs.php';
require_once dirname(__FILE__) . '/mpc_count.php';

$xml = simplexml_load_file($data_src . "temp/TubeReport.xml");

$GLOBALS["name"] = null;
$GLOBALS["wasteMode"] = null;
$GLOBALS["material"] = null;
$GLOBALS["width"] = null;
$GLOBALS["height"] = null;
$GLOBALS["diameter"] = null;
$GLOBALS["c_mb"] = null;
$GLOBALS["mtype"] = null;
$GLOBALS["thickness"] = null;
$GLOBALS["mname"] = null;
$GLOBALS["pname"] = null;
$GLOBALS["type"] = null;

$cut_all_time = null;

$settings = array();
$qsettings = $db->query("SELECT * FROM `settings`");
foreach ($qsettings as $row) {
    $settings[$row["name"]] = floatval($row["value"]);
}

function addTime($_time) {
    $time = explode(":", $_time);
    return($time[0] * 3600) + ($time[1] * 60) + $time[2];
}

$oq = 0;
$d_qty = 0;
$dlugosc_mat = 0;
$zajetosc_mat = 0;
$d_weight = 0;
$tps = 0;
$tpl = 0;
$s_dqty = "";

function getTube($tube) {
    if ($GLOBALS["name"] == null) {
        //Get pname
        $part = $tube->Part[0]->attributes();
        $GLOBALS["pname"] = str_replace(' ', '', $part["Name"]);
        //GET name
        $GLOBALS["name"] = $tube->Header->Name;
        $_name = str_split($GLOBALS["name"]);
        if ($_name[0] . $_name[1] != "W3") {
            die("2");
        }

        $n_e = explode("+", $GLOBALS["name"]);
        $wm = 0;

        foreach ($n_e as $np) {
            if ($np == "I") {
                $wm = 1;
            }
        }
        $GLOBALS["paramI"] = $wm;
        $GLOBALS["wasteMode"] = 0;

        //GET $c_mb
        if ($GLOBALS["gC_mb"] != null) {
            $GLOBALS["c_mb"] = floatval($GLOBALS["gC_mb"]);
        } else if (@$_name[3] == "C") {
            $GLOBALS["estring"] = explode("-", $tube->Header->Name);
            $GLOBALS["sc_mb"] = substr($GLOBALS["estring"][0], 4);
            $GLOBALS["c_mb"] = floatval(str_replace("P", ".", $GLOBALS["sc_mb"]));
        } else {
            die("Brak ceny za mter bierzący!");
        }

        //Get material
        $GLOBALS["_material"] = substr($tube->Header->MaterialShape, 1);
        $GLOBALS["types"] = array("Tube", "Round", "Channel", "Angle");
        foreach ($GLOBALS["types"] as $key => $type) {
            if (strpos($GLOBALS["_material"], $type) !== false) {
                $GLOBALS["type"] = $key;
            }
        }
        if ($GLOBALS["type"] === null) {
            die("Nieznany typ materiału!");
        }

        $GLOBALS["wh"] = str_replace(' ', '', $tube->Header->WidthHeightDiameter);
        $GLOBALS["diameter"] = floatval(str_replace(' ', '', $tube->Header->Diameter));

        $GLOBALS["mtype"] = str_replace(' ', '', $tube->Header->{'Material-Type'});
        $GLOBALS["thickness"] = floatval(str_replace(' ', '', $tube->Header->Thickness));
        $GLOBALS["mname"] = str_replace(' ', '', $tube->Header->{'Material-Name'});
        $GLOBALS["tps"] = floatval(str_replace(' ', '', $tube->Header->TotalPartsWeight));
        $GLOBALS["tpl"] = floatval(str_replace(' ', '', $tube->Header->TotalLength));
    }

    $GLOBALS["oq"] += intval(str_replace(' ', '', $tube->Header->Qty));
    $GLOBALS["dlugosc_mat"] += intval(str_replace(' ', '', $tube->Header->Qty)) * floatval(str_replace(' ', '', $tube->Header->PipeLength));
    $GLOBALS["zajetosc_mat"] += intval(str_replace(' ', '', $tube->Header->Qty)) * floatval(str_replace(' ', '', $tube->Header->TotalLength));
    $GLOBALS["cut_all_time"] += addTime(str_replace(' ', '', $tube->Header->TimeStudy)) * intval(str_replace(' ', '', $tube->Header->Qty));

    //Detail
    $dqt = 0;
    foreach ($tube->Part as $part) {
        $dqt++;
    }

    $tp = intval(str_replace(' ', '', $tube->Header->Qty));
    $GLOBALS["s_dqty"] .= "($dqt * $tp), ";
    $GLOBALS["d_qty"] += $dqt * $tp;

    if (@$GLOBALS["d_weight"] == 0) {
        $GLOBALS["d_weight"] = $GLOBALS["tps"] / $dqt;
    }
}

//Main xml
if ($xml->TubeReport->count() == null) {
    $ctubes = 1;
    getTube($xml);
} else {
    $ctubes = count($xml->TubeReport);
    foreach ($xml->TubeReport as $tube) {
        getTube($tube);
    }
}

//DetailName.xml
$dnxml = simplexml_load_file($data_src . "temp/$pname.xml");
$AreaWithoutHoles = floatval($dnxml->Part[0]->Header[0]->AreaWithoutHoles);
$AreaWithHoles = floatval($dnxml->Part[0]->Header[0]->AreaWithHoles);

$dnsxml = simplexml_load_file($data_src . "temp/" . $pname . "-shd.xml");
$AreaWithoutHolesSHD = floatval($dnxml->Part[0]->Header[0]->AreaWithoutHoles);

/* $qmaterial_data = $db->query("SELECT `price`, `waste` FROM `material` WHERE `name` = '$mtype'");
  $material_data = $qmaterial_data->fetch();
  $remnant_calc = ceil($material_data["price"] / $material_data["waste"]);

  $clean_cut = $cut_all_time / 60 * $settings["cut"];
  $przeladunek = $settings["otime"] / 60 * $settings["ocost"] * $oq;
  $cut_all = $clean_cut + $przeladunek;

  $cut_all_netto = $cut_all * $settings["p_factor"];
  $cut_all_brutto = $cut_all_netto * 1.23;
  $d_clean_cut = $clean_cut / $d_qty;
  $d_przeladunek = $przeladunek / $d_qty;
  $d_cut_all = $cut_all / $d_qty;
  $d_cut_all_netto = $cut_all_netto / $d_qty;
  $d_cut_all_brutto = $cut_all_brutto / $d_qty;

  $waga_1m = $tps / $tpl;
  $rm_odpad = $dlugosc_mat - $zajetosc_mat;
  $tcost = $c_mb / 1000 * $dlugosc_mat;
  $cena_mat_ciet = $c_mb / 1000 * $zajetosc_mat;
  $cost_mat_kg = $c_mb / $waga_1m;
  $waga_rm = $waga_1m / 1000 * $rm_odpad;
  $rm_value = $waga_rm * $material_data["waste"] * $settings["remnant_factor"];
  $d_rmn = $rm_value / $d_qty;
  $cost_all_price = $tcost - $rm_value;
  $d_mat = $cost_all_price / $d_qty;
  $d_rmn = $rm_value / $d_qty;

  $mprice = $material_data["price"];
  $mwaste = $material_data["waste"];
  $scut = $settings["cut"];
  $sotime = $settings["otime"];
  $socost = $settings["ocost"];
  $sp_factor = $settings["p_factor"];
  $s_remnant_factor = $settings["remnant_factor"]; */
mpc_count();
INPUT_INIT();

$pdata = $db->query("SELECT `id`, `pid`, `did`, `src`, `pieces` FROM `mpw` WHERE `code` = '$pname' AND `type` = '0'");
if ($fpdata = $pdata->fetch()) {
    /* if (file_exists($fpdata["src"]) == false) {
      die('Brak pliku shd!');
      } */

    if ($fpdata["pieces"] != $d_qty) {
        die('Liczba detali się nie zgadza! ' . $s_dqty);
    }

    $mpwid = $fpdata["id"];
    $mpwu = $db->query("UPDATE `mpw` SET `type` = '1' WHERE `id` = '$mpwid'");
    //Detail set type to profile
    $did = $fpdata["did"];
    $dst = $db->query("UPDATE `details` SET `type` = '2' WHERE `id` = '$did'");

    $prepare = "INSERT INTO `mpc` (wid, ";
    foreach ($_INPUTS->inputs as $inp) {
        $prepare .= $inp->name . ", ";
    }
    $prepare .= "udate) VALUES ('$mpwid', ";
    foreach ($_INPUTS->inputs as $inp) {
        $prepare .= ":" . $inp->name . ", ";
    }
    $prepare .= ":udate)";

    $q = $db->prepare($prepare);
    $_INPUTS->BindInputs($q);
    $date = date("Y-m-d H:i:s");
    $q->bindValue(":udate", $date, PDO::PARAM_STR);
    $q->execute();

    @unlink($fpdata["src"]); //Delete file
    @unlink($data_src . "temp/TubeReport.xml");
    @unlink($data_src . "temp/$pname.xml");
    @unlink($data_src . "temp/" . $pname . "-shd.xml");

    die("Dodałem");
} else {
    die("Nie znalazłem detalu w bazie.");
}
