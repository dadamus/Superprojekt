<?php

$xml = simplexml_load_file($data_src . "temp/TubeReport.xml");

class Data
{
    public $oitem = [];
    public $pname = "";

    public $material_name = "";
    public $material_type = "";
    public $thickness = 0;
    public $pierceposition = 0;
    public $pipelength = 0;
    public $totallength = 0;
    public $widthheightdiameter = "";
    public $widthheight = "";
    public $diameter = 0;
    public $cpipecornerr = 0;
    public $utilization = 0;
    public $timestudy = 0; // in seconds
}

$data = new Data();

function getTube(Data $data, $tube)
{
    if ($data->pname == null) {
        $data->pname = substr($tube->Header->Name, 0, -4);
        $data->material_name = str_replace(" ", "", $tube->Header->{"Material-Name"});
        $data->material_type = str_replace(" ", "", $tube->Header->{"Material-Type"});
        $data->thickness = floatval(str_replace(" ", "", $tube->Header->Thickness));
        $data->pierceposition = floatval(str_replace(" ", "", $tube->Header->PiercePosition));
        $data->pipelength = floatval(str_replace(" ", "", $tube->Header->PipeLength));
        $data->widthheightdiameter = str_replace(" ", "", $tube->Header->WidthHeightDiameter);
        $data->widthheight = str_replace(" ", "", $tube->Header->WidthHeight);
        $data->diameter = str_replace(" ", "", $tube->Header->Diameter);
        $data->cpipecornerr = floatval(str_replace(" ", "", $tube->Header->CPipeCornerR));

        if ($data->pname[0] != "T") {
            die("2");
        }
    }

    $data->totallength += floatval(str_replace(" ", "", $tube->Header->TotalLength));
    $data->utilization += floatval(str_replace(" ", "", $tube->Header->Utilization));
    $data->timestudy += _timeToSec(str_replace(" ", "", $tube->Header->TimeStudy));


    $qty = str_replace(' ', '', $tube->Header->Qty);
    foreach ($tube->Part as $part) {
        $atributes = $part->attributes();
        $name = str_replace(' ', '', $atributes["Name"]);
        if (@$data->oitem[$name] == 0) {
            $data->oitem[$name] = $qty;
        } else {
            $data->oitem[$name] += $qty;
        }
    }
}

if ($xml->TubeReport->count() == null) {
    $ctubes = 1;
    getTube($data, $xml);
} else {
    $ctubes = count($xml->TubeReport);
    foreach ($xml->TubeReport as $tube) {
        getTube($data, $tube);
    }
}

$mpwl = array();
$jitems = array();

foreach ($data->oitem as $key => $row) {
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
function addInput($name, $type)
{
    global $inputs;
    array_push($inputs, array("name" => $name, "type" => $type));
}

function getInputName($mark = "`", $prefix = "")
{
    global $inputs;
    $return = "";
    foreach ($inputs as $input) {
        $return .= $mark . $prefix . $input["name"] . $mark . ", ";
    }
    return $return;
}

function bindInput($query)
{
    global $inputs, $data;
    foreach ($inputs as $input) {
        $query->bindValue(":" . $input["name"], $data->$input["name"], $input["type"]);
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

$qe = $db->prepare("INSERT INTO `programs` ($in `name`, `mpw`, `date`) VALUES ($inp '$data->pname', '$jeitems', '$date')");
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