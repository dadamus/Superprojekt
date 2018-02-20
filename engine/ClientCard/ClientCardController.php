<?php
/**
 * Created by PhpStorm.
 * User: dawidadamus
 * Date: 03.09.2017
 * Time: 15:52
 */

require_once dirname(__FILE__) . "/../mainController.php";

/**
 * Class ClientCardController
 */
class ClientCardController extends mainController
{
    /**
     * ClientCardController constructor.
     */
    public function __construct()
    {
        $this->setViewPath(dirname(__FILE__) . "/view/clientCard/");
    }

    /**
     * @param int $clientId
     * @return string
     */
    public function viewClientCardAction(int $clientId): string
    {
        global $db;

        $clientDataQuery = $db->prepare("
            SELECT *
            FROM clients c
            WHERE
            c.id = :cid
        ");
        $clientDataQuery->bindValue(":cid", $clientId, PDO::PARAM_INT);
        $clientDataQuery->execute();

        $ticketsQuery = $db->prepare("
            SELECT t.*, a.name as user_name
            FROM tickets t 
            LEFT JOIN accounts a ON a.id = t.created_by
            WHERE t.client_id = :cid
        ");
        $ticketsQuery->bindValue(":cid", $clientId, PDO::PARAM_INT);
        $ticketsQuery->execute();

        return $this->render("clientCardView.php", [
            "data" => $clientDataQuery->fetch(PDO::FETCH_ASSOC),
            "tickets" => $ticketsQuery->fetchAll(PDO::FETCH_ASSOC),
            "modal" => $this->render("addTicketModalView.php", [
                "clientId" => $clientId
            ])
        ]);
    }

    /**
     * @param int $ticketId
     * @return string
     */
    public function getTicketModalAction(int $ticketId): string
    {
        global $db;

        $ticketQuery = $db->prepare("SELECT * FROM tickets WHERE id = :id");
        $ticketQuery->bindValue(":id", $ticketId, PDO::PARAM_INT);
        $ticketQuery->execute();

        $ticketData = $ticketQuery->fetch(PDO::FETCH_ASSOC);

        return $this->render("addTicketModalView.php", [
            "ticketData" => $ticketData,
            "mode" => "edit"
        ]);
    }

    /**
     * @param int $ticketId
     * @param array $data
     */
    public function saveTicketAction(int $ticketId, array $data)
    {
        $SqlBuilder = new sqlBuilder(sqlBuilder::UPDATE, "tickets");
        $SqlBuilder->addCondition("id = " . $ticketId);
        if (isset($data["realization-date-checkbox"])) {
            $SqlBuilder->bindValue("deadline", $data["realization-date"], PDO::PARAM_STR);
            $SqlBuilder->bindValue("deadline_on", (strtolower($data["realization-date-checkbox"]) == "on" ? 1 : 0), PDO::PARAM_INT);
        }
        $SqlBuilder->bindValue("state", "oczekuje", PDO::PARAM_STR);
        $SqlBuilder->bindValue("priority", $data["priority"], PDO::PARAM_INT);
        $SqlBuilder->flush();


    }

    /**
     * @param int $clientId
     * @param array $data
     * @return string
     */
    public function addTicketAction(int $clientId, array $data): string
    {
        global $db;

        $newTicketIdQuery = $db->query("SELECT COUNT(*) as ticketId FROM tickets");
        $newTicketIdData = $newTicketIdQuery->fetch();

        //Zamowienie
        $OrderSqlBuilder = new sqlBuilder(sqlBuilder::INSERT, "order");
        $OrderSqlBuilder->bindValue("cid", $clientId, PDO::PARAM_INT);
        $OrderSqlBuilder->bindValue("on", "ZAM #" . ($newTicketIdData["ticketId"] + 1), PDO::PARAM_STR);
        $OrderSqlBuilder->bindValue("des", "by ticket", PDO::PARAM_STR);
        $OrderSqlBuilder->bindValue("status", 1, PDO::PARAM_INT);
        $OrderSqlBuilder->bindValue("date", date("Y-m-d"), PDO::PARAM_STR);
        $OrderSqlBuilder->bindValue("cdate", date("Y-m-d H:i:s"), PDO::PARAM_STR);
        $OrderSqlBuilder->flush();

        $orderId = $db->lastInsertId();

        $SqlBuilder = new sqlBuilder(sqlBuilder::INSERT, "tickets");
        $SqlBuilder->bindValue("client_id", $clientId, PDO::PARAM_STR);
        $SqlBuilder->bindValue("order_id", $orderId, PDO::PARAM_STR);
        if (isset($data["realization-date-checkbox"])) {
            $SqlBuilder->bindValue("deadline", $data["realization-date"], PDO::PARAM_STR);
            $SqlBuilder->bindValue("deadline_on", (strtolower($data["realization-date-checkbox"]) == "on" ? 1 : 0), PDO::PARAM_INT);
        }
        $SqlBuilder->bindValue("state", "oczekuje", PDO::PARAM_STR);
        $SqlBuilder->bindValue("priority", $data["priority"], PDO::PARAM_INT);
        $SqlBuilder->bindValue("created_at", date("Y-m-d H:i:s"), PDO::PARAM_STR);
        $SqlBuilder->bindValue("created_by", $_SESSION["login"], PDO::PARAM_STR);
        $SqlBuilder->flush();

        $ticketId = $db->lastInsertId();

        return $ticketId;
    }
}