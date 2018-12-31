<?php
/**
 * Created by PhpStorm.
 * User: dawidadamus
 * Date: 29.10.2017
 * Time: 17:43
 */

include __DIR__ . '/materialCard/MaterialCardController.php';

$action = @$_GET["action"];
if ($action !== null) {
    if (session_status() === PHP_SESSION_NONE) {
        require_once __DIR__ . "/protect.php";
    }
    require_once __DIR__ . "/../config.php";
}

$materialCardController = new MaterialCardController();
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

    default:
        $sheetCode = $_GET['sheet_code'];
        echo $materialCardController->indexAction($sheetCode);
        break;
}