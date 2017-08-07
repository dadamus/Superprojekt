<?php

/**
 * Created by PhpStorm.
 * User: dawid
 * Date: 05.07.2017
 * Time: 20:48
 */

class  plateSinglePartCostingAttributes
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var bool
     */
    private $a1;
    /**
     * @var float
     */
    private $a1_value;
    /**
     * @var bool
     */
    private $a2;
    /**
     * @var float
     */
    private $a2_value;
    /**
     * @var bool
     */
    private $a3;
    /**
     * @var float
     */
    private $a3_value;
    /**
     * @var bool
     */
    private $a4;
    /**
     * @var float
     */
    private $a4_value;
    /**
     * @var bool
     */
    private $a5;
    /**
     * @var float
     */
    private $a5_value;
    /**
     * @var bool
     */
    private $a6;
    /**
     * @var float
     */
    private $a6_value;
    /**
     * @var bool
     */
    private $a7;

    /**
     * @return array
     */
    public function serialize()
    {
        $data = [];

        for ($i = 1; $i <= 7; $i++) {
            $name = "a" . $i;
            $data[$i]["checked"] = $this->$name;

            $valueName = $name . "_value";
            if (property_exists("plateSinglePartCostingAttributes", $valueName)) {
                $data[$i]["value"] = $this->$valueName;
            }
        }

        return $data;
    }

    /**
     * @param int $detailCount
     * @return float|int
     */
    public function getPrice(int $detailCount)
    {
        $price = 0;

        if ($this->isA1()) {
            $price += $this->getA1Value();
        }

        if ($this->isA2()) {
            $price += $this->getA2Value();
        }

        if ($this->isA3()) {
            $price += $this->getA3Value();
        }

        if ($this->isA4()) {
            $price += $this->getA4Value();
        }

        if ($this->isA5()) {
            $price += $this->getA5Value();
        }

        if ($this->isA6()) {
            $price += $this->getA6Value();
        }

        //echo $price . "|" . $detailCount;die;

        return $price * $detailCount;
    }

    /**
     * @param array $data
     */
    public function setData($data)
    {
        foreach ($data as $key => $value) {
            switch ($key) {
                case 1:
                    $this->setA1($value["active"]);
                    $this->setA1Value($value["price"]);
                    break;
                case 2:
                    $this->setA2($value["active"]);
                    $this->setA2Value($value["price"]);
                    break;
                case 3:
                    $this->setA3($value["active"]);
                    $this->setA3Value($value["price"]);
                    break;
                case 4:
                    $this->setA4($value["active"]);
                    $this->setA4Value($value["price"]);
                    break;
                case 5:
                    $this->setA5($value["active"]);
                    $this->setA5Value($value["price"]);
                    break;
                case 6:
                    $this->setA6($value["active"]);
                    $this->setA6Value($value["price"]);
                    break;
                case 7:
                    $this->setA7($value["active"]);
                    break;
            }
        }
    }

    public function saveAtributes($plate_singlePartCosting)
    {
        global $db;
        $type = "INSERT";
        if ($this->getId() > 0) {
            $type = "UPDATE";
        }

        $sqlBuilder = new sqlBuilder($type, "plate_singlePartCostingAttribute");

        $sqlBuilder->bindValue("plate_singlePartCosting", $plate_singlePartCosting, PDO::PARAM_INT);
        $sqlBuilder->bindValue("a1", $this->isA1(), PDO::PARAM_BOOL);
        $sqlBuilder->bindValue("a2", $this->isA2(), PDO::PARAM_BOOL);
        $sqlBuilder->bindValue("a3", $this->isA3(), PDO::PARAM_BOOL);
        $sqlBuilder->bindValue("a4", $this->isA4(), PDO::PARAM_BOOL);
        $sqlBuilder->bindValue("a5", $this->isA5(), PDO::PARAM_BOOL);
        $sqlBuilder->bindValue("a6", $this->isA6(), PDO::PARAM_BOOL);
        $sqlBuilder->bindValue("a7", $this->isA7(), PDO::PARAM_BOOL);

        $sqlBuilder->bindValue("a1_value", $this->getA1Value(), PDO::PARAM_STR);
        $sqlBuilder->bindValue("a2_value", $this->getA2Value(), PDO::PARAM_STR);
        $sqlBuilder->bindValue("a3_value", $this->getA3Value(), PDO::PARAM_STR);
        $sqlBuilder->bindValue("a4_value", $this->getA4Value(), PDO::PARAM_STR);
        $sqlBuilder->bindValue("a5_value", $this->getA5Value(), PDO::PARAM_STR);
        $sqlBuilder->bindValue("a6_value", $this->getA6Value(), PDO::PARAM_STR);

        if ($type == "UPDATE") {
            $sqlBuilder->addCondition("id = " . $this->getId());
        }

        $sqlBuilder->flush();
    }

    /**
     * @param int $costingId
     * @throws Exception
     */
    public function getFromDb($costingId)
    {
        global $db;
        if ($this->getId() < 1) {
            $selectQuery = $db->prepare("SELECT `id` FROM plate_singlePartCostingAttribute WHERE plate_singlePartCosting = :id LIMIT 1");
            $selectQuery->bindValue(":id", $costingId, PDO::PARAM_INT);
            $selectQuery->execute();

            $responseData = $selectQuery->fetch();
            if ($responseData) {
                $this->setId($responseData["id"]);
            } else {
                throw new \Exception("Brak atrybutow!");
            }
        }

        $sqlBuilder = new sqlBuilder("SELECT", "plate_singlePartCostingAttribute");
        $sqlBuilder->addCondition("id = " . $this->getId());

        $sqlBuilder->addBind('a1');
        $sqlBuilder->addBind('a2');
        $sqlBuilder->addBind('a3');
        $sqlBuilder->addBind('a4');
        $sqlBuilder->addBind('a5');
        $sqlBuilder->addBind('a6');
        $sqlBuilder->addBind('a7');

        $sqlBuilder->addBind('a1_value');
        $sqlBuilder->addBind('a2_value');
        $sqlBuilder->addBind('a3_value');
        $sqlBuilder->addBind('a4_value');
        $sqlBuilder->addBind('a5_value');
        $sqlBuilder->addBind('a6_value');

        $result = $sqlBuilder->getData();
        $attributesData = reset($result);

        if ($attributesData != false) {
            $this->setA1($attributesData["a1"]);
            $this->setA2($attributesData["a2"]);
            $this->setA3($attributesData["a3"]);
            $this->setA4($attributesData["a4"]);
            $this->setA5($attributesData["a5"]);
            $this->setA6($attributesData["a6"]);
            $this->setA7($attributesData["a7"]);

            $this->setA1Value($attributesData["a1_value"]);
            $this->setA2Value($attributesData["a2_value"]);
            $this->setA3Value($attributesData["a3_value"]);
            $this->setA4Value($attributesData["a4_value"]);
            $this->setA5Value($attributesData["a5_value"]);
            $this->setA6Value($attributesData["a6_value"]);
        }
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return bool
     */
    public function isA1(): bool
    {
        return $this->a1;
    }

    /**
     * @param bool $a1
     */
    public function setA1(bool $a1)
    {
        $this->a1 = $a1;
    }

    /**
     * @return float
     */
    public function getA1Value(): float
    {
        return $this->a1_value;
    }

    /**
     * @param float $a1_value
     */
    public function setA1Value(float $a1_value)
    {
        $this->a1_value = $a1_value;
    }

    /**
     * @return bool
     */
    public function isA2(): bool
    {
        return $this->a2;
    }

    /**
     * @param bool $a2
     */
    public function setA2(bool $a2)
    {
        $this->a2 = $a2;
    }

    /**
     * @return float
     */
    public function getA2Value(): float
    {
        return $this->a2_value;
    }

    /**
     * @param float $a2_value
     */
    public function setA2Value(float $a2_value)
    {
        $this->a2_value = $a2_value;
    }

    /**
     * @return bool
     */
    public function isA3(): bool
    {
        return $this->a3;
    }

    /**
     * @param bool $a3
     */
    public function setA3(bool $a3)
    {
        $this->a3 = $a3;
    }

    /**
     * @return float
     */
    public function getA3Value(): float
    {
        return $this->a3_value;
    }

    /**
     * @param float $a3_value
     */
    public function setA3Value(float $a3_value)
    {
        $this->a3_value = $a3_value;
    }

    /**
     * @return bool
     */
    public function isA4(): bool
    {
        return $this->a4;
    }

    /**
     * @param bool $a4
     */
    public function setA4(bool $a4)
    {
        $this->a4 = $a4;
    }

    /**
     * @return float
     */
    public function getA4Value(): float
    {
        return $this->a4_value;
    }

    /**
     * @param float $a4_value
     */
    public function setA4Value(float $a4_value)
    {
        $this->a4_value = $a4_value;
    }

    /**
     * @return bool
     */
    public function isA5(): bool
    {
        return $this->a5;
    }

    /**
     * @param bool $a5
     */
    public function setA5(bool $a5)
    {
        $this->a5 = $a5;
    }

    /**
     * @return float
     */
    public function getA5Value(): float
    {
        return $this->a5_value;
    }

    /**
     * @param float $a5_value
     */
    public function setA5Value(float $a5_value)
    {
        $this->a5_value = $a5_value;
    }

    /**
     * @return bool
     */
    public function isA6(): bool
    {
        return $this->a6;
    }

    /**
     * @param bool $a6
     */
    public function setA6(bool $a6)
    {
        $this->a6 = $a6;
    }

    /**
     * @return float
     */
    public function getA6Value(): float
    {
        return $this->a6_value;
    }

    /**
     * @param float $a6_value
     */
    public function setA6Value(float $a6_value)
    {
        $this->a6_value = $a6_value;
    }

    /**
     * @return bool
     */
    public function isA7(): bool
    {
        return $this->a7;
    }

    /**
     * @param bool $a7
     */
    public function setA7(bool $a7)
    {
        $this->a7 = $a7;
    }
}