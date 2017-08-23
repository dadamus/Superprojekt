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
}