<?php

ob_start();
require_once dirname(__FILE__) . "/../protect.php";
require_once dirname(__FILE__) . "/../../config.php";

$action = @$_GET["action"];

if ($action == 1) { //Get status change form
    $oid = $_GET["oid"];
    $ds = @$_GET["ds"];

    $content = "<div class=\"input-group\"><select class=\"form-control\" id=\"scs\">";
    for ($i = 1; $i <= $orderStatusMax; $i++) {
        $_t = getOrderStatus($i);
        if ($_t["change"] == true) {
            $add = "";
            if ($i == $ds) {
                $add = ' selected="selected"';
            }
            $content .= "<option value=\"$i\"$add>" . $_t['text'] . "</option>";
        }
    }
    $content .= "</select><span class=\"input-group-addon\" id=\"statusClose\" style=\"cursor: pointer;\"><i class=\"fa fa-remove\"></i></span></div>";
    die($content);
}
if ($action == 2) { // Get status label
    $_s = $_GET["status"];
    $status = getOrderStatus($_s);
    die('<span class="label label-sm ' . $status["color"] . '" style="cursor: pointer;">' . $status["text"] . '</span>');
}
if ($action == 3) { // Change status manualy
    $_s = $_GET["status"];
    $oid = $_GET["oid"];

    $db->query("UPDATE `order` SET `status` = '$_s' WHERE `id` = '$oid'");
    $status = getOrderStatus($_s);
    die('<span class="label label-sm ' . $status["color"] . '" style="cursor: pointer;">' . $status["text"] . '</span>');
}
if ($action == 4) { // Delete program
    $pid = $_GET["pid"];
    $qprogram = $db->query("SELECT `mpw`, `multiplier` FROM `programs` WHERE `id` = '$pid'");
    $program = $qprogram->fetch();

    $mpws = json_decode($program["mpw"], true);
    foreach ($mpws as $key => $value) {
        $val = $value;
        if ($program["multiplier"] > 1) {
            $val = $value * $program["multiplier"];
        }

        //MPW SET
        $newMpw = "";

        $qmpw = $db->query("SELECT `program` FROM `mpw` WHERE `id` = '$key'");
        $fmpw = $qmpw->fetch();
        $mpwProgram = explode("|", $fmpw["program"]);
        for ($i = 0; $i < count($mpwProgram); $i++) {
            if ($mpwProgram[$i] != null) {

                if ($mpwProgram[$i] == $pid) {
                    continue;
                } else {
                    $newMpw .= $mpwProgram[$i] . "|";
                }
            }
        }

        $db->query("UPDATE `mpw` SET `program` = '$newMpw' WHERE `id` = '$key'");
    }

    $db->query("DELETE FROM `programs` WHERE `id` = '$pid'");

    $oid = $_GET["hb"];
    header("Location: $site_path/order/$oid/");
}
if ($action == 5) { //Change multiplier FORM
    $pid = $_GET["pid"];
    $qmp = $db->query("SELECT `multiplier` FROM `programs` WHERE `id` = '$pid'");
    $fmp = $qmp->fetch();
    $multi = $fmp["multiplier"];
    die("<div class=\"input-group\"><input type=\"number\" class=\"form-control\" value=\"$multi\" name=\"multiVal\" id=\"multiVal\"/><span class=\"input-group-addon\" id=\"multiSave\" style=\"cursor: pointer;\"><i class=\"fa fa-check\"></i></span><span class=\"input-group-addon\" id=\"multiClose\" style=\"cursor: pointer;\"><i class=\"fa fa-remove\"></i></span></div>");
}
if ($action == 6) { //Change multiplier
    $pid = $_GET["pid"];
    $val = $_GET["val"];
    $oid = $_GET["oid"];
    $_qpr = $db->query("SELECT `position`, `multiplier` FROM `programs` WHERE `id` = '$pid'");
    $_pr = $_qpr->fetch();

    $position = "";
    $qposition = "";
    if ($val < $_pr["multiplier"]) {
        if ($_pr["position"] != null) {
            $position = explode("|", $_pr["position"]);

            asort($position);
            unset($position[count($position) - 1]);
            $qposition = $position[0];
            for ($i = 1; $i < count($position); $i++) {
                $qposition .= "|" . $position[$i];
            }
        }
    } else {
        $position = $_pr["position"];
    }

    $db->query("UPDATE `programs` SET `multiplier` = '$val', `position` = '$qposition' WHERE `id` = '$pid'");

    $aOid = array();
    array_push($aOid, $oid);
    orderCheck($aOid);
    header("Location: $site_path/order/$oid/");
}
if ($action == 7) { //Delete item
    $oitemId = $_GET["oitemId"];

    $oitemQuery = $db->prepare('
        SELECT
        cqd.id,
        oi.dct,
        cqd.cutting_queue_list_id,
        cql, 
        FROM
        cutting_queue_details cqd
        LEFT JOIN oitems oi ON oi.id = cqd.oitem_id
        LEFT JOIN cutting_queue_list cql ON cql.id = cqd.cutting_queue_list_id
        WHERE
        cqd.oitem_id = :oitemId
    ');
    $oitemQuery->bindValue(':oitemId', $oitemId. PDO::PARAM_INT);
    $oitemQuery->execute();

    while($cuttingQueueDetails = $oitemQuery->fetch())
    {
        if ($cuttingQueueDetails['dct'] <= 0) {
            $db->query('DELETE FROM cutting_queue_list WHERE id = ' . $cuttingQueueDetails['id']);
        } else {
            break;
        }
    }

    $db->query("DELETE FROM `oitems` WHERE `id` = '$oitemId'");
    die("1");
}
ob_end_flush();
