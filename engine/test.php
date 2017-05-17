[{"SheetCode":
"0009-1980X666X4MM",
"MaterialName":"SPH-4",
"QtyAvailable":1,
"GrainDirection":1,
"Width":1980.0,
"Height":666.0,
"SpecialInfo":"0",
"Comment":"",
"SheetType":"Remnant",
"SkeletonFile":"",
"SkeletonData":"",
"MD5":"","Price":0.0,"Priority":1
},
{
"SheetCode":"SECC3.0-2000X1250","MaterialName":"SECC3.0","QtyAvailable":3,"GrainDirection":1,"Width":2000.0,"Height":1250.0,"SpecialInfo":"0","Comment":"z hali","SheetType":"Standard","SkeletonFile":"","SkeletonData":"","MD5":"","Price":0.0,"Priority":1}, {"SheetCode":"SECC3.0-2510X1250","MaterialName":"SECC3.0","QtyAvailable":4,"GrainDirection":1,"Width":2510.0,"Height":1250.0,"SpecialInfo":"0","Comment":"z hali","SheetType":"Standard","SkeletonFile":"","SkeletonData":"","MD5":"","Price":0.0,"Priority":1}, {"SheetCode":"0010-830X1435X3MM","MaterialName":"SECC3.0","QtyAvailable":1,"GrainDirection":1,"Width":830.0,"Height":1435.0,"SpecialInfo":"0","Comment":"","SheetType":"Remnant","SkeletonFile":"","SkeletonData":"","MD5":"","Price":0.0,"Priority":1}, {"SheetCode":"SPH5.0-3000X1500-STALPROD","MaterialName":"SPH-5","QtyAvailable":0,"GrainDirection":1,"Width":3002.0,"Height":1512.0,"SpecialInfo":"0","Comment":"stalprodukt","SheetType":"Standard","SkeletonFile":"","SkeletonData":"","MD5":"","Price":0.0,"Priority":1}, {"SheetCode":"SPC3.0-3000X1500","MaterialName":"SPC-3","QtyAvailable":0,"GrainDirection":1,"Width":3000.0,"Height":1500.0,"SpecialInfo":"0","Comment":"","SheetType":"Standard","SkeletonFile":"","SkeletonData":"","MD5":"","Price":0.0,"Priority":1}, {"SheetCode":"0013-2000X1000X3MM","MaterialName":"SPC-3","QtyAvailable":1,"GrainDirection":1,"Width":2000.0,"Height":1000.0,"SpecialInfo":"0","Comment":"","SheetType":"Remnant","SkeletonFile":"","SkeletonData":"","MD5":"","Price":0.0,"Priority":1}, {"SheetCode":"SUS5.0-2000X1000","MaterialName":"SUS3045.0","QtyAvailable":-1,"GrainDirection":1,"Width":2000.0,"Height":1000.0,"SpecialInfo":"0","Comment":"NOVA Trading","SheetType":"Standard","SkeletonFile":"","SkeletonData":"","MD5":"","Price":0.0,"Priority":1}]
<?php


require_once "tools/toolsEngine.php";

$plate = new PlateWarehouse();
echo $plate->parsePlate([
	"test" => "test1",
	"superIndex" => "test3"
]);

//
//require_once '../config.php';
//$item = 18;
//$mpwq = $db->query("SELECT `src`, `did`, `pid`, `atribute`, `pieces`, `material`, `type`, `code`, `version`, `radius`, `mcp` FROM `mpw` WHERE `id` = '$item'");
//$mpw = $mpwq->fetch();
//$pid = $mpw["pid"];
//$projq = $db->query("SELECT `cid`, `src` FROM `projects` WHERE `id` = '$pid'");
//$proj = $projq->fetch();
//$cid = $proj["cid"];
//
//$mpcq = $db->query("SELECT `type`, `mtype`, `thickness`, `wh` FROM `mpc` WHERE `wid` = '$item'");
//$mpc = $mpcq->fetch();
//
//$did = $mpw["did"];
//$dq = $db->query("SELECT `type`, `src` FROM `details` WHERE `id` = '$did'");
//$d = $dq->fetch();
//
//$main = "";
//$dim = "";
//echo $mpw["type"];
//
//$new_type = 2;
//if ($mpw["type"] == 1) { //Profil
//    $main = "roto";
//    $thickness = floatval($mpc["thickness"]);
//
//    //DIR
//    if ($mpc["type"] == 0) { //Profil
//        $wh = explode("X", $mpc["wh"]);
//        $dim = floatval($wh[0]) . "x" . floatval($wh[1]) . "x" . floatval($mpc["thickness"]);
//    } else if ($mpc["type"] == 1) { //Rura
//        $dim = "fi" . floatval($mpc["wh"]) . "x" . floatval($mpc["thickness"]);
//    } else { //Inne
//        $dim = "k" . floatval($mpc["thickness"]);
//    }
//} else if ($mpw["type"] == 3) {//Profil manual
//    $main = "roto";
//    $new_type = 4;
//    $qpc = $db->query("SELECT `dimension`, `type` FROM `profile_costing` WHERE `id` = '" . $mpw["mcp"] . "'");
//    $pc = $qpc->fetch();
//
//    echo "$dim: ".$pc["dimension"];
//    $exdim = explode("x", $pc["dimension"]);
//    $thickness = floatval(end($exdim));
//
//    if ($pc["type"] == 1) {
//        $dim = "fi" . $pc["dimension"];
//    } else {
//        $dim = $pc["dimension"];
//    }
//} else if ($mpw["type"] == 5) { // Blacha
//    $main = "sheet";
//
//    $dim = floatval($mpc["thickness"]);
//}
//
//$dpath = $data_src . "cutting/" . $main;
//
////Get material folder
//if ($mpw["type"] == 3) {
//    $materialId = $mpw["material"];
//    $mq = $db->query("SELECT `lname` FROM `material` WHERE `id` = '$materialId'");
//    $m = $mq->fetch();
//    $sm = strtoupper($m["lname"][0]);
//} else {
//    $materialSname = $mpc["mtype"];
//    $mq = $db->query("SELECT `lname` FROM `material` WHERE `name` = '$materialSname'");
//    $m = $mq->fetch();
//    $sm = strtoupper($m["lname"][0]);
//}
//
//
//$dpath .= "/" . $m["lname"];
//$dpath .= "/" . str_replace(".", "P", $dim);
//
////Name
//$esrc = explode(".", $d["src"]);
//$ext = end($esrc);
//
//$j_atr = json_decode($mpw["atribute"], true);
//$a = "";
//if (count($j_atr) > 0) {
//    foreach ($j_atr as $atr) {
//        $a .= _getChecboxText($atr);
//    }
//}
//$atribute = "-" . $a;
///* $ecode = explode("-", $mpw["code"]);
//  $atribute = "";
//  if (count($ecode) > 3) {
//  $atribute = "-" . end($ecode);
//  } */
//
//$newName = $cid . "-" . $mpw["pieces"] . "X" . $thickness . "-$sm-$item" . $atribute . "." . $ext;
//echo "|" . $newName;
//?>