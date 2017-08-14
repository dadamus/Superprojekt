<?php

/**
 * Created by PhpStorm.
 * User: dawid
 * Date: 05.07.2017
 * Time: 20:55
 */

require_once dirname(__FILE__) . "/model/PhpData.php";

class PlateMultiPart
{
    /** @var  ProgramData[] */
    private $programs;

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