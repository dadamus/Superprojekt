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

    /** @var  string */
    private $PreTime;

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

    /** @var  float */
    private $CleanCutAll;

    /** @var  float */
    private $CutAll;

    /** @var float  */
    private $rectangleAreaRectVal = 0;

    /** @var float  */
    private $rectangleAreaTrashVal = 0;

    /** @var float */
    private $matValAll = 0;

    /** @var float */
    private $rectangleAreaRectWeight = 0;

    /** @var float */
    private $rectangleAreaRectWWeight = 0;

    /** @var float */
    private $rectangleAreaTrashWeight = 0;

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
                    $this->setPrgOTime($row["value"]);
                    break;
                case "ocost":
                    $this->setPrgOPrice($row["value"]);
                    break;
                case "p_factor":
                    $this->setPriceFactor(floatval($row["value"]));
                    break;
                case "cut":
                    $this->setPrgMinPrice(floatval($row["value"]));
                    break;
            }
        }
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
    public function getCleanCutAll(): float
    {
        return $this->CleanCutAll;
    }

    /**
     * @param float $CleanCutAll
     */
    public function setCleanCutAll(float $CleanCutAll)
    {
        $this->CleanCutAll = $CleanCutAll;
    }

    /**
     * @return string
     */
    public function getPreTime(): string
    {
        return $this->PreTime;
    }

    /**
     * @param string $PreTime
     */
    public function setPreTime(string $PreTime)
    {
        $this->PreTime = $PreTime;
    }

    /**
     * @return float
     */
    public function getCutAll(): float
    {
        return $this->CutAll;
    }

    /**
     * @param float $CutAll
     */
    public function setCutAll(float $CutAll)
    {
        $this->CutAll = $CutAll;
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
        $this->setPreTime($data["PreTime"]);
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
        $this->setSheetCount($data["SheetCount"]);
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
            $this->id = $checkResponse["id"];

        } else {
            $saveQuery->bindValue("CreateDate", date("Y-m-d H:i:s"), PDO::PARAM_STR);
        }

        $usedSheetNum = $this->getUsedSheetNum();
        $sheetCount = $this->getSheetCount();
        $preTime = $this->getPreTime();

        $saveQuery->bindValue("SheetName", $sheetName, PDO::PARAM_STR);
        $saveQuery->bindValue("UsedSheetNum", $usedSheetNum, PDO::PARAM_STR);
        $saveQuery->bindValue("materialId", $materialId, PDO::PARAM_INT);
        $saveQuery->bindValue("SheetCount", $sheetCount, PDO::PARAM_INT);
        $saveQuery->bindValue("PreTime", $preTime, PDO::PARAM_STR);
        $saveQuery->flush();

        if ($programDbId === false) {
            $programDbId = $db->lastInsertId();
            $this->id = $programDbId;
        }

        $this->saveSettings();

        //Robimy ramke
        $this->createFrame($programDbId);

        //Zapisujemy party
        foreach ($this->getParts() as $part) {
            $part->SaveData($programDbId);
        }
    }

    public function saveSettings()
    {
        global $db;
        $checkQuery = $db->prepare("
            SELECT id
            FROM plate_multiPartProgramsSettings
            WHERE
            program_id = :programId
        ");
        $checkQuery->bindValue(":programId", $this->getId(), PDO::PARAM_INT);
        $checkQuery->execute();

        $checkData = $checkQuery->fetch();

        if ($checkData === false) {
            $saveSettingsQuery = new sqlBuilder(sqlBuilder::INSERT,"plate_multiPartProgramsSettings");
        } else {
            $saveSettingsQuery = new sqlBuilder(sqlBuilder::UPDATE,"plate_multiPartProgramsSettings");
            $saveSettingsQuery->addCondition("id = " . $checkData["id"]);
        }

        $saveSettingsQuery->bindValue("program_id", $this->getId(), PDO::PARAM_INT);
        $saveSettingsQuery->bindValue("o_time", $this->getPrgOTime(), PDO::PARAM_INT);
        $saveSettingsQuery->bindValue("mat_price", $this->getMaterial()->getPrgSheetPrice(), PDO::PARAM_STR);
        $saveSettingsQuery->bindValue("prg_min_price", $this->getPrgMinPrice(), PDO::PARAM_STR);
        $saveSettingsQuery->flush();
    }

    public function getSettings()
    {
        global $db;
        $checkQuery = $db->prepare("
            SELECT *
            FROM plate_multiPartProgramsSettings
            WHERE
            program_id = :programId
        ");
        $checkQuery->bindValue(":programId", $this->getId(), PDO::PARAM_INT);
        $checkQuery->execute();

        $checkData = $checkQuery->fetch();

        if ($checkData === false) {
            return false;
        }

        $this->setPrgOTime($checkData["o_time"]);
        $this->getMaterial()->setPrgSheetPrice($checkData["mat_price"]);
        $this->setPrgMinPrice($checkData["prg_min_price"]);
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

        $this->getSettings();

        if (isset($_POST["program_id"])) {
            if ($_POST["program_id"] == $this->getId()) {
                $this->setPrgOTime(globalTools::calculate_second($_POST["oTime"]) / 60);
                $this->setPrgMinPrice($_POST["prgMinPrice"]);
                $material->setPrgSheetPrice($_POST["prgSheetPrice"]);
            }
        }


        $material->setPrgSheetPriceMm(
            $material->getPrgSheetPrice() / $material->getPrgSheetSur()
        );
        $material->setPrgSheetPriceKg(
            $material->getPrgSheetPrice() / $material->getPrgSheetAllWeight()
        );
        $this->setPrgOValue($this->getPrgOTime() * $this->getPrgOPrice());

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
                    $part->getRectangleAreaRectWeight() - $part->getRectangleAreaRectWWeight()
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

        //Dla calego programu
        $this->setCleanCutAll(
            globalTools::calculate_second($this->getPreTime()) / 60 * $this->PrgMinPrice
        );
        $this->setCutAll(
            $this->getCleanCutAll() + $this->getPrgOValue()
        );

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
            $part->setRectangleAreaTrashVal(
                $part->getRectangleAreaTrashWeight() * $material->getWaste() * $remnant_factor
            );
            $part->setMatValAll(
                $part->getRectangleAreaRectVal() - $part->getRectangleAreaTrashVal() + $frame->getPrice()
            );
            $part->setMatVal(
                $part->getMatValAll() / $part->getPartCount()
            );
            $part->setCompleteCut(
                globalTools::calculate_second($part->getPrgDetAllTime()) / $this->getDetAllTimeC() * $this->getCutAll()
            );
            $part->setDetailCut(
                $part->getCompleteCut() / $this->getSheetCount() / $part->getPartCount()
            );
            $part->setAllSheetQty(
                $part->getPartCount() * $this->getSheetCount()
            );
            $part->setComplAllPrice(
                $part->getCompleteCut() / $this->getSheetCount()
            );
            $part->setLastPrice(
                $part->getDetailCut() + $part->getMatVal()
            );
            $part->setPriceKg(
                $part->getLastPrice() / $part->getWeight() * 1000
            );

            $this->rectangleAreaRectVal += $part->getRectangleAreaRectVal();
            $this->rectangleAreaTrashVal += $part->getRectangleAreaTrashVal();
            $this->matValAll += $part->getMatValAll();
            $this->rectangleAreaRectWeight += $part->getRectangleAreaRectWeight();
            $this->rectangleAreaRectWWeight += $part->getRectangleAreaRectWWeight();
            $this->rectangleAreaTrashWeight += $part->getRectangleAreaTrashWeight();
        }
    }

    /**
     * @return float
     */
    public function getRectangleAreaRectVal()
    {
        return $this->rectangleAreaRectVal;
    }

    /**
     * @return float
     */
    public function getRectangleAreaTrashVal()
    {
        return $this->rectangleAreaTrashVal;
    }

    /**
     * @return float
     */
    public function getMatValAll()
    {
        return $this->matValAll;
    }

    /**
     * @return float
     */
    public function getRectangleAreaRectWeight()
    {
        return $this->rectangleAreaRectWeight;
    }

    /**
     * @return float
     */
    public function getRectangleAreaRectWWeight()
    {
        return $this->rectangleAreaRectWWeight;
    }

    /**
     * @return float
     */
    public function getRectangleAreaTrashWeight()
    {
        return $this->rectangleAreaTrashWeight;
    }
}