<?php
/**
 * Created by PhpStorm.
 * User: dawidadamus
 * Date: 15.10.2017
 * Time: 09:08
 */

/**
 * Class WZItem
 */
class WZItem
{
    /**
     * @var int
     */
    private $id = 0;

    /**
     * @var int
     */
    private $oitemId;

    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $name = "";

    /**
     * @var float
     */
    private $price = 0;

    /**
     * @var int
     */
    private $quantity;

    /**
     * WZItem constructor.
     * @param int $oitemId
     */
    public function __construct(int $oitemId)
    {
        $this->oitemId = $oitemId;
    }

    /**
     * @param array $data
     */
    public function createFromDBData(array $data)
    {
        $this->id = $data['id'];
        $this->code = $data['code'];
        $this->name = $data['name'];
        $this->price = $data['price'];
        $this->quantity = $data['quantity'];
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getOitemId(): int
    {
        return $this->oitemId;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode(string $code)
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @param float $price
     */
    public function setPrice(float $price)
    {
        $this->price = $price;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity(int $quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @param int $wzId
     */
    public function save(int $wzId)
    {
        global $db;
        $saveQuery = new sqlBuilder(sqlBuilder::INSERT, 'wz_item');

        if ($this->getId() > 0) {
            $saveQuery = new sqlBuilder(sqlBuilder::UPDATE, 'wz_item');
            $saveQuery->addCondition('id = ' . $this->getId());
        }

        $saveQuery->bindValue('oitem_id', $this->getOitemId(), PDO::PARAM_INT);
        $saveQuery->bindValue('code', $this->getCode(), PDO::PARAM_STR);
        $saveQuery->bindValue('name', $this->getName(), PDO::PARAM_STR);
        $saveQuery->bindValue('price', $this->getPrice(), PDO::PARAM_STR);
        $saveQuery->bindValue('quantity', $this->getQuantity(), PDO::PARAM_INT);
        $saveQuery->bindValue('wz_id', $wzId, PDO::PARAM_INT);
        $saveQuery->flush();

        if ($this->getId() === 0) {
            $this->id = $db->lastInsertId();
        }
    }
}