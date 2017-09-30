<?php

require_once dirname(__FILE__) . "/../protect.php";
require_once dirname(__FILE__) . "/../../config.php";
require_once dirname(__FILE__) . "/../imap.php";

$action = @$_GET["action"];
if ($action == 1) { //Imap check messages
    echo "{}";
    die;//todo chwilowo wylaczone jak sie naprawi serwer to odpalic
    $imap = new Imap($IMAP_IP, $IMAP_USER, $IMAP_PASS);

    $response = [];

    $qeld = $db->query("SELECT `value` FROM `settings` WHERE `name` = 'email_last_date'");
    $feld = $qeld->fetch();
    $email_last_date = $feld["value"];

    $content = $imap->getMail($email_last_date);
    foreach ($content as $email) {
        $quid = $db->query("SELECT `id` FROM `email` WHERE `uid` = '" . $email['uid'] . "'");
        if ($e = $quid->fetch()) {
            continue;
        }

        $type = array_search($email["subject"], $imap->subject_types);
        if ($type === false) {
            $type = 404;
        }

        $email_body = $email["content"];

        $message_preg = "Message";
        if ($type == 2 || $type == 4 || $type == 5) {
            $message_preg = "Alarm Information";
        }

        $send_time_pos = strpos($email_body, "Send Time");
        $message_pos = strpos($email_body, $message_preg);
        $program_name_pos = strpos($email_body, "Program name");
        $ael = strpos($email_body, "An executive line");
        $cycle_time_pos = strpos($email_body, "Cycle Time");

        $send_time = str_replace("/", "-", substr($email_body, $send_time_pos + strlen("Send Time : "), strlen("0000/00/00 00:00:00") + 2));
        $message = substr($email_body, $message_pos + strlen($message_preg . " : "), $program_name_pos - $message_pos - strlen($message_preg . " : ") - 1);
        $program_name = substr($email_body, $program_name_pos + strlen("Program name : "), $ael - $program_name_pos - strlen("Program name : ") - 1);
        $cycle_time = 0;
        $done = 0;

        $qpid = $db->query("SELECT `id`, `mpw` FROM `programs` WHERE `name` LIKE '%$program_name%'");
        if ($fpid = $qpid->fetch()) {
            $pid = $fpid["id"];
            $pmpw = $fpid["mpw"];
        } else {
            $pid = 0;
            $pmpw = 0;
        }

        if ($type == 3) {
            $done = 1;
        }

        if ($type == 1) { //Cycle time
            $db->query("UPDATE `programs` SET `status` = '$type' WHERE `id` = '$pid'");
            $cycle_time = trim(preg_replace('/\s\s+/', '', substr($email_body, $cycle_time_pos + strlen("Cycle Time : "), strlen($email_body) - $cycle_time_pos)));
        }

        array_push($response, array("type" => $type, "uid" => $email['uid']));
        $date = date("Y-m-d H:i:s");
        $db->query("INSERT INTO `email` (`uid`, `pid`, `program`, `type`, `send_date`, `cycle_time`, `date`, `waring`, `done`) VALUES ('" . $email['uid'] . "', '$pid', '$program_name', '$type', '$send_time', '$cycle_time', '$date', '$message', '$done')");
    }

    //Search date update
    $d = strtotime('-1 day', strtotime(date("Y-m-d H:i:s")));
    $ndate = date("j F Y", $d);
    $db->query("UPDATE `settings` SET `value` = '$ndate' WHERE `name` = 'email_last_date'");
    die(json_encode($response));
}

$prId = @$_GET["prId"];
if ($prId == null) {
    die("Brak id programu!");
}

$qprogram = $db->prepare("
SELECT 
p.new_cutting_queue_id,
p.name,
i.src as image_src
FROM `programs` p
LEFT JOIN sheet_image i ON i.program_id = p.id
WHERE 
p.id = :prId
");
$qprogram->bindValue(':prId', $prId, PDO::PARAM_INT);
$qprogram->execute();

$program = $qprogram->fetch();

$mpwQuery = $db->prepare('
  SELECT
  pw.SheetCode,
  pw.MaterialName,
  tm.Thickness,
  tm.MaterialTypeName,
  cq.sheet_name,
  qd.LaserMatName
  FROM
  cutting_queue_details qd
  LEFT JOIN cutting_queue cq ON cq.id = qd.cutting_queue_id
  LEFT JOIN plate_warehouse pw ON pw.id = qd.plate_warehouse_id
  LEFT JOIN T_material tm ON tm.MaterialName = pw.MaterialName
  WHERE
  qd.cutting_queue_id = :cuttingQueueId
  LIMIT 1
');
$mpwQuery->bindValue(':cuttingQueueId', $program['new_cutting_queue_id'], PDO::PARAM_INT);
$mpwQuery->execute();

$mpwData = $mpwQuery->fetch();

$programName = str_replace('.', '+', $program['name']);
$image = str_replace('/var/www/html', '', $program['image_src']);
?>

<div class="alert alert-info">
    <div style="float: right;"><a href=""><i style="cursor: pointer;" class="fa fa-external-link"></i></a></div>
    <div style="clear: both;"></div>
</div>
<table class="table table-striped">
    <tbody>
    <tr>
        <td>Nazwa:</td>
        <td><?= $program["name"] ?></td>
    </tr>
    <tr>
        <td>SheetCode:</td>
        <td><?= $mpwData["SheetCode"] ?></td>
    </tr>
    <tr>
        <td>Nazwa materiału:</td>
        <td><?= $mpwData["MaterialTypeName"] ?></td>
    </tr>
    <tr>
        <td>LaserMatName:</td>
        <td><?= $mpwData["LaserMatName"] ?></td>
    </tr>
    <tr>
        <td>Grubość:</td>
        <td><?= $mpwData["Thickness"] ?></td>
    </tr>
    <tr>
        <td>Obrazek:</td>
        <td><img src="<?= $image ?>" width="200px"></td>
    </tr>
    </tbody>
</table>
