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
    default:
        echo $multiPartController->getList();
        break;
}