<?php
/**
 * Created by PhpStorm.
 * User: dawidadamus
 * Date: 08.08.2017
 * Time: 21:19
 */

class ProgramCardPartData
{
    /** @var  int */
    private $id;

    /** @var  int */
    private $PartNo;

    /** @var  string */
    private $PartName;

    /** @var  int */
    private $DetailId;

    /** @var  int */
    private $PartCount;

    /** @var  float */
    private $UnfoldXSize;

    /** @var  float */
    private $UnfoldYSize;

    /** @var  float */
    private $RectangleArea;

    /** @var  float */
    private $RectangleAreaW;

    /** @var  float */
    private $RectangleAreaWO;

    /** @var  float */
    private $Weight;

    /** @var  string */
    private $LaserMatName;

    /** @var  string */
    private $PrgDetSingleTime;

    /** @var  string */
    private $PrgDetAllTime;

    /** @var  int */
    private $ProgramId;

    /** @var  float */
    private $RectangleAreaRectAll;

    /** @var  float */
    private $RectangleAreaRectW;

    /** @var  float */
    private $RectangleAreaRectTrash;

    /** @var  float */
    private $RectangleAreaRectVal;

    /** @var  float */
    private $RectangleAreaRectWeight;

    /** @var  float */
    private $RectangleAreaRectWWeight;

    /** @var  float */
    private $RectangleAreaTrashWeight;

    /** @var  float */
    private $RectangleAreaTrashVal;

    /** @var  float */
    private $RemnantValue;

    /** @var  float */
    private $SurfPer;

    /** @var  float */
    private $RectAllVal;

    /** @var  float */
    private $MatValAll;

    /** @var  float */
    private $MatVal;

    /** @var  float */
    private $CompleteCut;

    /** @var  float */
    private $DetailCut;

    /** @var  float */
    private $AllSheetQty;

    /** @var  float */
    private $ComplAllPrice;

    /** @var  float */
    private $PriceKg;

    /** @var  float */
    private $LastPrice;

    /** @var  float */
    private $p_factor = 0;

    /**
     * @param array $data
     * @throws Exception
     */
    public function create($data)
    {
        try {
            foreach ($data as $name => $val)
            {
                $this->$name = $val;
            }
        } catch (\Exception $ex) {
            throw new \Exception("Brak parametru: " . $ex->getMessage());
        }

        $this->setDetailIdByPartName();
    }

    /**
     * @param int $dirId
     * @return bool
     */
    public function getDetailSettings(int $dirId)
    {
        global $db;

        $searchQuery = $db->prepare("
            SELECT p_factor
            FROM plate_multiPartCostingDetailsSettings
            WHERE
            directory_id = :dirId
            AND detaild_id = :detailId
        ");
        $searchQuery->bindValue(":dirId", $dirId, PDO::PARAM_INT);
        $searchQuery->bindValue(":detailId", $this->getDetailId(), PDO::PARAM_INT);
        $searchQuery->execute();

        $data = $searchQuery->fetch();
        if ($data === false) {
            return false;
        }

        $this->setPFactor($data["p_factor"]);
        return true;
    }

    /**
     * @param int $programId
     * @throws Exception
     */
    public function SaveData(int $programId = 0)
    {
        global $db;

        if ($programId == 0) {
            $programId = $this->getProgramId();
            if ($programId == 0) {
                throw new \Exception("Brak Id programu!");
            }
        }

        $checkQuery = $db->prepare("SELECT id FROM plate_multiPartProgramsPart WHERE PartName = :partName AND ProgramId = :programId");
        $checkQuery->bindValue(":partName", $this->getPartName(), PDO::PARAM_STR);
        $checkQuery->bindValue(":programId", $programId, PDO::PARAM_INT);
        $checkQuery->execute();
        $checkQueryResult = $checkQuery->fetch();

        $saveQuery = new sqlBuilder(sqlBuilder::INSERT, "plate_multiPartProgramsPart");
        if ($checkQueryResult !== false) {
            $saveQuery = new sqlBuilder(sqlBuilder::UPDATE, "plate_multiPartProgramsPart");
            $saveQuery->addCondition("PartName = '" . $this->getPartName() . "' AND ProgramId = " . $programId);
        } else {
            //Trzeba zapisac obrazek detalu
            $this->saveDetailImg();
        }

        $partId = $this->getId();
        if ($partId > 0) {
            $saveQuery = new sqlBuilder(sqlBuilder::UPDATE, "plate_multiPartProgramsPart");
            $saveQuery->addCondition("id = " . $partId);
        }

        $saveQuery->bindValue("DetailId", $this->getDetailId(), PDO::PARAM_INT);
        $saveQuery->bindValue("PartNo", $this->getPartNo(), PDO::PARAM_INT);
        $saveQuery->bindValue("PartName", $this->getPartName(), PDO::PARAM_STR);
        $saveQuery->bindValue("PartCount", $this->getPartCount(), PDO::PARAM_INT);
        $saveQuery->bindValue("UnfoldXSize", $this->getUnfoldXSize(), PDO::PARAM_STR);
        $saveQuery->bindValue("UnfoldYSize", $this->getUnfoldYSize(), PDO::PARAM_STR);
        $saveQuery->bindValue("RectangleArea", $this->getRectangleArea(), PDO::PARAM_STR);
        $saveQuery->bindValue("RectangleAreaW", $this->getRectangleAreaW(), PDO::PARAM_STR);
        $saveQuery->bindValue("RectangleAreaWO", $this->getRectangleAreaWO(), PDO::PARAM_STR);
        $saveQuery->bindValue("Weight", $this->getWeight(), PDO::PARAM_STR);
        $saveQuery->bindValue("LaserMatName", $this->getLaserMatName(), PDO::PARAM_STR);
        $saveQuery->bindValue("ProgramId", $programId, PDO::PARAM_INT);
    }

    private function saveDetailImg()
    {
        global $db, $data_src;

        $imgSrc = $data_src . "temp/pimg_" . $this->getPartNo() . ".jpg";
        $imgDest = $data_src . "/detale/img/min/";

        $newImgSrc = $imgDest . $this->getDetailId() . ".jpg";

        if (!file_exists($imgDest)) {
            mkdir($imgDest, 0777, true);
        }

        if (!file_exists($imgSrc)) {
            return true;
        }

        if (file_exists($newImgSrc)) {
            unlink($newImgSrc);
        }

        rename($imgSrc, $newImgSrc);

        $updateQuery = $db->prepare("
            UPDATE details
            SET img = :img
            WHERE 
            id = :did
        ");
        $updateQuery->bindValue(":img", $newImgSrc, PDO::PARAM_STR);
        $updateQuery->bindValue(":did", $this->getDetailId(), PDO::PARAM_INT);
        $updateQuery->execute();
        return true;
    }

    /**
     * @return float
     */
    public function getPFactor(): float
    {
        return floatval($this->p_factor);
    }

    /**
     * @param float $p_factor
     */
    public function setPFactor(float $p_factor)
    {
        $this->p_factor = $p_factor;
    }

    /**
     * @return float
     */
    public function getCompleteCut(): float
    {
        return $this->CompleteCut;
    }

    /**
     * @param float $CompleteCut
     */
    public function setCompleteCut(float $CompleteCut)
    {
        $this->CompleteCut = $CompleteCut;
    }

    /**
     * @return float
     */
    public function getDetailCut(): float
    {
        return $this->DetailCut;
    }

    /**
     * @param float $DetailCut
     */
    public function setDetailCut(float $DetailCut)
    {
        $this->DetailCut = $DetailCut;
    }

    /**
     * @return float
     */
    public function getAllSheetQty(): float
    {
        return $this->AllSheetQty;
    }

    /**
     * @param float $AllSheetQty
     */
    public function setAllSheetQty(float $AllSheetQty)
    {
        $this->AllSheetQty = $AllSheetQty;
    }

    /**
     * @return float
     */
    public function getComplAllPrice(): float
    {
        return $this->ComplAllPrice;
    }

    /**
     * @param float $ComplAllPrice
     */
    public function setComplAllPrice(float $ComplAllPrice)
    {
        $this->ComplAllPrice = $ComplAllPrice;
    }

    /**
     * @return float
     */
    public function getPriceKg(): float
    {
        return $this->PriceKg;
    }

    /**
     * @param float $PriceKg
     */
    public function setPriceKg(float $PriceKg)
    {
        $this->PriceKg = $PriceKg;
    }

    /**
     * @return float
     */
    public function getLastPrice(): float
    {
        return $this->LastPrice;
    }

    /**
     * @param float $LastPrice
     */
    public function setLastPrice(float $LastPrice)
    {
        $this->LastPrice = $LastPrice;
    }

    /**
     * @return float
     */
    public function getRemnantValue(): float
    {
        return $this->RemnantValue;
    }

    /**
     * @param float $RemnantValue
     */
    public function setRemnantValue(float $RemnantValue)
    {
        $this->RemnantValue = $RemnantValue;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return intval($this->id);
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getProgramId(): int
    {
        return $this->ProgramId;
    }

    /**
     * @param int $ProgramId
     */
    public function setProgramId(int $ProgramId)
    {
        $this->ProgramId = $ProgramId;
    }

    /**
     * @throws Exception
     */
    public function Calculate()
    {
        global $db;
        $detailId = $this->getDetailId();
        $timeQuery = $db->prepare("SELECT PreTime FROM plate_multiPartCostingDetails WHERE did = :did");
        $timeQuery->bindValue(":did", $detailId, PDO::PARAM_INT);
        $timeQuery->execute();
        $timeQueryData = $timeQuery->fetch();

        if (!$timeQueryData) {
            throw new \Exception("Brak czasu dla detalu o id: " . $detailId);
        }

        $this->PrgDetSingleTime = $timeQueryData["PreTime"];
        $singleTimeParsed = globalTools::calculate_second($this->PrgDetSingleTime);
        $this->PrgDetAllTime = globalTools::seconds_to_time($singleTimeParsed * $this->getPartCount());
    }

    public function setDetailIdByPartName()
    {
        $data = explode("-", $this->getPartName());
        $this->DetailId = intval($data[4]);
    }

    public function setDetailId(int $detailId)
    {
        $this->DetailId = $detailId;
    }

    /**
     * @return float
     */
    public function getSurfPer(): float
    {
        return $this->SurfPer;
    }

    /**
     * @param float $SurfPer
     */
    public function setSurfPer(float $SurfPer)
    {
        $this->SurfPer = $SurfPer;
    }

    /**
     * @return float
     */
    public function getRectAllVal(): float
    {
        return $this->RectAllVal;
    }

    /**
     * @param float $RectAllVal
     */
    public function setRectAllVal(float $RectAllVal)
    {
        $this->RectAllVal = $RectAllVal;
    }

    /**
     * @return float
     */
    public function getMatValAll(): float
    {
        return $this->MatValAll;
    }

    /**
     * @param float $MatValAll
     */
    public function setMatValAll(float $MatValAll)
    {
        $this->MatValAll = $MatValAll;
    }

    /**
     * @return float
     */
    public function getMatVal(): float
    {
        return $this->MatVal;
    }

    /**
     * @param float $MatVal
     */
    public function setMatVal(float $MatVal)
    {
        $this->MatVal = $MatVal;
    }

    /**
     * @return float
     */
    public function getRectangleAreaRectAll(): float
    {
        return $this->RectangleAreaRectAll;
    }

    /**
     * @param float $RectangleAreaRectAll
     */
    public function setRectangleAreaRectAll(float $RectangleAreaRectAll)
    {
        $this->RectangleAreaRectAll = $RectangleAreaRectAll;
    }

    /**
     * @return float
     */
    public function getRectangleAreaRectW(): float
    {
        return $this->RectangleAreaRectW;
    }

    /**
     * @param float $RectangleAreaRectW
     */
    public function setRectangleAreaRectW(float $RectangleAreaRectW)
    {
        $this->RectangleAreaRectW = $RectangleAreaRectW;
    }

    /**
     * @return float
     */
    public function getRectangleAreaRectTrash(): float
    {
        return $this->RectangleAreaRectTrash;
    }

    /**
     * @param float $RectangleAreaRectTrash
     */
    public function setRectangleAreaRectTrash(float $RectangleAreaRectTrash)
    {
        $this->RectangleAreaRectTrash = $RectangleAreaRectTrash;
    }

    /**
     * @return float
     */
    public function getRectangleAreaRectVal(): float
    {
        return $this->RectangleAreaRectVal;
    }

    /**
     * @param float $RectangleAreaRectVal
     */
    public function setRectangleAreaRectVal(float $RectangleAreaRectVal)
    {
        $this->RectangleAreaRectVal = $RectangleAreaRectVal;
    }

    /**
     * @return float
     */
    public function getRectangleAreaRectWeight(): float
    {
        return $this->RectangleAreaRectWeight;
    }

    /**
     * @param float $RectangleAreaRectWeight
     */
    public function setRectangleAreaRectWeight(float $RectangleAreaRectWeight)
    {
        $this->RectangleAreaRectWeight = $RectangleAreaRectWeight;
    }

    /**
     * @return float
     */
    public function getRectangleAreaRectWWeight(): float
    {
        return $this->RectangleAreaRectWWeight;
    }

    /**
     * @param float $RectangleAreaRectWWeight
     */
    public function setRectangleAreaRectWWeight(float $RectangleAreaRectWWeight)
    {
        $this->RectangleAreaRectWWeight = $RectangleAreaRectWWeight;
    }

    /**
     * @return float
     */
    public function getRectangleAreaTrashWeight(): float
    {
        return $this->RectangleAreaTrashWeight;
    }

    /**
     * @param float $RectangleAreaTrashWeight
     */
    public function setRectangleAreaTrashWeight(float $RectangleAreaTrashWeight)
    {
        $this->RectangleAreaTrashWeight = $RectangleAreaTrashWeight;
    }

    /**
     * @return float
     */
    public function getRectangleAreaTrashVal(): float
    {
        return $this->RectangleAreaTrashVal;
    }

    /**
     * @param float $RectangleAreaTrashVal
     */
    public function setRectangleAreaTrashVal(float $RectangleAreaTrashVal)
    {
        $this->RectangleAreaTrashVal = $RectangleAreaTrashVal;
    }

    /**
     * @return int
     */
    public function getDetailId(): int
    {
        return $this->DetailId;
    }

    /**
     * @param string $PrgDetAllTime
     */
    public function setPrgDetAllTime(string $PrgDetAllTime)
    {
        $this->PrgDetAllTime = $PrgDetAllTime;
    }

    /**
     * @param string $PrgDetSingleTime
     */
    public function setPrgDetSingleTime(string $PrgDetSingleTime)
    {
        $this->PrgDetSingleTime = $PrgDetSingleTime;
    }

    /**
     * @return string
     */
    public function getPrgDetAllTime(): string
    {
        return $this->PrgDetAllTime;
    }

    /**
     * @return string
     */
    public function getPrgDetSingleTime(): string
    {
        return $this->PrgDetSingleTime;
    }

    /**
     * @return int
     */
    public function getPartNo(): int
    {
        return $this->PartNo;
    }

    /**
     * @param int $PartNo
     */
    public function setPartNo($PartNo)
    {
        $this->PartNo = $PartNo;
    }

    /**
     * @return string
     */
    public function getPartName(): string
    {
        return $this->PartName;
    }

    /**
     * @param string $PartName
     */
    public function setPartName(string $PartName)
    {
        $this->PartName = $PartName;
    }

    /**
     * @return int
     */
    public function getPartCount(): int
    {
        return $this->PartCount;
    }

    /**
     * @param int $PartCount
     */
    public function setPartCount(int $PartCount)
    {
        $this->PartCount = $PartCount;
    }

    /**
     * @return float
     */
    public function getUnfoldXSize(): float
    {
        return $this->UnfoldXSize;
    }

    /**
     * @param float $UnfoldXSize
     */
    public function setUnfoldXSize(float $UnfoldXSize)
    {
        $this->UnfoldXSize = $UnfoldXSize;
    }

    /**
     * @return float
     */
    public function getUnfoldYSize(): float
    {
        return $this->UnfoldYSize;
    }

    /**
     * @param float $UnfoldYSize
     */
    public function setUnfoldYSize(float $UnfoldYSize)
    {
        $this->UnfoldYSize = $UnfoldYSize;
    }

    /**
     * @return float
     */
    public function getRectangleArea(): float
    {
        return $this->RectangleArea;
    }

    /**
     * @param float $RectangleArea
     */
    public function setRectangleArea(float $RectangleArea)
    {
        $this->RectangleArea = $RectangleArea;
    }

    /**
     * @return float
     */
    public function getRectangleAreaW(): float
    {
        return $this->RectangleAreaW;
    }

    /**
     * @param float $RectangleAreaW
     */
    public function setRectangleAreaW(float $RectangleAreaW)
    {
        $this->RectangleAreaW = $RectangleAreaW;
    }

    /**
     * @return float
     */
    public function getRectangleAreaWO(): float
    {
        return $this->RectangleAreaWO;
    }

    /**
     * @param float $RectangleAreaWO
     */
    public function setRectangleAreaWO(float $RectangleAreaWO)
    {
        $this->RectangleAreaWO = $RectangleAreaWO;
    }

    /**
     * @return float
     */
    public function getWeight(): float
    {
        return $this->Weight;
    }

    /**
     * @param float $Weight
     */
    public function setWeight(float $Weight)
    {
        $this->Weight = $Weight;
    }

    /**
     * @return string
     */
    public function getLaserMatName(): string
    {
        return $this->LaserMatName;
    }

    /**
     * @param string $LaserMatName
     */
    public function setLaserMatName(string $LaserMatName)
    {
        $this->LaserMatName = $LaserMatName;
    }


}