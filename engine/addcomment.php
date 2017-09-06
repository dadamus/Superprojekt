<?php

require_once dirname(__FILE__) . '/../config.php';
require_once dirname(__FILE__) . '/protect.php';

function getComments($type, $eid) {
    global $db;

    $shoutQuery = $db->prepare("
      SELECT 
      c.*,
      a.name
      FROM 
      comments c
      LEFT JOIN accounts a ON a.id = c.uid
      WHERE 
      c.`type` = :type 
      AND c.eid = :eid
      ORDER BY c.id DESC
    ");
    $shoutQuery->bindValue(":type", $type, PDO::PARAM_STR);
    $shoutQuery->bindValue(":eid", $eid, PDO::PARAM_INT);
    $shoutQuery->execute();
    $qshout = $shoutQuery->fetchAll(PDO::FETCH_ASSOC);

    $content = "";
    foreach ($qshout as $shout) {
        $user = $shout["name"];

        $content .= '<div class="shout"><div class="shout-header"><b>' . $user . '</b>  <div style="float: right">' . $shout["date"] . '</div></div>' . $shout["content"] . '</div>';
    }

    return $content;
}

if (@$_GET["action"] == null) {
    $uid = $_SESSION["login"];
    $type = $_GET["type"];
    $eid = $_GET["eid"];
    $content = $_GET["content"];
    $date = date("Y-m-d H:i:s");

    $newType = "";

    if (intval($type) > 0) {
        switch (intval($type)) {
            case 1:
                $newType = "detailView";
                break;
            case 2:
                $newType = "costing";
                break;
        }
    } else {
        $newType = $type;
    }

    $SqlBuilder = new sqlBuilder(sqlBuilder::INSERT, "comments");
    $SqlBuilder->bindValue("type", $newType, PDO::PARAM_STR);
    $SqlBuilder->bindValue("uid", $uid, PDO::PARAM_INT);
    $SqlBuilder->bindValue("eid", $eid, PDO::PARAM_INT);
    $SqlBuilder->bindValue("content", $content, PDO::PARAM_STR);
    $SqlBuilder->bindValue("date", $date, PDO::PARAM_STR);
    $SqlBuilder->flush();

    die (getComments($type, $eid));
} else {
    $type = $_GET["type"];
    $eid = $_GET["eid"];

    die (getComments($type, $eid));
}