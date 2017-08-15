<?php

/**
 * Created by PhpStorm.
 * User: dawid
 * Date: 05.07.2017
 * Time: 20:55
 */

require_once dirname(__FILE__) . "/model/PhpData.php";
require_once dirname(__DIR__) . "/../repository/MPWRepository.php";

class PlateMultiPart
{
    /** @var  ProgramData[] */
    private $programs;

    /** @var  MPWRepository */
    private $MPWRepository;

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

    //MPW zeby ramka sie pokazywala
    public function MpwUpdate()
    {
        /** @var ProgramData $program */
        $program = reset($this->programs);

        /** @var ProgramCardPartData $part */
        $part = reset($program->getParts());

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
        $materialSheetNumber = $material->getUsed§SheetNum();
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
            }

            $material = current($materials);
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