<?php
/**
 * Created by PhpStorm.
 * User: dawidadamus
 * Date: 08.08.2017
 * Time: 21:12
 */

class MaterialData
{
    /** @var  string */
    private $SheetCode;

    /** @var  int */
    private $UsedSheetNum;

    /** @var  string */
    private $name;

    /** @var  string */
    private $MatName;

    /** @var  string */
    private $thickness;

    /** @var  string */
    private $SheetSize;

    /** @var  float */
    private $density;

    /** @var  float */
    private $price;

    /** @var  float */
    private $prgSheetPrice;

    /** @var  float */
    private $waste;

    /** @var  int */
    private $id;


    //Main card
    /** @var  int */
    private $time;

    /** @var  ProgramData[] */
    private $programs;

    /**
     * @param array $data
     * @throws Exception
     */
    public function create($data)
    {
        try {
            foreach ($data as $name => $val) {
                $this->$name = $val;
            }
        } catch (\Exception $ex) {
            throw new \Exception('Brak parametru: ' . $ex->getMessage());
        }

        $this->getDbData();
    }

    /**
     * @return int
     */
    public function save(): int
    {
        global $db;

        $saveQuery = new sqlBuilder(sqlBuilder::INSERT, "plate_multiPartCostingMaterial");

        if ($this->id > 0) {
            $saveQuery = new sqlBuilder(sqlBuilder::UPDATE, "plate_multiPartCostingMaterial");
            $saveQuery->addCondition('id = ' .$this->id);
        }

        $saveQuery->bindValue("SheetCode", $this->getSheetCode(), PDO::PARAM_STR);
        $saveQuery->bindValue("UsedSheetNum", $this->getUsedSheetNum(), PDO::PARAM_INT);
        $saveQuery->bindValue("MatName", $this->getMatName(), PDO::PARAM_INT);
        $saveQuery->bindValue("thickness", $this->getThickness(), PDO::PARAM_STR);
        $saveQuery->bindValue("SheetSize", $this->getSheetSize(), PDO::PARAM_STR);
        $saveQuery->bindValue("density", $this->getDensity(), PDO::PARAM_STR);
        $saveQuery->bindValue("price", $this->getPrice(), PDO::PARAM_STR);
        $saveQuery->bindValue("prgSheetPrice", $this->getPrgSheetPrice(), PDO::PARAM_STR);
        $saveQuery->flush();

        if ($this->id == 0) {
            $this->id = $db->lastInsertId();
        }

        return $this->id;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return intval($this->id);
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
     * @param float $prgSheetPrice
     */
    public function setPrgSheetPrice(float $prgSheetPrice)
    {
        $this->prgSheetPrice = $prgSheetPrice;
    }

    /**
     * @return float
     */
    public function getPrgSheetPrice(): float
    {
        return $this->prgSheetPrice;
    }

    /**
     * @return float
     */
    public function getPrgSheetAllWeight(): float
    {
        return $this->getPrgSheetSur() * $this->getThickness() * $this->getDensity() / 1000;
    }

    /**
     * @return float
     */
    public function getPrgSheetPriceKg(): float
    {
        return $this->getPrgSheetPrice() / $this->getPrgSheetAllWeight();
    }

    /**
     * @return float
     */
    public function getPrgSheetPriceMm(): float
    {
        return $this->getPrgSheetPrice()/ $this->getPrgSheetSur();
    }

    /**
     * @return float
     */
    public function getPrgSheetSur(): float
    {
        return $this->getSheetSizeX() * $this->getSheetSizeY();
    }

    /**
     * @return float
     */
    public function calculatePrgSheetPrice()
    {
        $this->setPrgSheetPrice(($this->getSheetSizeX() * $this->getSheetSizeY() * $this->getThickness() * $this->getDensity() / 1000) * $this->getPrice());
        return $this->getPrgSheetPrice();
    }

    /**
     * @throws Exception
     */
    private function getDbData()
    {
        global $db;
        $dataQuery = $db->prepare("
          SELECT m.cubic, m.price, m.waste, m.name
          FROM T_material tm
          LEFT JOIN material m ON m.name = tm.MaterialTypeName
          WHERE tm.MaterialName = :name
        ");
        $dataQuery->bindValue(':name', $this->getMatName(), PDO::PARAM_STR);
        $dataQuery->execute();

        $materialData = $dataQuery->fetch();
        if ($materialData === false) {
            throw new \Exception('Brak materialu: ' . $this->getMatName());
        }

        $this->setDensity(floatval($materialData["cubic"]));
        $this->setPrice(floatval($materialData["price"]));
        $this->setWaste(floatval($materialData["waste"]));
        $this->setName($materialData["name"]);
        $this->calculatePrgSheetPrice();
    }

    /**
     * @param int $materialId
     * @throws Exception
     */
    public function getByMaterialId(int $materialId) {
        global $db;

        $searchQuery = $db->prepare("
            SELECT *
            FROM plate_multiPartCostingMaterial
            WHERE id = :id
        ");
        $searchQuery->bindValue(":id", $materialId, PDO::PARAM_INT);
        $searchQuery->execute();

        $data = $searchQuery->fetch(PDO::FETCH_ASSOC);
        if ($data === false) {
            throw  new Exception("Brak materialu o id: " . $materialId);
        }
        $this->create($data);
    }

    /**
     * @return float
     */
    public function getWaste(): float
    {
        return $this->waste;
    }

    /**
     * @param float $waste
     */
    public function setWaste(float $waste)
    {
        $this->waste = $waste;
    }

    /**
     * @return float
     */
    public function getSheetSizeY(): float
    {
        $size = explode("X",str_replace(' ', '', strtoupper($this->getSheetSize())));
        return floatval($size[1]);
    }

    /**
     * @return float
     */
    public function getSheetSizeX(): float
    {
        $size = explode("X",str_replace(' ', '', strtoupper($this->getSheetSize())));
        return floatval($size[0]);
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
     * @return float
     */
    public function getDensity(): float
    {
        return $this->density;
    }

    /**
     * @param float $density
     */
    public function setDensity(float $density)
    {
        $this->density = $density;
    }

    /**
     * @return string
     */
    public function getSheetCode()
    {
        return $this->SheetCode;
    }

    /**
     * @param string $SheetCode
     */
    public function setSheetCode($SheetCode)
    {
        $this->SheetCode = $SheetCode;
    }

    /**
     * @return int
     */
    public function getUsedSheetNum()
    {
        return $this->UsedSheetNum;
    }

    /**
     * @param int $UsedSheetNum
     */
    public function setUsedSheetNum($UsedSheetNum)
    {
        $this->UsedSheetNum = $UsedSheetNum;
    }

    /**
     * @return string
     */
    public function getMatName()
    {
        return $this->MatName;
    }

    /**
     * @param string $MatName
     */
    public function setMatName($MatName)
    {
        $this->MatName = $MatName;
    }

    /**
     * @return string
     */
    public function getThickness()
    {
        return $this->thickness;
    }

    /**
     * @param string $thickness
     */
    public function setThickness($thickness)
    {
        $this->thickness = $thickness;
    }

    /**
     * @return string
     */
    public function getSheetSize()
    {
        return $this->SheetSize;
    }

    /**
     * @param string $SheetSize
     */
    public function setSheetSize($SheetSize)
    {
        $this->SheetSize = $SheetSize;
    }

    /**
     * @return int
     */
    public function getTime(): int
    {
        return $this->time;
    }

    /**
     * @param string $time
     */
    public function addTime(string $time)
    {
        $this->time += globalTools::calculate_second($time);
    }

    /**
     * @return ProgramData[]
     */
    public function getPrograms(): array
    {
        return $this->programs;
    }

    /**
     * @param ProgramData $program
     */
    public function addProgram(ProgramData $program)
    {
        if (!isset($this->programs[$program->getId()])) {
            $this->programs[$program->getId()] = $program;
            $this->addTime($program->getPreTime());
        }
    }
}