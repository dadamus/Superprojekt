<?php
/**
 * Created by PhpStorm.
 * User: dawidadamus
 * Date: 08.08.2017
 * Time: 23:13
 */

require_once dirname(__FILE__) . "/ProgramCardPartData.php";
require_once dirname(__FILE__) . "/ImgFrame.php";

class ProgramData
{
    /** @var  int */
    private $id;

    /** @var  string */
    private $SheetName;

    /** @var  int */
    private $UsedSheetNum;

    /** @var  ProgramCardPartData[] */
    private $Parts;

    /** @var  MaterialData */
    private $Material;

    /** @var  int */
    private $SheetCount;

    /** @var  int */
    private $ImageId;

    /** @var  ImgFrame */
    private $frame;

    /** @var  float */
    private $surfValue = 0;

    /** @var  float */
    private $PrgMinPrice;

    /** @var  float */
    private $PriceFactor;

    /** @var  int */
    private $PrgOTime;

    /** @var  float */
    private $PrgOPrice;

    /** @var  float */
    private $PrgOValue;

    /** @var  int */
    private $DetAllTimeC = 0;

    /** @var int[] */
    private $DetailAllCount = [];

    /**
     * ProgramData constructor.
     */
    public function __construct()
    {
        global $db;
        $searchQuery = $db->query("
            SELECT *
            FROM settings
        ");

        while($row = $searchQuery->fetch()) {
            switch($row["name"]) {
                case "otime":
                    $this->setPrgOTime(globalTools::calculate_second($row["value"]));
                    break;
                case "ocost":
                    $this->setPrgOPrice($row["value"]);
                    break;
                case "p_factor":
                    $this->setPriceFactor(floatval($row["value"]));
                    break;
            }
        }

        $this->setPrgOValue($this->getPrgOTime() * $this->getPrgOPrice());
    }

    /**
     * @param int $detailId
     * @return int
     */
    public function getDetailAllCount(int $detailId): int
    {
        return $this->DetailAllCount[$detailId];
    }

    public function addDetailAllTCount(int $detailId, int $count)
    {
        if (isset($this->DetailAllCount[$detailId])) {
            $this->DetailAllCount[$detailId] += $count;
        } else {
            $this->DetailAllCount[$detailId] = $count;
        }
    }

    /**
     * @return float
     */
    public function getPriceFactor(): float
    {
        return $this->PriceFactor;
    }

    /**
     * @param float $PriceFactor
     */
    public function setPriceFactor(float $PriceFactor)
    {
        $this->PriceFactor = $PriceFactor;
    }

    /**
     * @return int
     */
    public function getDetAllTimeC(): int
    {
        return $this->DetAllTimeC;
    }

    /**
     * @param int $DetAllTimeC
     */
    public function setDetAllTimeC(int $DetAllTimeC)
    {
        $this->DetAllTimeC = $DetAllTimeC;
    }

    /**
     * @return int
     */
    public function getPrgOTime(): int
    {
        return $this->PrgOTime;
    }

    /**
     * @param int $PrgOTime
     */
    public function setPrgOTime(int $PrgOTime)
    {
        $this->PrgOTime = $PrgOTime;
    }

    /**
     * @return float
     */
    public function getPrgOPrice(): float
    {
        return $this->PrgOPrice;
    }

    /**
     * @param float $PrgOPrice
     */
    public function setPrgOPrice(float $PrgOPrice)
    {
        $this->PrgOPrice = $PrgOPrice;
    }

    /**
     * @return float
     */
    public function getPrgOValue(): float
    {
        return $this->PrgOValue;
    }

    /**
     * @param float $PrgOValue
     */
    public function setPrgOValue(float $PrgOValue)
    {
        $this->PrgOValue = $PrgOValue;
    }

    /**
     * @return float
     */
    public function getPrgMinPrice(): float
    {
        return $this->PrgMinPrice;
    }

    /**
     * @param float $PrgMinPrice
     */
    public function setPrgMinPrice(float $PrgMinPrice)
    {
        $this->PrgMinPrice = $PrgMinPrice;
    }

    /**
     * @param array $data
     */
    public function create($data)
    {
        $this->setSheetName($data["SheetName"]);
        $this->setUsedSheetNum($data["UsedSheetNum"]);
    }

    /**
     * @param int $programId
     * @throws Exception
     */
    public function getById(int $programId)
    {
        global $db;

        $searchQuery = $db->prepare("
            SELECT *
            FROM plate_multiPartPrograms
            WHERE id = :id
        ");
        $searchQuery->bindValue(":id", $programId, PDO::PARAM_INT);
        $searchQuery->execute();
        $data = $searchQuery->fetch();

        if ($data === false) {
            throw new Exception("Brak programu o id: " . $programId);
        }

        $this->create($data);
        $this->setId($programId);

        $material = new MaterialData();
        $material->getByMaterialId($data["materialId"]);
        $this->setMaterial($material);
        $this->getImageByProgramId($programId);
    }

    /**
     * @param int $programId
     */
    private function getImageByProgramId(int $programId) {
        $frame = new ImgFrame();
        $frame->getDataByProgramId($programId);
        $this->setFrame($frame);
    }

    public function SaveData()
    {
        global $db;
        $materialId = $this->getMaterial()->getId();
        $sheetName = $this->getSheetName();

        $checkQuery = $db->prepare("SELECT id FROM plate_multiPartPrograms WHERE SheetName = :sheetName");
        $checkQuery->bindParam(":sheetName", $sheetName, PDO::PARAM_STR);
        $checkQuery->execute();
        $checkResponse = $checkQuery->fetch();

        $saveQuery = new sqlBuilder(sqlBuilder::INSERT, "plate_multiPartPrograms");
        $programDbId = false;
        if ($checkResponse !== false)
        {
            $saveQuery = new sqlBuilder(sqlBuilder::UPDATE, "plate_multiPartPrograms");
            $saveQuery->addCondition("SheetName = '" . $sheetName . "'");
            $programDbId = $checkResponse["id"];
        } else {
            $saveQuery->bindValue("CreateDate", date("Y-m-d H:i:s"), PDO::PARAM_STR);
        }

        $usedSheetNum = $this->getUsedSheetNum();
        $saveQuery->bindValue("SheetName", $sheetName, PDO::PARAM_STR);
        $saveQuery->bindValue("UsedSheetNum", $usedSheetNum, PDO::PARAM_STR);
        $saveQuery->bindValue("materialId", $materialId, PDO::PARAM_INT);
        $saveQuery->flush();

        if ($programDbId === false) {
            $programDbId = $db->lastInsertId();
        }

        //Robimy ramke
        $this->createFrame($programDbId);

        //Zapisujemy party
        foreach ($this->getParts() as $part) {
            $part->SaveData($programDbId);
        }
    }

    /**
     * @param int $programDbId
     */
    public function createFrame(int $programDbId)
    {
        $insertQuery = new sqlBuilder(sqlBuilder::INSERT, "plate_costingFrame");
        $insertQuery->bindValue("imgId", $this->getImageId(), PDO::PARAM_INT);
        $insertQuery->bindValue("type", "multiPartCosting", PDO::PARAM_STR);
        $insertQuery->bindValue("programId", $programDbId, PDO::PARAM_INT);
        $insertQuery->flush();
    }

    /**
     * @return float
     */
    public function getSurfValue(): float
    {
        return $this->surfValue;
    }

    /**
     * @param float $surfValue
     */
    public function setSurfValue(float $surfValue)
    {
        $this->surfValue = $surfValue;
    }

    /**
     * @return int
     */
    public function getId(): int
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
     * @return ImgFrame
     */
    public function getFrame(): ImgFrame
    {
        return $this->frame;
    }

    /**
     * @param ImgFrame $frame
     */
    public function setFrame(ImgFrame $frame)
    {
        $this->frame = $frame;
    }

    /**
     * @return int
     */
    public function getImageId(): int
    {
        return $this->ImageId;
    }

    /**
     * @param int $ImageId
     */
    public function setImageId(int $ImageId)
    {
        $this->ImageId = $ImageId;
    }

    /**
     * @return int
     */
    public function getSheetCount(): int
    {
        return $this->SheetCount;
    }

    /**
     * @param int $SheetCount
     */
    public function setSheetCount(int $SheetCount)
    {
        $this->SheetCount = $SheetCount;
    }

    /**
     * @return MaterialData
     */
    public function getMaterial(): MaterialData
    {
        return $this->Material;
    }

    /**
     * @param MaterialData $Material
     */
    public function setMaterial(MaterialData $Material)
    {
        $this->Material = $Material;
    }

    /**
     * @return ProgramCardPartData[]
     */
    public function getParts(): array
    {
        return $this->Parts;
    }

    /**
     * @param ProgramCardPartData $part
     */
    public function addPart(ProgramCardPartData $part)
    {
        $this->Parts[] = $part;
    }

    /**
     * @param ProgramCardPartData[] $parts
     */
    public function setParts(array $parts)
    {
        $this->Parts = $parts;
    }

    /**
     * @return string
     */
    public function getSheetName(): string
    {
        return $this->SheetName;
    }

    /**
     * @param string $SheetName
     */
    public function setSheetName(string $SheetName)
    {
        $this->SheetName = $SheetName;
    }

    /**
     * @return int
     */
    public function getUsedSheetNum(): int
    {
        return $this->UsedSheetNum;
    }

    /**
     * @param int $UsedSheetNum
     */
    public function setUsedSheetNum(int $UsedSheetNum)
    {
        $this->UsedSheetNum = $UsedSheetNum;
    }

    /**
     * Najwazniejszy event liczmy wszystkie dane
     * @param float $remnant_factor
     */
    public function Calculate(float $remnant_factor)
    {
        $frame = $this->getFrame();
        $material = $this->getMaterial();

        /*
         * RAMKA
         */
        $frame->setWeight(
            $frame->getValue() * $material->getThickness() * $material->getDensity() / 1000
        );
        $frame->setAreaPrice(
            $frame->getValue() * $material->getPrgSheetPriceMm()
        );
        $frame->setRmntVal(
            $frame->getWeight() * $material->getWaste()
        );
        $frame->setPrice(
            $frame->getAreaPrice() - $frame->getRmntVal()
        );

        /*
         * Detale
         */
        foreach ($this->getParts() as $part)
        {
            $part->setRectangleAreaRectAll(
                $part->getRectangleArea() * $part->getPartCount()
            );
            $part->setRectangleAreaRectW(
                $part->getRectangleAreaW() * $part->getPartCount()
            );
            $part->setRectangleAreaRectTrash(
                $part->getRectangleAreaRectAll() - $part->getRectangleAreaRectW()
            );
            $part->setRectangleAreaRectVal(
                $part->getRectangleAreaRectAll() * $material->getPrgSheetPriceMm()
            );
            $part->setRectangleAreaRectWeight(
                $part->getRectangleAreaRectAll() * $material->getThickness() * $material->getDensity() / 1000
            );
            $part->setRectangleAreaRectWWeight(
                $part->getRectangleAreaRectW() * $material->getThickness() * $material->getDensity() / 1000
            );
            $part->setRectangleAreaTrashWeight(
                $part->getRectangleAreaRectWWeight() - $part->getRectangleAreaRectWeight()
            );
            $part->setRemnantValue(
                $part->getRectangleAreaTrashWeight() * $material->getWaste() * $remnant_factor
            );
            $this->setSurfValue(
                $this->getSurfValue() + $part->getRectangleAreaRectAll()
            );
            $this->setDetAllTimeC(
                $this->getDetAllTimeC() + globalTools::calculate_second($part->getPrgDetAllTime())
            );
            $this->addDetailAllTCount($part->getDetailId(), $part->getPartCount());
        }

        /*
         * Petla detali jeszcze raz zeby obliczyc procenty
         */
        foreach ($this->getParts() as $part)
        {
            $part->setSurfPer(
                $part->getRectangleAreaRectAll() / $this->getSurfValue()
            );
            $part->setRectAllVal(
                $part->getSurfPer() * $frame->getValue()
            );
            $part->setMatValAll(
                $part->getRectangleAreaRectVal() - $part->getRectangleAreaTrashVal() + $part->getRectAllVal()
            );
            $part->setMatVal(
                $part->getMatValAll() / $part->getPartCount()
            );
            $part->setCleanCut(
                globalTools::calculate_second($part->getPrgDetSingleTime()) * $this->PrgMinPrice
            );
            $part->setCutAll(
                $part->getCleanCut() + $this->getPrgOValue()
            );
            $part->setCompleteCut(
                globalTools::calculate_second($part->getPrgDetAllTime()) / $this->getDetAllTimeC() * $part->getCutAll()
            );
            $part->setDetailCut(
                $part->getCompleteCut() / $part->getPartCount()
            );
            $part->setAllSheetQty(
                $part->getPartCount() * $this->getSheetCount()
            );
            $part->setComplAllPrice(
                $part->getCompleteCut() * $this->getSheetCount()
            );
            $part->setLastPrice(
                $part->getDetailCut() + $part->getMatVal()
            );
            $part->setPriceKg(
                $part->getLastPrice() / $part->getWeight() * 1000
            );
        }
    }
}