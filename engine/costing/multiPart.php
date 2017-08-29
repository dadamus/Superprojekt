<?php
/**
 * Created by PhpStorm.
 * User: dawidadamus
 * Date: 24.08.2017
 * Time: 17:50
 */

require_once dirname(__FILE__) . "/multiPart/MultiPartController.php";


$action = @$_GET["action"];
if (!is_null($action)) {
    require_once dirname(__DIR__) . "/../config.php";
}

$multiPartController = new MultiPartController();

switch ($action) {
    case "viewPlateCostingCard":
        echo $multiPartController->getPlateCosting($_GET["directory_id"]);
        break;
    case "deleteMpwItem":
        echo $multiPartController->deleteDetail($_POST["dir"], $_POST["mpw"], $_POST["detail"]);
        break;
    default:
        echo $multiPartController->getList();
        break;
}