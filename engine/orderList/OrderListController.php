<?php
/**
 * Created by PhpStorm.
 * User: dawidadamus
 * Date: 27.09.2017
 * Time: 23:22
 */

require_once dirname(__DIR__) . "/mainController.php";

/**
 * Class OrderListController
 */
class OrderListController extends mainController
{
    /**
     * OrderListController constructor.
     */
    public function __construct()
    {
        $this->setViewPath(dirname(__FILE__) . '/view/');
    }

    public function mainListAction(): string
    {
        global $db;

        $ordersQuery = $db->query('
            SELECT
            t.id,
            t.deadline,
            t.deadline_on,
            t.state,
            t.priority,
            t.created_at,
            c.name as client_name,
            c.id as client_id
            FROM
            tickets t
            LEFT JOIN clients c ON c.id = t.client_id
        ');
        $orders = $ordersQuery->fetchAll(PDO::FETCH_ASSOC);

        return $this->render('mainList.php', [
            'orders' => $orders
        ]);
    }
}