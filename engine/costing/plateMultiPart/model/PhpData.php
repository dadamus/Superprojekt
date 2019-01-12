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

        $programId = 1;
        //Load programs
        foreach ($data["programs"] as $programData)
        {
            $program = new ProgramData();
            $program->create($programData);

            while (true) {
                try {
                    $ImageId = $this->createImage($program->getSheetName(), $programId);
                    $program->setImageId($ImageId);
                } catch (\Exception $ex) {
                    $programId++;
                    continue;
                }

                return;
            }

            $this->programs[] = $program;
            $programId++;
        }

        //Load program data
        foreach ($data["programsData"] as $programData) {
            $programCard = new ProgramCardData();
            $programCard->create($programData);
            $this->programsData[] = $programCard;
        }
    }

    /**
     * @param string $SheetName
     * @param int $programId
     * @return string
     * @throws Exception
     */
    private function createImage(string $SheetName, int $programId)
    {
        global $data_src, $db;

        $newImageName = substr(
            md5(
                date("Y-m-d H:i:s")
            ),
            0,
            6
            ) . "_" . str_replace(
                [" ", "+"],
                ['-', '-'],
                $SheetName
            ) . ".bmp"
        ;

        $filePath = $data_src . 'temp/' . $programId . '.bmp';

        if (!file_exists($filePath)) {
            throw new Exception("Plik: $filePath nie istnieje!");
        }

        $newPath = $data_src . "temp/plateData/" . $newImageName;
        if (!file_exists($data_src . 'temp/plateData/')) {
            mkdir($data_src . 'temp/plateData/', 0777, true);
        }
        rename($filePath, $newPath);

        $sqlBuilder = new sqlBuilder(sqlBuilder::INSERT, "plate_CostingImage");
        $sqlBuilder->bindValue("path", $newPath, PDO::PARAM_STR);
        $sqlBuilder->bindValue("plate_costingType", "multiPartCosting", PDO::PARAM_STR);
        $sqlBuilder->bindValue("costing_name", "Plate Multi", PDO::PARAM_STR);
        $sqlBuilder->flush();

        $imgId = $db->lastInsertId();
        return $imgId;
    }
}