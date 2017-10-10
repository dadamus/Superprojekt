<?php

require_once dirname(__FILE__) . "/../protect.php";
require_once dirname(__FILE__) . "/../../config.php";
require_once dirname(__FILE__) . "/../imap.php";

$listStatus = [
    0 => 'Oczekuje',
    1 => 'w trakcje',
    2 => 'Do potwierdzenia',
    3 => 'Wycięto',
    4 => 'Wstrzymany',
    5 => 'Anulowany',
    6 => 'Nie rozpoznany',
];

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
} else if ($action == 2) { //Zmiana statusu z listy
    $listItemQuery = $db->prepare('
        SELECT
        d.id as queue_detail_id,
        d.qantity,
        d.cutting,
        l.state,
        l.id as list_id,
        det.src
        FROM
        cutting_queue_details d
        LEFT JOIN oitems oitems ON oitems.id = d.oitem_id
        LEFT JOIN details det ON det.id = oitems.did
        LEFT JOIN cutting_queue_list l ON l.id = d.cutting_queue_list_id
        WHERE
        l.lp = :lp
        AND l.cutting_queue_id = :cqid
    ');
    $listItemQuery->bindValue(':lp', $_GET['lp'], PDO::PARAM_INT);
    $listItemQuery->bindValue(':cqid', $_GET['p'], PDO::PARAM_INT);
    $listItemQuery->execute();

    $listItems = $listItemQuery->fetchAll(PDO::FETCH_ASSOC);
    include dirname(__FILE__) . '/programStatusModal.php';
    die;
} else if ($action == 3) { //Zapis zmiany statusu
    $newState = $_POST['state'];
    $listId = $_POST['list-id'];
    $detailCount = [];

    $checkOldState = $db->prepare('
        SELECT 
        state,
        cutting_queue_id
        FROM
        cutting_queue_list
        WHERE 
        id = :listId
    ');
    $checkOldState->bindValue(':listId', $listId, PDO::PARAM_INT);
    $checkOldState->execute();

    $oldStateData = $checkOldState->fetch();
    $oldState = $oldStateData['state'];

    $details = explode(',', $_POST['details']);
    foreach ($details as $detailId) {
        $oitemDataQuery = $db->prepare('
            SELECT
            d.oitem_id,
            o.dct,
            o.stored,
            d.cutting
            FROM 
            cutting_queue_details d
            LEFT JOIN oitems o ON o.id = d.oitem_id
            WHERE
            d.id = :qdid
        ');
        $oitemDataQuery->bindValue(':qdid', $detailId, PDO::PARAM_INT);
        $oitemDataQuery->execute();
        $oitemData = $oitemDataQuery->fetch();

        $cutting = $_POST['detail_' . $detailId];
        if ($newState == 2 || $newState == 3) {
            $cuttingUpdate = new sqlBuilder(sqlBuilder::UPDATE, 'cutting_queue_details');
            $cuttingUpdate->addCondition('id = ' . $detailId);
            $cuttingUpdate->bindValue('cutting', $cutting, PDO::PARAM_INT);
            $cuttingUpdate->flush();
        }

        if ($newState == 3) {
            $oitemData['dct'] += $cutting;
            $oitemData['stored'] += $cutting;

            $oitemUpdate = new sqlBuilder(sqlBuilder::UPDATE, 'oitems');
            $oitemUpdate->addCondition('id = ' . $oitemData['oitem_id']);
            $oitemUpdate->bindValue('dct', $oitemData['dct'], PDO::PARAM_INT);
            $oitemUpdate->bindValue('stored', $oitemData['stored'], PDO::PARAM_INT);
            $oitemUpdate->flush();
        }

        if ($oldState == 3) {
            $oitemData['dct'] -= $oitemData['cutting'];
            $oitemData['stored'] -= $oitemData['cutting'];

            $oitemUpdate = new sqlBuilder(sqlBuilder::UPDATE, 'oitems');
            $oitemUpdate->addCondition('id = ' . $oitemData['oitem_id']);
            $oitemUpdate->bindValue('dct', $oitemData['dct'], PDO::PARAM_INT);
            $oitemUpdate->bindValue('stored', $oitemData['stored'], PDO::PARAM_INT);
            $oitemUpdate->flush();
        }
    }

    $stateUpdate = new sqlBuilder(sqlBuilder::UPDATE, 'cutting_queue_list');
    $stateUpdate->bindValue('state', $newState, PDO::PARAM_INT);
    $stateUpdate->addCondition('id = ' . $listId);
    $stateUpdate->flush();

    $checkListQuery = $db->prepare('
      SELECT COUNT(*) as wrongState
      FROM cutting_queue_list
      WHERE
      state not in (5, 3)
      AND cutting_queue_id = :cqid
    ');
    $checkListQuery->bindValue(':cqid', $oldStateData['cutting_queue_id'], PDO::PARAM_INT);
    $checkListQuery->execute();

    $countData = $checkListQuery->fetch();

    $mainListUpdate = new sqlBuilder(sqlBuilder::UPDATE, 'programs');
    $mainListUpdate->addCondition('new_cutting_queue_id = ' . $oldStateData['cutting_queue_id']);

    if ($countData['wrongState'] == 0) {
        $mainListUpdate->bindValue('status', 1, PDO::PARAM_INT);
        $mainListUpdate->flush();
    } else {
        $mainListUpdate->bindValue('status', 0, PDO::PARAM_INT);
        $mainListUpdate->flush();
    }

    die;
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
  cq.id,
  pw.SheetCode,
  pw.MaterialName,
  tm.Thickness,
  tm.MaterialTypeName,
  cq.sheet_name,
  cq.sheet_count,
  qd.LaserMatName
  FROM
  cutting_queue_details qd
  LEFT JOIN cutting_queue_list l ON l.id = qd.cutting_queue_list_id
  LEFT JOIN cutting_queue cq ON cq.id = l.cutting_queue_id
  LEFT JOIN plate_warehouse pw ON pw.id = qd.plate_warehouse_id
  LEFT JOIN T_material tm ON tm.MaterialName = pw.MaterialName
  WHERE
  cq.id = :cuttingQueueId
  LIMIT 1
');
$mpwQuery->bindValue(':cuttingQueueId', $program['new_cutting_queue_id'], PDO::PARAM_INT);
$mpwQuery->execute();

$mpwData = $mpwQuery->fetch();

$listQuery = $db->prepare('
    SELECT
    l.*
    FROM
    cutting_queue_list l
    WHERE
    l.cutting_queue_id = :qid
');
$listQuery->bindValue(':qid', $program['new_cutting_queue_id'], PDO::PARAM_INT);
$listQuery->execute();

$listData = $listQuery->fetchAll(PDO::FETCH_ASSOC);

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
        <td><?= $programName ?></td>
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

<table class="table table-striped">
    <thead>
    <tr>
        <th>Numer</th>
        <th>Status</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($listData as $listItem): ?>
        <tr>
            <td>
                <?= $listItem['lp'] ?>
            </td>
            <td class="list-item-state" data-item-id="<?= $listItem['id'] ?>">
                <?= $listStatus[$listItem['state']] ?>
            </td>
            <td>
                <a href="<?= '/engine/chart/program.php?action=2&p=' . $mpwData['id'] . '&lp=' . $listItem['lp'] ?>"
                   data-toggle="modal" class="ajax-modal">
                    <i class="fa fa-pencil"></i>
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<div id="modal-container"></div>