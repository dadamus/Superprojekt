<?php

require_once '../config.php';

$path = @$_GET["path"];

if ($path == null) {
    die("Błąd");
}
$date = date("Y-m-d H:i:s");

$epath = explode("/", str_replace("\\\\", "/", $path));

$cid = $epath[2];
$pn = $epath[4];
$name = basename(str_replace("\\\\", "/", $path));

$qProject = $db->query("SELECT `id` FROM `projects` WHERE `nr` = '$pn' AND `cid` = '$cid'");
if ($project = $qProject->fetch()) {
    $pid = $project["id"];

    $sQuery = $db->query("SELECT `id` FROM `details` WHERE `src` = '$name' AND `pid` = '$pid'");
    if ($detail = $sQuery->fetch()) {
        $did = $detail["id"];
        $qmpw = $db->query("SELECT `type` FROM `mpw` WHERE `did` = '$did'");
        foreach ($qmpw as $row) {
            if ($row["type"] >= 7) {
                continue;
                ;
            }
            die("Detal znajduje się juz w bazie!");
        }
    }

    $db->query("INSERT INTO `details` (`pid`, `src`, `date`) VALUES ('$pid', '$name', '$date')");
    $did = $db->lastInsertId();
    insertStatus($did, $STATUS_NEW);
    die("1");
}

/*
  $query = $db->prepare("INSERT INTO `details` (`pid`, `src`, `date`) VALUES ('$pid', '$srcfile', '$date')");
  $query->execute();

  $did = $db->lastInsertId();
  insertStatus($did, $STATUS_NEW);
 */
?>