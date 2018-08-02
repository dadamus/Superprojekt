<?php

/**
 * Created by PhpStorm.
 * User: dawid
 * Date: 10.07.2017
 * Time: 17:35
 */

include __DIR__ . "/plateMultiPart/directoryViewController.php";
include __DIR__ . "/plateMultiPart/createMPWController.php";
include __DIR__ . "/plateMultiPart/plateMultiPartController.php";

$action = @$_GET["action"];
if (!is_null($action)) {
    if (session_status() == PHP_SESSION_NONE) {
        require_once dirname(__DIR__) . "/protect.php";
    }
    require_once dirname(__DIR__) . "/../config.php";
}

$directoryViewController = new directoryViewController();
$createMPWController = new createMPWController();
$plateMultiPartController = new plateMultiPartController();

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

    case "getMaterialThickness": //Thickness for material
        echo json_encode($createMPWController->getMaterialThickness($_POST['material_id']));
        break;

    case 'getMaterialLaser':
        echo json_encode($createMPWController->getMaterialLaser($_POST['material'], (float)$_POST['thickness']));
        break;

    case "addMPW": //Create MPW
        echo $createMPWController->addMpw($_POST, $_POST["mpw_directory"]);
        break;

    case "viewMainCard": //Karta glowna wyceny
        $programId = 0;
        $directoryId = 0;

        if (isset($_GET["directory_id"])) {
            $directoryId = intval($_GET["directory_id"]);
        }
        if (isset($_GET["program"])) {
            $programId = intval($_GET["program"]);
        }

        echo $plateMultiPartController->viewMainCard($directoryId, $programId);
        break;

    case "viewDetailCard": // Karta detalu
        $directoryId = 0;
        $detailId = 0;

        if (isset($_GET["directory_id"])) {
            $directoryId = $_GET["directory_id"];
        }

        if (isset($_GET["detail_id"])) {
            $detailId = $_GET["detail_id"];
        }

        echo $plateMultiPartController->viewDetailCard($directoryId, $detailId);
        break;
    case "viewProgramCard": //Karta programu
        $directoryId = 0;
        $programId = 0;

        if (isset($_GET["directory_id"])) {
            $directoryId = $_GET["directory_id"];
        }

        if (isset($_GET["program_id"])) {
            $programId = $_GET["program_id"];
        }

        echo $plateMultiPartController->viewProgramCard($directoryId, $programId);
        break;
    case "changeDesigner": //Zmiana projektanta
        $directoryId = $_GET["dir"];
        $userId = $_GET["user"];

        echo $plateMultiPartController->changeDesigner($directoryId, $userId);
        break;

    case "block": //Blokujemy przed edycja
        $directoryId = $_GET["dir"];

        $plateMultiPartController->block($directoryId);
        header("Location: /plateMulti/$directoryId/");
        break;
    case "accept": //Akcpetujemy
        $directoryId = $_GET["dir"];

        $plateMultiPartController->accept($directoryId);
        header("Location: /plateMulti/$directoryId/");
        break;
    case "cancel": //Anulujemy
        $directoryId = $_GET["dir"];

        $plateMultiPartController->cancel($directoryId);
        header("Location: /plateMulti/$directoryId/");
        break;
    case "duplicate": //Kopiujemy
        $directoryId = $_GET["dir"];
        $newDirId = $plateMultiPartController->duplicate($directoryId);

        header("Location: /plateMulti/$newDirId/");
        break;
    case 'edit': //Edytujemy
        $plateMultiPartController->edit($_POST);
        break;
}