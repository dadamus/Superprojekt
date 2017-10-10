<?php
/**
 * Created by PhpStorm.
 * User: dawidadamus
 * Date: 07.10.2017
 * Time: 10:18
 */

require_once dirname(__DIR__) . '/mainController.php';
require_once dirname(__DIR__) . '/wz/service/WZService.php';

/**
 * Class WZController
 */
class WZController extends mainController
{
    /** @var  WZService */
    private $wzService;

    /**
     * WZController constructor.
     */
    public function __construct()
    {
        $this->wzService = new WZService();
        $this->setViewPath(dirname(__FILE__) . '/view/');
    }

    /**
     * @param int $orderId
     * @return string
     */
    public function indexAction(int $orderId): string
    {
        $orderData = $this->wzService->getOrderInfo($orderId);
        $clientData = $this->wzService->getClientData($orderData['cid']);
        $orderItems = $this->wzService->getOrderItems($orderId);

        return $this->render('wzCreator.php', [
            'clientData' => $clientData,
            'orderItems' => $orderItems
        ]);
    }
}