<?php
/**
 * Created by PhpStorm.
 * User: dawidadamus
 * Date: 07.10.2017
 * Time: 10:18
 */

require_once dirname(__DIR__) . '/mainController.php';
require_once dirname(__DIR__) . '/wz/service/WZService.php';
require_once dirname(__DIR__) . '/wz/model/WZObject.php';

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
        $defaultSeller = $this->wzService->getDefaultSeller();

        return $this->render('wzCreator.php', [
            'clientData' => $clientData,
            'orderItems' => $orderItems,
            'defaultSeller' => $defaultSeller
        ]);
    }

    /**
     * @param array $request
     * @return string
     */
    public function generateAction(array $request): string
    {
        if (!isset($request['col-enabled'])) {
            throw new \Exception('Brak wybranych kolumn!');
        }

        $wz = new WZObject($request['col-enabled']);

        $sellerAddress = new WZAddress();
        $sellerAddress->setName($request['seller_name']);
        $sellerAddress->setAddress1($request['seller_address1']);
        $sellerAddress->setAddress2($request['seller_address2']);
        $sellerAddress->setNip($request['seller_nip']);

        if ($request['seller_default_id'] > 0) {
            $sellerAddress->setId($request['seller_default_id']);
        }

        $buyerAddress = new WZAddress();
        $buyerAddress->setName($request['buyer_name']);
        $buyerAddress->setAddress1($request['buyer_address1']);
        $buyerAddress->setAddress2($request['buyer_address2']);
        $buyerAddress->setNip($request['buyer_nip']);

        $wz->setSellerAddress($sellerAddress);
        $wz->setBuyerAddress($buyerAddress);

        $oitems = $request['oitems'];
        foreach ($oitems as $oitemId) {
            $wzItem = new WZItem($oitemId);
            $wzItem->setCode($request['oitem-code-' . $oitemId]);
            $wzItem->setName($request['oitem-name-' . $oitemId]);
            $wzItem->setPrice($request['oitem-price-' . $oitemId]);
            $wzItem->setQuantity($request['oitem-quantity-' . $oitemId]);
            $wz->addItem($wzItem);
        }

        $wz->save();
        return $wz->getWzId();
    }

    /**
     * @param int $wzId
     * @return string
     */
    public function viewAction(int $wzId): string
    {
        $wz = new WZObject();
        $wz->findById($wzId);
        return $this->render('wzView.php', [
            'wz' => $wz
        ]);
    }
}