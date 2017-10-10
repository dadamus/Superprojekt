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
$orderId = $_GET['order_id'];

switch ($action) {
    case 'create':
        echo '1';
        break;
    default:
        echo $wzController->indexAction($orderId);
        break;
}