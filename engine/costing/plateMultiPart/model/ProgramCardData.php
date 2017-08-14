<?php
/**
 * Created by PhpStorm.
 * User: dawidadamus
 * Date: 08.08.2017
 * Time: 21:18
 */

require_once dirname(__FILE__) . "/ProgramCardPartData.php";

class ProgramCardData
{
    /** @var  ProgramCardPartData[] */
    private $parts;

    /** @var  string */
    private $SheetName;

    /** @var  int */
    private $SheetCount;

    /**
     * @param array $data
     */
    function create($data)
    {
        $this->setSheetName($data["SheetName"]);
        $this->setSheetCount($data["SheetCount"]);

        foreach ($data["parts"] as $partData) {
            $part = new ProgramCardPartData();
            $part->create($partData);
            $this->addPart($part);
        }
    }

    /**
     * @return ProgramCardPartData[]
     */
    public function getParts()
    {
        return $this->parts;
    }

    /**
     * @param ProgramCardPartData $part
     */
    public function addPart($part)
    {
       $this->parts[] = $part;
    }

    /**
     * @param ProgramCardPartData[] $parts
     */
    public function setParts($parts)
    {
        $this->parts = $parts;
    }

    /**
     * @return string
     */
    public function getSheetName()
    {
        return $this->SheetName;
    }

    /**
     * @param string $SheetName
     */
    public function setSheetName($SheetName)
    {
        $this->SheetName = $SheetName;
    }

    /**
     * @return int
     */
    public function getSheetCount()
    {
        return $this->SheetCount;
    }

    /**
     * @param int $SheetCount
     */
    public function setSheetCount($SheetCount)
    {
        $this->SheetCount = $SheetCount;
    }


}