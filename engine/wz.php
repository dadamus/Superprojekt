<?php
/**
 * Created by PhpStorm.
 * User: dawidadamus
 * Date: 07.10.2017
 * Time: 10:32
 */

include dirname(__FILE__) . '/wz/WZController.php';

$action = @$_GET["action"];
if (!is_null($action)) {
    if (session_status() == PHP_SESSION_NONE) {
        require_once dirname(__FILE__) . "/protect.php";
    }
    require_once dirname(__FILE__) . "/../config.php";
}

$wzController = new WZController();

/** @var int $orderId */
$orderId = 0;

if (isset($_GET['order_id'])) {
    $orderId = $_GET['order_id'];
}

switch ($action) {
    case 'view':
        echo $wzController->viewAction($_GET['wz_id']);
        break;
    case 'generate':
        echo $wzController->generateAction($_POST);
        break;
    default:
        echo $wzController->indexAction($orderId);
        break;
}