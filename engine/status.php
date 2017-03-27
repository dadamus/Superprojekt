<?php

$STATUS_NEW = 0;
$STATUS_AUTO = 1;
$STATUS_PRICE = 2;
$STATUS_IMPORTANT = 3;
$STATUS_WARING = 4;
$STATUS_REJECTED = 5;
$STATUS_IN_LINE = 6;
$STATUS_ACCEPT = 7;
$STATUS_OK = 8;
$STATUS_OLD = 9;

$STATUS_COUNT = 9;

$STATUS_ALLOWED = array($STATUS_IMPORTANT,
    $STATUS_WARING,
    $STATUS_REJECTED,
    $STATUS_ACCEPT);

$STATUS_ICONS = array("fa fa-plus-square",
    "fa fa-cog",
    "fa fa-tag",
    "fa fa-star",
    "fa fa-exclamation-triangle",
    "fa fa-trash",
    "fa fa-industry",
    "fa fa-thumbs-up",
    "fa fa-check",
    "fa fa-hourglass-end");

function insertStatus($did, $status) {
    global $db;
    $date = date("Y-m-d H:i:s");

    $query = $db->prepare("SELECT `id` FROM `status` WHERE `did` = '$did' AND `type` = '$status'");
    $query->execute();

    if ($query->rowCount() == 0) {
        $insert = $db->prepare("INSERT INTO `status` (`did`, `type`, `date`) VALUES ('$did', '$status', '$date')");
        $insert->execute();
    }
}

function deleteStatus($did, $status) {
    global $db;
    $query = $db->prepare("DELETE FROM `status` WHERE `did` = '$did' AND `type` = '$status'");
    $query->execute();
}

function getStatus($did) {
    global $db;
    statusRefresh($did);

    $query = $db->prepare("SELECT `type` FROM `status` WHERE `did` = '$did'");
    $query->execute();

    $output = $query->fetchAll(PDO::FETCH_NUM);
    return $output;
}

function statusRefresh($did) {
    global $db, $STATUS_NEW;
    
    $time = date("Y-m-d H:i:s");
    $date = DateTime::createFromFormat("Y-m-d H:i:s", $time);
    $date->modify('-7 day');
    $sdate = $date->format("Y-m-d H:i:s");
    
    $squery = $db->query("SELECT `id`, `type` FROM `status` WHERE `date` < '$sdate' AND `type` = '$STATUS_NEW'");
    foreach($squery as $row) {
        if ($row["type"] == $STATUS_NEW) {
            $sid = $row["id"];
            $db->query("DELETE FROM `status` WHERE `id` = '$sid'");
        }
    }
    
}

function statusCosting($did) {
    global $db, $STATUS_COUNT, $STATUS_ICONS;
    $output = "";

    $status = getStatus($did);
    for ($i = 0; $i <= $STATUS_COUNT; $i++) {
        $class = "";
        for ($s = 0; $s < count($status); $s++) {
            if ($status[$s][0] == $i) {
                $class = "btn-success";
            }
        }
        $output .= '<div class="status_icon" id="' . $i . '_status"><a class="' . $class . ' sb btn btn-small" href="#"><i class="' . $STATUS_ICONS[$i] . '"></i></a></div>';
    }
    return $output;
}

function statusGetAll($did) {
    global $db, $STATUS_COUNT, $STATUS_ICONS;
    $output = "";

    $status = getStatus($did);
    for ($i = 0; $i < count($status); $i++) {
        $output .= '<div style="float: left; margin: 5px;"><i class="' . $STATUS_ICONS[$status[$i][0]] . '"></i></div>';
    }
    return $output;
}

if (@$_GET["sa"] != null) {
    require_once dirname(__FILE__).'/../config.php';
    require_once dirname(__FILE__).'/protect.php'; 
}
if (@$_GET["sa"] == 1) {
    $selected = $_POST["selected"];
    $status = $_POST["status"];

    if (is_array($selected)) {
        for ($i = 0; $i < count($selected); $i++) {
            insertStatus($selected[$i], $status);
        }
    } else {
        if (array_search($status, $STATUS_ALLOWED) !== false) {
            insertStatus($selected, $status);
        } else {
            die("3");
        }
    }
    die("1");
}
if (@$_GET["sa"] == 2) {
    $selected = $_POST["selected"];
    $status = $_POST["status"];

    if (is_array($selected)) {
        for ($i = 0; $i < count($selected); $i++) {
            deleteStatus($selected[$i], $status);
        }
    } else {
        if (array_search($status, $STATUS_ALLOWED) !== false) {
            deleteStatus($selected, $status);
        } else {
            die("3");
        }
    }
    die("2");
}