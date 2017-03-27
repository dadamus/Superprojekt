<?php

require_once dirname(__FILE__) . '/../config.php';
require_once dirname(__FILE__) . '/protect.php';

if (@$_GET["action"] == null) {
    $uid = $_SESSION["login"];
    $type = $_GET["type"];
    $eid = $_GET["eid"];
    $content = $_GET["content"];
    $date = date("Y-m-d H:i:s");

    $db->query("INSERT INTO `comments` (`type`, `uid`, `eid`, `content`, `date`) VALUES ('$type', '$uid', '$eid', '$content', '$date')");
} else {
    $type = $_GET["type"];
    $eid = $_GET["eid"];

    $qshout = $db->query("SELECT * FROM `comments` WHERE `type` = '$type' AND `eid` = '$eid' ORDER BY `id` DESC");
    $content = "";
    foreach ($qshout as $shout) {
        $uid = $shout["uid"];
        $uq = $db->query("SELECT `name` FROM `accounts` WHERE `id` = '$uid'");
        $uf = $uq->fetch();
        $user = $uf["name"];

        $content .= '<div class="shout"><div class="shout-header"><b>' . $user . '</b>  <div style="float: right">' . $shout["date"] . '</div></div>' . $shout["content"] . '</div>';
    }
    die($content);
}