<?php
$action = @$_GET["action"];

require_once __DIR__ . '/../config.php';
require __DIR__ . '/operator/OperatorController.php';

$operatorController = new OperatorController();

if ($action == 1) { // Save new queue
    require_once dirname(__FILE__) . '/../config.php';
    require_once dirname(__FILE__) . '/protect.php';

    $p = json_decode(str_replace('\\', '', $_POST["plis"]), true);

    $queue = array();
    for ($i = 0; $i < count($p); $i++) {
        $item = $p[$i]["id"];
        if (array_key_exists($item, $queue) == false) {
            $queue[$item] = $i;
        } else {
            $queue[$item] .= "|" . $i;
        }
    }
    foreach ($queue as $key => $value) {
        $db->query("UPDATE `programs` SET `position` = '$value' WHERE `id` = '$key'");
    }
    echo '1';
} else if ($action == 2) {
    echo $operatorController->programDetailsAction($_GET['prId'], @$_GET['extended']);
} else {
    switch ($list) {
        case 1:
            echo $operatorController->cutListAction();
            break;

        case 2:
            echo $operatorController->historyListAction();
            break;
    }

}

//function getPrograms()
//{
//    global $db;
//    $programs = $db->query("
//      SELECT
//      p.`name`,
//      p.`id`,
//      p.`mpw`,
//      p.`cut`,
//      p.`position`,
//      cq.sheet_count as quantity,
//      cq.parent_synced,
//      p.new_cutting_queue_id
//      FROM `programs` p
//      LEFT JOIN cutting_queue cq ON cq.id = p.new_cutting_queue_id
//      WHERE p.`status` < 1
//      ORDER BY p.`id` DESC
//    ");
//
//    $queue = array();
//    $ooq = array();
//
//    foreach ($programs as $program) {
//        $mpwa = json_decode($program["mpw"], true);
//        $pieces = 0;
//        if (is_array($mpwa)) {
//            if (count($mpwa) > 0) {
//                foreach ($mpwa as $name => $value) {
//                    $pieces += $value;
//                }
//            }
//        }
//
//        // ------ Tutaj parenty
//        $parentQuery = $db->prepare("
//            SELECT COUNT(*) as ile FROM cutting_queue_list WHERE state <> 2 AND cutting_queue_id = :cqid
//        ");
//        $parentQuery->bindParam(":cqid", $program["new_cutting_queue_id"], PDO::PARAM_INT);
//        $parentQuery->execute();
//        $count = $parentQuery->fetch();
//
//        if ($count['ile'] == 0) {
//            continue;
//        }
//
//        // ------ Tutaj update parentu odpadu
//
//        if ($program['parent_synced'] === 0) {
//            //Najpierw detale bo to do nich jest blacha przypisana
//            $detailWasteQuery = $db->prepare("SELECT id, plate_warehouse_id FROM cutting_queue_details WHERE cutting_queue_list_id = :listId");
//            $detailWasteQuery->bindValue(':listId', $program['new_cutting_queue_id'], PDO::PARAM_INT);
//            $detailWasteQuery->execute();
//
//            $detailsCount = 0;
//            $syncedCount = 0;
//            while($row = $detailWasteQuery->fetch())
//            {
//                $details++;
//
//            }
//
//            if ($detailsCount === $syncedCount) {
//                $queueUpdateQuery = $db->prepare("UPDATE cutting_queue SET parent_synced = 1 WHERE id = :queueId");
//                $queueUpdateQuery->bindValue(":queueId", $program['new_cutting_queue_id'], PDO::PARAM_INT);
//                $queueUpdateQuery->execute();
//            }
//        }
//
//        if ($program["quantity"] > 0) {
//            $pieces += $program["quantity"];
//        }
//
//        if ($program["cut"] < 1) {
//            $program["cut"] = 0;
//        }
//
//        $programName = str_replace('.', '+', $program['name']);
//
//        $position = intval($program["position"]);
//        $str2 = '<li class="dd-item dd3-item" data-id="' . $program["id"] . '"><div class="dd-handle dd3-handle"></div><div class="dd3-content">' . $programName . ' <div style="float: right; cursor: pointer;" class="bPinfo">' . $program["cut"] . '/' . $pieces . ' <i class="fa fa-info-circle"></i></div></div></li>';
//
//        if (array_key_exists($position, $queue) == false) {
//            $queue[$position] = $str2;
//        } else {
//            array_push($ooq, $str2);
//        }
//    }
//
//    ksort($queue);
//    foreach ($queue as $row) {
//        echo $row;
//    }
//    foreach ($ooq as $row) {
//        echo $row;
//    }
//}
?>
