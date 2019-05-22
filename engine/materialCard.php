<?php
/**
 * Created by PhpStorm.
 * User: dawidadamus
 * Date: 29.10.2017
 * Time: 17:43
 */

require_once __DIR__ . '/../config.php';

include __DIR__ . '/materialCard/MaterialCardController.php';
include __DIR__ . '/materialCard/MaterialCardLogController.php';

$action = @$_GET["action"];
if ($action !== null) {
    if (session_status() === PHP_SESSION_NONE) {
        require_once __DIR__ . "/protect.php";
    }
    require_once __DIR__ . "/../config.php";
}

$materialCardController = new MaterialCardController();
$materialLogController = new MaterialCardLogController();

switch ($action) {
    case 'release':
        echo $materialCardController->releaseAction($_POST);
        break;

    case 'remnantCheck':
        $checkbox = 0;
        if (isset($_POST['remnant-check'])) {
            $checkbox = $_POST['remnant-check'];
        }

        $materialCardController->remnantCheck($_POST['plate-warehouse-id'], $checkbox, $_POST['remnant-text']);
        break;

    case 'image':
        $sheetCode = $_GET['sheet_code'];
        $generator = new barcode_generator();
        $generator->output_image('svg', 'dmtx', $sheetCode, []);
        break;

    case 'print':
        $sheetCode = $_GET['sheet_code'];
        echo $materialCardController->printAction($sheetCode);
        break;

    case 'log':
        $sheetCode = $_GET['sheet_code'];
        echo $materialLogController->showAction($sheetCode);
        break;

    default:
        $sheetCode = $_GET['sheet_code'];
        echo $materialCardController->indexAction($sheetCode);
        break;
}