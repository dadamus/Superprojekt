<?php

require_once dirname(__FILE__) . '/config.php';
require_once dirname(__FILE__) . '/engine/class/material.php';

$Material = new Material();

$scan = scandir($data_src . "temp");
$files = array();

foreach ($scan as $file) {
    if (!is_dir($file)) {
        array_push($files, $file);
    }
}

$gC_mb = @$_GET["c_mb"];
$p_a = @$_GET["p_a"];

if ($p_a == 1) { //Program add
    require_once dirname(__FILE__) . '/aengine/pm.php';
} else if ($p_a == 2) {//Check img exist 
    $_parts = $_GET["parts"];

    $resp = "";
    $parts = explode('|', $_parts);
    foreach ($parts as $part) {
        $_name = str_replace(' ', '', $part);

        $did = null;

        $ne = explode("-", $_name);
        $did = $ne[2];

        /* $qmpw = $db->query("SELECT `did` FROM `mpw` WHERE `code` = '$_name'");
          if ($mpw = $qmpw->fetch()) {
          $did = $mpw["did"];
          } else {
          $qoitem = $db->query("SELECT `mpw` FROM `oitems` WHERE `code` = '$_name'");
          if ($oitem = $qoitem->fetch()) {
          $qmpw = $db->query("SELECT `did` FROM `mpw` WHERE `id` = '" . $oitem["mpw"] . "'");
          $fmpw = $qmpw->fetch();
          $did = $fmpw["did"];
          }
          } */

        if ($did == null) {
            continue;
        } else {
            $dquery = $db->query("SELECT count(*) FROM `details` WHERE `id` = '$did' AND `img` = ''");
            $selected = $dquery->fetchColumn();
            if ($selected > 0) {
                $resp .= $part . "#" . $did . "|";
            }
        }
    }

    die($resp);
} else if ($p_a == 3) { //Save img
    $parts = $_GET["d"];
    $to_save = explode("|", $parts);
    foreach ($to_save as $img) {
        $db->query("UPDATE `details` SET `img` = '" . $img . ".bmp' WHERE `id` = '$img'");
    }
    die("Zapisałem obrazki.");
} else if ($p_a == 4) { //Copy db
    $new = $db->query("SELECT `id` FROM `plate_warehouse` WHERE `date` = '0000-00-00 00:00:00'");
    $date = date("Y-m-d H:i:s");

    foreach ($new as $row) {
        $db->query("UPDATE `plate_warehouse` SET `date` = '$date', `type` = '1' WHERE `id` = '" . $row['id'] . "'");
    }

    $nesting = $db->query("SELECT `id` FROM `plate_warehouse` WHERE UPPER(SheetCode) LIKE '%NEST%'");
    foreach ($nesting as $row) {
        $db->query("UPDATE `plate_warehouse` SET `type` = '4' WHERE `id` = '" . $row["id"] . "'");
    }

    die("php done");
} else if ($p_a == 5) { //Upload T_materialType
    $data = $_GET["data"];
} else if ($p_a == "check_costing_line") { //Check detail in costing line
    $code = $_GET["code"];

    $detail = $db->query("SELECT * FROM `mpw` WHERE `code` = '$code'");
    if ($data = $detail->fetch()) {
        echo json_encode($data);
    }
    else
    {
        echo json_encode(null);
    }

    die();
} else if ($p_a == "tube_single") {
    require_once dirname(__FILE__) . '/aengine/mpc.php';
} else if ($p_a == "add_plate_costing_single") {
    require_once dirname(__FILE__) . '/engine/costing/plateSinglePart.php';

    $plateData = new plateSinglePart($data_src . "temp/plate.csv");
    $plateData->saveImage();
    $plateData->saveInputData();
}

/*$delete = false;
if (count($files) == 1) {
    switch ($files[0]) {
        case "PrintData.xml":
            require_once dirname(__FILE__) . '/aengine/sptc.php';
            break;
        default:
            //Error delete all files
            foreach ($files as $file) {
                unlink($data_src . "temp/" . $file);
            }
            break;
    }
}*/