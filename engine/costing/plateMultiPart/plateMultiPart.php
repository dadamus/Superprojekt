<?php

/**
 * Created by PhpStorm.
 * User: dawid
 * Date: 05.07.2017
 * Time: 20:55
 */

require_once dirname(__FILE__) . "/model/PhpData.php";
require_once dirname(__DIR__) . "/../repository/MPWRepository.php";

/**
 * Class PlateMultiPart
 */
class PlateMultiPart
{
    /** @var  ProgramData[] */
    private $programs;

    /** @var  MPWRepository */
    private $MPWRepository;

    /** @var  float */
    private $remnantFactor;

    /** @var  float */
    private $priceFactor;

    /** @var  int */
    private $dirId;

    /**
     * PlateMultiPart constructor.
     */
    public function __construct()
    {
        $this->MPWRepository = new MPWRepository();
        $this->getDbSettings();
    }

    public function getDbSettings()
    {
        global $db;

        $dataQuery = $db->query("
            SELECT *
            FROM settings
        ");

        while ($row = $dataQuery->fetch()) {
            switch($row["name"]) {
                case "remnant_factor":
                    $this->setRemnantFactor(floatval($row["value"]));
                    break;
                case "p_factor":
                    $this->setPriceFactor(floatval($row["value"]));
                    break;
            }
        }
    }

    /**
     * @return float
     */
    public function getRemnantFactor(): float
    {
        return $this->remnantFactor;
    }

    /**
     * @param float $remnantFactor
     */
    public function setRemnantFactor(float $remnantFactor)
    {
        $this->remnantFactor = $remnantFactor;
    }

    /**
     * @param ProgramData $program
     */
    public function addProgram($program)
    {
        $this->programs[] = $program;
    }

    /**
     * @return ProgramData[]
     */
    public function getPrograms()
    {
        return $this->programs;
    }

    /**
     * @param int $programId
     * @return bool|ProgramData
     */
    public function getProgramById(int $programId): ProgramData
    {
        $programs = $this->getPrograms();
        for ($i = count($programs) - 1; $i >= 0; $i--) {
            /** @var ProgramData $program */
            $program = $programs[$i];
            if ($program->getId() === $programId) {
                return $program;
            }
        }

        return false;
    }

    /**
     * @param string $data
     */
    public function MakeFromData($data)
    {
        $phpData = new PhpData();
        $phpData->setContainer($data);

        $this->programs = $this->MatchPartsToPrograms($phpData->getPrograms(), $phpData->getProgramsData());
        $this->programs = $this->MatchProgramsToMaterial($this->programs, $phpData->getMaterials());
        $this->SaveData();
        $this->MpwUpdate();
    }

    /**
     * @return float
     */
    public function getPriceFactor(): float
    {
        return $this->priceFactor;
    }

    /**
     * @param float $priceFactor
     */
    public function setPriceFactor(float $priceFactor)
    {
        $this->priceFactor = $priceFactor;
    }

    /**
     * @return int
     */
    public function getDirId(): int
    {
        return $this->dirId;
    }

    /**
     * @param int $dirId
     */
    public function setDirId(int $dirId)
    {
        $this->dirId = $dirId;
    }

    /**
     * @param int $dirId
     */
    public function MakeFromDirId(int $dirId)
    {
        $this->setDirId($dirId);
        $this->programs = $this->GetProgramsByDirId($dirId);
    }

    //MPW zeby ramka sie pokazywala
    public function MpwUpdate()
    {
        $mpws = [];
        foreach ($this->programs as $program) {
            foreach ($program->getParts() as $part) {
                $detailName = $part->getPartName();
                $mpw = $this->MPWRepository->getMpwByDetailName($detailName);

                if (isset($mpws[$mpw->getMpwId()])) {
                    continue;
                }

                $mpws[$mpw->getMpwId()] = true;
                $mpw->setType(OT::AUTO_WYCENA_BLACH_MULTI_KROK_2);
                $mpw->save();
            }
        }
    }

    public function SaveData()
    {
        foreach($this->programs as $program)
        {
            $program->SaveData($this->getDirId());
        }
    }

    /**
     * @param int $dirId
     * @return array
     */
    private function GetProgramsByDirId(int $dirId): array
    {
        $parts = $this->GetPartsByDirId($dirId);

        $programsParts = [];

        foreach ($parts as $part) {
            $programsParts[$part->getProgramId()][] = $part;
        }

        /** @var ProgramData[] $programs */
        $programs = [];
        foreach ($programsParts as $programId => $parts) {
            $programData = new ProgramData();
            $programData->getById($programId);
            $programData->setParts($parts);
            $programs[] = $programData;
        }

        return $programs;
    }

    /**
     * @param int $dirId
     * @return array
     */
    private function GetPartsByDirId(int $dirId): array
    {
        global $db;

        $searchQuery = $db->prepare("
            SELECT 
            part.*
            FROM
            plate_multiPartDetails d
            LEFT JOIN plate_multiPartProgramsPart part ON part.PartName = d.name
            WHERE
            d.dirId = :dirId
        ");
        $searchQuery->bindValue(":dirId", $dirId, PDO::PARAM_INT);
        $searchQuery->execute();

        $parts = [];
        while($partData = $searchQuery->fetch(PDO::FETCH_ASSOC)) {
            $part = new ProgramCardPartData();
            $part->create($partData);
            $part->getDetailSettings($dirId);
            $part->Calculate();
            $parts[] = $part;
        }

        return $parts;
    }

    /**
     * @param ProgramData[] $programs
     * @param ProgramCardData[] $programsData
     * @return ProgramData[]
     */
    private function MatchPartsToPrograms($programs, $programsData)
    {
        foreach ($programs as $program) {
            $sheetName = $program->getSheetName();
            foreach ($programsData as $programData)
            {
                if ($programData->getSheetName() === $sheetName)
                {
                    $program->setSheetCount($programData->getSheetCount());
                    foreach ($programData->getParts() as $part)
                    {
                        $program->addPart($part);
                    }
                }
            }
        }

        return $programs;
    }

    /**
     * @param ProgramData[] $programs
     * @param MaterialData[] $materials
     * @return ProgramData[]
     */
    private function MatchProgramsToMaterial($programs, $materials): array
    {
        $material = reset($materials);
        $materialSheetNumber = $material->getUsedSheetNum();
        //Zapisujemy do bazy zeby sie id to samo zapisalo
        $material->save();

        foreach ($programs as $program) {
            $program->setMaterial($material);
            $materialSheetNumber -= $program->getSheetCount();
            if ($materialSheetNumber <= 0) {
                if (next($materials) === false)
                {
                    break;
                }
                $material = current($materials);
                $material->save();
            }

            $materialSheetNumber = $material->getUsedSheetNum();
        }

        return $programs;
    }

    /**
     * Jak juz sobie wszystko dopasujemy zrobimy ramki to odapla sie ten event
     */
    public function Calculate()
    {
        foreach ($this->getPrograms() as $program) {
            $program->Calculate($this->getRemnantFactor());
        }
    }
}