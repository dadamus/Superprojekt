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
        $saveQuery->flush();
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