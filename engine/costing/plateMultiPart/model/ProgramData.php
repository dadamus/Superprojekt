<?php
/**
 * Created by PhpStorm.
 * User: dawidadamus
 * Date: 08.08.2017
 * Time: 23:13
 */

require_once dirname(__FILE__) . "/ProgramCardPartData.php";

class ProgramData
{
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

    /**
     * @param array $data
     */
    public function create($data)
    {
        $this->setSheetName($data["SheetName"]);
        $this->setUsedSheetNum($data["UsedSheetNum"]);
    }

    public function Calculate()
    {
    }

    public function SaveData()
    {
        global $db;
        $materialId = $this->getMaterial()->getId();

        $checkQuery = $db->prepare("SELECT id FROM plate_multiPartPrograms WHERE SheetName = ':sheetName'");
        $checkQuery->bindParam(":sheetName", $this->getSheetName(), PDO::PARAM_STR);
        $checkQuery->execute();
        $checkResponse = $checkQuery->fetch();

        $saveQuery = new sqlBuilder(sqlBuilder::INSERT, "plate_multiPartPrograms");
        $programDbId = false;
        if ($checkResponse !== false)
        {
            $saveQuery = new sqlBuilder(sqlBuilder::UPDATE, "plate_multiPartPrograms");
            $saveQuery->addCondition("SheetName = '" . $this->getSheetName() . "'");
        } else {
            $programDbId = $checkResponse["id"];
            $saveQuery->bindValue("CreateDate", date("Y-m-d H:i:s"), PDO::PARAM_STR);
        }

        $saveQuery->bindValue("SheetName", $this->getSheetName(), PDO::PARAM_STR);
        $saveQuery->bindValue("UsedSheetNum", $this->getUsedSheetNum(), PDO::PARAM_STR);
        $saveQuery->bindValue("materialId", $materialId, PDO::PARAM_INT);
        $saveQuery->flush();

        if ($programDbId === false) {
            $programDbId = $db->lastInsertId();
        }

        //Zapisujemy party
        foreach ($this->getParts() as $part) {
            $part->SaveData($programDbId);
        }
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
}