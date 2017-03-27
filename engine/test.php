<?php

require_once '../config.php';
$item = 18;
$mpwq = $db->query("SELECT `src`, `did`, `pid`, `atribute`, `pieces`, `material`, `type`, `code`, `version`, `radius`, `mcp` FROM `mpw` WHERE `id` = '$item'");
$mpw = $mpwq->fetch();
$pid = $mpw["pid"];
$projq = $db->query("SELECT `cid`, `src` FROM `projects` WHERE `id` = '$pid'");
$proj = $projq->fetch();
$cid = $proj["cid"];

$mpcq = $db->query("SELECT `type`, `mtype`, `thickness`, `wh` FROM `mpc` WHERE `wid` = '$item'");
$mpc = $mpcq->fetch();

$did = $mpw["did"];
$dq = $db->query("SELECT `type`, `src` FROM `details` WHERE `id` = '$did'");
$d = $dq->fetch();

$main = "";
$dim = "";
echo $mpw["type"];

$new_type = 2;
if ($mpw["type"] == 1) { //Profil
    $main = "roto";
    $thickness = floatval($mpc["thickness"]);

    //DIR
    if ($mpc["type"] == 0) { //Profil
        $wh = explode("X", $mpc["wh"]);
        $dim = floatval($wh[0]) . "x" . floatval($wh[1]) . "x" . floatval($mpc["thickness"]);
    } else if ($mpc["type"] == 1) { //Rura
        $dim = "fi" . floatval($mpc["wh"]) . "x" . floatval($mpc["thickness"]);
    } else { //Inne
        $dim = "k" . floatval($mpc["thickness"]);
    }
} else if ($mpw["type"] == 3) {//Profil manual
    $main = "roto";
    $new_type = 4;
    $qpc = $db->query("SELECT `dimension`, `type` FROM `profile_costing` WHERE `id` = '" . $mpw["mcp"] . "'");
    $pc = $qpc->fetch();
    
    echo "$dim: ".$pc["dimension"];
    $exdim = explode("x", $pc["dimension"]);
    $thickness = floatval(end($exdim));

    if ($pc["type"] == 1) {
        $dim = "fi" . $pc["dimension"];
    } else {
        $dim = $pc["dimension"];
    }
} else if ($mpw["type"] == 5) { // Blacha
    $main = "sheet";

    $dim = floatval($mpc["thickness"]);
}

$dpath = $data_src . "cutting/" . $main;

//Get material folder
if ($mpw["type"] == 3) {
    $materialId = $mpw["material"];
    $mq = $db->query("SELECT `lname` FROM `material` WHERE `id` = '$materialId'");
    $m = $mq->fetch();
    $sm = strtoupper($m["lname"][0]);
} else {
    $materialSname = $mpc["mtype"];
    $mq = $db->query("SELECT `lname` FROM `material` WHERE `name` = '$materialSname'");
    $m = $mq->fetch();
    $sm = strtoupper($m["lname"][0]);
}


$dpath .= "/" . $m["lname"];
$dpath .= "/" . str_replace(".", "P", $dim);

//Name
$esrc = explode(".", $d["src"]);
$ext = end($esrc);

$j_atr = json_decode($mpw["atribute"], true);
$a = "";
if (count($j_atr) > 0) {
    foreach ($j_atr as $atr) {
        $a .= _getChecboxText($atr);
    }
}
$atribute = "-" . $a;
/* $ecode = explode("-", $mpw["code"]);
  $atribute = "";
  if (count($ecode) > 3) {
  $atribute = "-" . end($ecode);
  } */

$newName = $cid . "-" . $mpw["pieces"] . "X" . $thickness . "-$sm-$item" . $atribute . "." . $ext;
echo "|" . $newName;
?>