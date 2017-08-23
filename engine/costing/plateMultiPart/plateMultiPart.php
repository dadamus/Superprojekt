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

    /** @var  MPWModel */
    private $mpw;

    /**
     * PlateMultiPart constructor.
     */
    public function __construct()
    {
        $this->MPWRepository = new MPWRepository();
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
     * @return MPWModel
     */
    public function getMPW()
    {
        return $this->mpw;
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
     * @param int $mpwId
     */
    public function MakeFromMpwId(int $mpwId)
    {
        $mpw = $this->MPWRepository->getMpwById($mpwId);
        $this->mpw = $mpw;
        $this->programs = $this->GetProgramsByMpw($mpw);
    }

    //MPW zeby ramka sie pokazywala
    public function MpwUpdate()
    {
        /** @var ProgramData $program */
        $program = reset($this->programs);
        $parts = $program->getParts();

        /** @var ProgramCardPartData $part */
        $part = reset($parts);

        $detailName = $part->getPartName();
        $mpw = $this->MPWRepository->getMpwByDetailName($detailName);
        $mpw->setType(OT::AUTO_WYCENA_BLACH_MULTI_KROK_2);
        $mpw->save();
    }

    public function SaveData()
    {
        foreach($this->programs as $program)
        {
            $program->SaveData();
        }
    }

    /**
     * @param MPWModel $mpw
     * @return array
     */
    private function GetProgramsByMpw(MPWModel $mpw): array
    {
        $parts = $this->GetPartsByMpw($mpw);

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
     * @param MPWModel $mpw
     * @return ProgramCardPartData[]
     */
    private function GetPartsByMpw(MPWModel $mpw): array
    {
        global $db;
        $mpwId = $mpw->getMpwId();

        $searchQuery = $db->prepare("
            SELECT 
            part.*
            FROM
            plate_multiPartDetails d
            LEFT JOIN plate_multiPartProgramsPart part ON part.PartName = d.name
            WHERE
            d.mpw = :mpw
        ");
        $searchQuery->bindValue(":mpw", $mpwId, PDO::PARAM_INT);
        $searchQuery->execute();

        $parts = [];
        while($partData = $searchQuery->fetch(PDO::FETCH_ASSOC)) {
            $part = new ProgramCardPartData();
            $part->create($partData);
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
     * Jak juz sobie wszystko dopasujemy to odpalmy ta akcje
     */
    public function Calculate()
    {

    }


}