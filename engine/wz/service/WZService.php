<?php
/**
 * Created by PhpStorm.
 * User: dawidadamus
 * Date: 07.10.2017
 * Time: 17:24
 */

/**
 * Class WZService
 */
class WZService
{
    /**
     * @param int $orderId
     * @return array
     * @throws Exception
     */
    public function getOrderInfo(int $orderId): array
    {
        global $db;

        $orderQuery = $db->prepare('
            SELECT
            *
            FROM
            `order` o 
            WHERE
            o.id = :orderId
        ');
        $orderQuery->bindValue(':orderId', $orderId, PDO::PARAM_INT);
        $orderQuery->execute();

        $orderData = $orderQuery->fetch();

        if ($orderData === false) {
            throw new \Exception("Brak zamówienia o id: " . $orderId);
        }

        return $orderData;
    }

    /**
     * @param int $orderId
     * @return array
     */
    public function getOrderItems(int $orderId): array
    {
        global $db;

        $orderItemsQuery = $db->prepare('
            SELECT
            i.id as oitem_id,
            i.price,
            i.stored,
            i.code,
            d.src
            FROM
            oitems i
            LEFT JOIN details d ON d.id = i.did
            WHERE
            i.oid = :orderId
        ');
        $orderItemsQuery->bindValue(':orderId', $orderId, PDO::PARAM_INT);
        $orderItemsQuery->execute();

        $orderItemsData = $orderItemsQuery->fetchAll(PDO::FETCH_ASSOC);
        return $orderItemsData;
    }

    /**
     * @param int $clientId
     * @return array
     * @throws Exception
     */
    public function getClientData(int $clientId): array
    {
        global $db;

        $clientQuery = $db->prepare('
          SELECT
          nip,
          `name`,
          address
          FROM
          clients
          WHERE
          id = :clientId
        ');
        $clientQuery->bindValue(':clientId', $clientId, PDO::PARAM_INT);
        $clientQuery->execute();

        $clientData = $clientQuery->fetch();

        if ($clientData === false) {
            throw new \Exception("Brak klienta o id: " . $clientId);
        }

        return $clientData;
    }

    public function getAblData()
    {
        global $db;

        $ablQuery = $db->prepare('
            SELECT
            
        ');
    }
}