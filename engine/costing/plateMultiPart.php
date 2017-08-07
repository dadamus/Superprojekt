<?php

/**
 * Created by PhpStorm.
 * User: dawid
 * Date: 10.07.2017
 * Time: 17:35
 */

include dirname(__FILE__) . "/plateMultiPart/directoryViewController.php";
include dirname(__FILE__) . "/plateMultiPart/createMPWController.php";

$action = @$_GET["action"];
if (!is_null($action)) {
    require_once dirname(__DIR__) . "/../config.php";
}

$directoryViewController = new directoryViewController();
$createMPWController = new createMPWController();

switch ($action) {
    case "getDirectoryForm": //Get multipart directory form
        echo $directoryViewController->getDirectoryForm();
        break;

    case "getDirectory": //Get multipart directory data
        echo $directoryViewController->getDirectory(@$_GET["filter"]);
        break;

    case "addDirectory": //Add new multipart directory
        echo $directoryViewController->addDirectory($_POST["name"]);
        break;

    case "addMPWForm": //Create mpw form
        echo $createMPWController->addMPWForm($_POST["dir"], $_POST["project_id"], json_decode($_POST["details"], true));
        break;

    case "addMPW": //Create MPW
        echo $createMPWController->addMpw($_POST);
        break;
}