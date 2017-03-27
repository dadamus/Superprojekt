<?php

$xml = simplexml_load_file($data_src . "temp/TubeReport.xml");

$oitem = array();
$pname = "";

$material_name = "";
$material_type = "";
$thickness = 0;
$pierceposition = 0;
$pipelength = 0;
$totallength = 0;
$widthheightdiameter = "";
$widthheight = "";
$diameter = 0;
$cpipecornerr = 0;
$utilization = 0;
$timestudy = 0; // in seconds

function getTube($tube) {
    if ($GLOBALS["pname"] == null) {
        $GLOBALS["pname"] = substr($tube->Header->Name, 0, -4);
        $GLOBALS["material_name"] = str_replace(" ", "", $tube->Header->{"Material-Name"});
        $GLOBALS["material_type"] = str_replace(" ", "", $tube->Header->{"Material-Type"});
        $GLOBALS["thickness"] = floatval(str_replace(" ", "", $tube->Header->Thickness));
        $GLOBALS["pierceposition"] = floatval(str_replace(" ", "", $tube->Header->PiercePosition));
        $GLOBALS["pipelength"] = floatval(str_replace(" ", "", $tube->Header->PipeLength));
        $GLOBALS["widthheightdiameter"] = str_replace(" ", "", $tube->Header->WidthHeightDiameter);
        $GLOBALS["widthheight"] = str_replace(" ", "", $tube->Header->WidthHeight);
        $GLOBALS["diameter"] = str_replace(" ", "", $tube->Header->Diameter);
        $GLOBALS["cpipecornerr"] = floatval(str_replace(" ", "", $tube->Header->CPipeCornerR));
        
        if ($GLOBALS["pname"][0] != "T") {
            die("2");
        }
    }

    $GLOBALS["totallength"] += floatval(str_replace(" ", "", $tube->Header->TotalLength));
    $GLOBALS["utilization"] += floatval(str_replace(" ", "", $tube->Header->Utilization));
    $GLOBALS["timestudy"] += _timeToSec(str_replace(" ", "", $tube->Header->TimeStudy));
    
    
    $qty = str_replace(' ', '', $tube->Header->Qty);
    foreach ($tube->Part as $part) {
        $atributes = $part->attributes();
        $name = str_replace(' ', '', $atributes["Name"]);
        if (@$GLOBALS["oitem"][$name] == 0) {
            $GLOBALS["oitem"][$name] = $qty;
        } else {
            $GLOBALS["oitem"][$name] += $qty;
        }
    }
}

if ($xml->TubeReport->count() == null) {
    $ctubes = 1;
    getTube($xml);
} else {
    $ctubes = count($xml->TubeReport);
    foreach ($xml->TubeReport as $tube) {
        getTube($tube);
    }
}

$mpwl = array();
$jitems = array();

foreach ($oitem as $key => $row) {
    $n = $key . ".shd";
    $qoitem = $db->query("SELECT `id`, `mpw` FROM `oitems` WHERE `code` = '$n'");
    $i = $qoitem->fetch();
    $oitemid = $i["id"];
    $mpw = $i["mpw"];

    echo $key . " => " . $oitemid . "|";

    $mpwl[$key] = $mpw;
    $jitems[$mpw] = $row;
}


$jeitems = json_encode($jitems);
$date = date("Y-m-d H:i:s");

$inputs = array();
function addInput($name, $type) {
    global $inputs;
    array_push($inputs, array("name" => $name, "type" => $type));
}
function getInputName($mark = "`", $prefix = "") {
    global $inputs;
    $return = "";
    foreach($inputs as $input) {
        $return .= $mark.$prefix.$input["name"].$mark.", ";
    }
    return $return;
}
function bindInput($query) {
    global $inputs;
    foreach($inputs as $input) {
        $query->bindValue(":".$input["name"], $GLOBALS[$input["name"]], $input["type"]);
    }
}

addInput("material_name", PDO::PARAM_STR);
addInput("material_type", PDO::PARAM_STR);
addInput("thickness", PDO::PARAM_STR);
addInput("pierceposition", PDO::PARAM_STR);
addInput("pipelength", PDO::PARAM_STR);
addInput("totallength", PDO::PARAM_STR);
addInput("widthheightdiameter", PDO::PARAM_STR);
addInput("widthheight", PDO::PARAM_STR);
addInput("diameter", PDO::PARAM_STR);
addInput("cpipecornerr", PDO::PARAM_STR);
addInput("utilization", PDO::PARAM_STR);
addInput("timestudy", PDO::PARAM_STR);

$in = getInputName();
$inp = getInputName("", ":");

$qe = $db->prepare("INSERT INTO `programs` ($in `name`, `mpw`, `date`) VALUES ($inp '$pname', '$jeitems', '$date')");
bindInput($qe);
$qe->execute();

$pid = $db->lastInsertId();

$orders = array();
foreach ($jitems as $key => $value) {
    $qpmpw = $db->query("SELECT `program`, `pid` FROM `mpw` WHERE `id` = '$key'");
    $fmpw = $qpmpw->fetch();

    $qoid = $db->query("SELECT `oid` FROM `oitems` WHERE `mpw` = '$key'");
    $oid = $qoid->fetch();
    if (array_search($fmpw["pid"], $orders) === false) {
        array_push($orders, $fmpw["pid"]);
    }

    $program = $pid . "|" . $fmpw["program"];
    $db->query("UPDATE `mpw` SET `program` = '$program' WHERE `id` = '$key'");
}

orderCheck($orders);