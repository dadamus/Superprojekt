<?php
/**
 * Created by PhpStorm.
 * User: dawidadamus
 * Date: 04.09.2017
 * Time: 22:32
 */

if (session_status() == PHP_SESSION_NONE) {
    require_once dirname(__FILE__) . "/../config.php";
    require_once dirname(__FILE__) . "/protect.php";
}

require_once dirname(__FILE__) . "/ClientCard/ClientCardController.php";

$action = @$_GET["action"];
$clientCardController = new ClientCardController();

switch ($action) {
    case "addNewTicket":
        echo $clientCardController->addTicketAction($_GET["client_id"], $_POST);
        break;

    case "editTicket":
        echo $clientCardController->getTicketModalAction($_GET["ticket_id"]);
        break;

    case "saveTicket":
        echo $clientCardController->saveTicketAction($_GET["ticket_id"], $_POST);
        break;

    default:
        echo $clientCardController->viewClientCardAction($_GET["client_id"]);
        break;
}