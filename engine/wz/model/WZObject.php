<?php
/**
 * Created by PhpStorm.
 * User: dawidadamus
 * Date: 15.10.2017
 * Time: 09:11
 */

require_once __DIR__ . '/WZItem.php';
require_once __DIR__ . '/WZAddress.php';

/**
 * Class WZObject
 */
class WZObject 
{
    /**
     * @var int
     */
    private $wzId = 0;

    /**
     * @var WZItem[]
     */
    private $items;

    /**
     * @var array
     */
    private $columns = [];

    /**
     * @var WZAddress
     */
    private $sellerAddress;

    /**
     * @var WZAddress
     */
    private $buyerAddress;

    /**
     * @var string
     */
    private $createDate;

    /**
     * @var string
     */
    private $name;

    /**
     * WZObject constructor.
     * @param array $columns
     */
    public function __construct(array $columns = [])
    {
        $this->columns = $columns;
    }

    /**
     * @param int $id
     * @throws Exception
     */
    public function findById(int $id)
    {
        global $db;

        $this->wzId = $id;
        $wzQuery = $db->prepare('SELECT * FROM wz WHERE id = :id');
        $wzQuery->bindValue(':id', $id, PDO::PARAM_INT);
        $wzQuery->execute();

        $wzData = $wzQuery->fetch();
        if ($wzData === false) {
            throw new \Exception("Brak wz o id: " . $id);
        }

        $this->columns = json_decode($wzData['rows'], true);
        $this->createDate = $wzData['create_date'];

        $this->name = $id . '/' . date("m/Y", strtotime($this->createDate));

        $sellerAddress = new WZAddress();
        $sellerAddress->getById($wzData['seller_address_id']);
        $this->sellerAddress = $sellerAddress;

        $buyerAddress = new WZAddress();
        $buyerAddress->getById($wzData['buyer_address_id']);
        $this->buyerAddress = $buyerAddress;

        $itemsQuery = $db->prepare('SELECT * FROM wz_item WHERE wz_id = :wz_id');
        $itemsQuery->bindValue(':wz_id', $id, PDO::PARAM_INT);
        $itemsQuery->execute();

        while($row = $itemsQuery->fetch()) {
            $item = new WZItem($row['oitem_id']);
            $item->createFromDBData($row);
            $this->items[] = $item;
        }
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getCreateDate(): string
    {
        return $this->createDate;
    }

    /**
     * @return int
     */
    public function getWzId(): int
    {
        return $this->wzId;
    }

    /**
     * @return array
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @return WZItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param WZItem $item
     */
    public function addItem(WZItem $item)
    {
        $this->items[] = $item;
    }

    /**
     * @return WZAddress
     */
    public function getSellerAddress(): WZAddress
    {
        return $this->sellerAddress;
    }

    /**
     * @param WZAddress $sellerAddress
     */
    public function setSellerAddress(WZAddress $sellerAddress)
    {
        $this->sellerAddress = $sellerAddress;
    }

    /**
     * @return WZAddress
     */
    public function getBuyerAddress(): WZAddress
    {
        return $this->buyerAddress;
    }

    /**
     * @param WZAddress $buyerAddress
     */
    public function setBuyerAddress(WZAddress $buyerAddress)
    {
        $this->buyerAddress = $buyerAddress;
    }

    public function save()
    {
        global $db;

        $this->sellerAddress->save();
        $this->buyerAddress->save();

        $wzInsert = new sqlBuilder(sqlBuilder::INSERT, 'wz');

        if ($this->getWzId() > 0) {
            $wzInsert = new sqlBuilder(sqlBuilder::UPDATE, 'wz');
            $wzInsert->addCondition('id = ' . $this->getWzId());
        }

        $wzInsert->bindValue('seller_address_id', $this->sellerAddress->getId(), PDO::PARAM_INT);
        $wzInsert->bindValue('buyer_address_id', $this->buyerAddress->getId(), PDO::PARAM_INT);
        $wzInsert->bindValue('rows', json_encode($this->getColumns()), PDO::PARAM_INT);
        $wzInsert->bindValue('create_date', date("Y-m-d H:i:s"), PDO::PARAM_STR);
        $wzInsert->flush();

        if ($this->getWzId() === 0) {
            $this->wzId = $db->lastInsertId();
        }

        foreach ($this->getItems() as $item) {
            $item->save($this->getWzId());
        }
    }
}