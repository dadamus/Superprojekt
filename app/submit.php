<?php
/**
 * Created by PhpStorm.
 * User: dawidadamus
 * Date: 14.11.2017
 * Time: 20:52
 */

require_once __DIR__ . '/config.php';

$id = $_POST['id'];
$action = $_POST['action'];
$quantity = $_POST['quantity'];

switch ($action) {
    case 'download':
        echo downloadAction($id, $quantity);
        break;

    case 'upload':
        echo uploadAction($id, $quantity);
        break;
}

/**
 * @param int $id
 * @param int $quantity
 * @return string
 */
function downloadAction(int $id, int $quantity): string
{
    $db = DBConnector::connect();
    $updateQuery = $db->prepare("UPDATE app_details SET quantity = quantity - :quantity WHERE id = :id");
    $updateQuery->bindValue(':quantity', $quantity, PDO::PARAM_INT);
    $updateQuery->bindValue(':id', $id, PDO::PARAM_INT);
    $updateQuery->execute();

    HistoryService::addHistory(HistoryService::DOWNLOAD_TYPE, $id, $quantity);
    return "Zapisałem!";
}

/**
 * @param int $id
 * @param int $quantity
 * @return string
 */
function uploadAction(int $id, int $quantity): string
{
    $db = DBConnector::connect();
    $updateQuery = $db->prepare("UPDATE app_details SET quantity = quantity + :quantity WHERE id = :id");
    $updateQuery->bindValue(':quantity', $quantity, PDO::PARAM_INT);
    $updateQuery->bindValue(':id', $id, PDO::PARAM_INT);
    $updateQuery->execute();

    HistoryService::addHistory(HistoryService::UPLOAD_TYPE, $id, $quantity);
    return "Zapisałem!";
}