<?php
/**
 * Created by PhpStorm.
 * User: dawidadamus
 * Date: 27.09.2017
 * Time: 23:22
 */

include dirname(__FILE__) . '/orderList/OrderListController.php';

$action = @$_GET["action"];
if (!is_null($action)) {
    if (session_status() == PHP_SESSION_NONE) {
        require_once dirname(__FILE__) . "/protect.php";
    }
    require_once dirname(__FILE__) . "/../config.php";
}

$orderListController = new OrderListController();

switch ($action) {
    default:
        echo $orderListController->mainListAction();
        break;
}