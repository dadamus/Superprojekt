<?php
/**
 * Created by PhpStorm.
 * User: dawidadamus
 * Date: 08.08.2017
 * Time: 21:06
 */

require_once dirname(__FILE__) . "/ProgramData.php";
require_once dirname(__FILE__) . "/MaterialData.php";
require_once dirname(__FILE__) . "/ProgramCardData.php";

class PhpData
{
    /** @var  ProgramData[] */
    private $programs;

    /** @var  MaterialData[] */
    private $materials;

    /** @var  ProgramCardData[] */
    private $programsData;

    /**
     * @return ProgramData[]
     */
    public function getPrograms()
    {
        return $this->programs;
    }

    /**
     * @return MaterialData[]
     */
    public function getMaterials()
    {
        return $this->materials;
    }

    /**
     * @return ProgramCardData[]
     */
    public function getProgramsData()
    {
        return $this->programsData;
    }

    /**
     * @param string $message
     */
    public function setContainer($message)
    {
        $data = json_decode($message, true);

        //Load material
        foreach ($data["materials"] as $materialData)
        {
            $material = new MaterialData();
            $material->create($materialData);
            $this->materials[] = $material;
        }

        //Load programs
        foreach ($data["programs"] as $programData)
        {
            $program = new ProgramData();
            $program->create($programData);
            $this->programs[] = $program;
        }

        //Load program data
        foreach ($data["programsData"] as $programData) {
            $programCard = new ProgramCardData();
            $programCard->create($programData);
            $this->programsData[] = $programCard;
        }
    }
}