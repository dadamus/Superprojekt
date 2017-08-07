<?php

/**
 * Created by PhpStorm.
 * User: dawid
 * Date: 18.07.2017
 * Time: 20:20
 */

/**
 * Class MPWModel
 */
class MPWModel
{
    /**
     * @var int
     */
    private $mpw_id;

    /**
     * @var int
     */
    private $mpw_directory;

    /**
     * @var int
     */
    private $mpw_project;

    /**
     * @var string
     */
    private $mpw_details;

    /**
     * @var int
     */
    private $material;

    /**
     * @var float
     */
    private $thickness;

    /**
     * @var int
     */
    private $pieces;

    /**
     * @var int
     */
    private $version;

    /**
     * @var string
     */
    private $attributes;

    /**
     * @var string
     */
    private $des;

    public function __construct(array $data)
    {
        foreach ($data as $key => $value) {
            switch ($key) {
                case "mpw_directory":
                    $this->setMpwDirectory($value);
                    break;
                case "mpw_project":
                    $this->setMpwProject($value);
                    break;
                case "mpw_details":
                    $this->setMpwDetails($value);
                    break;
                case "material":
                    $this->setMaterial($value);
                    break;
                case "thickness":
                    $this->setThickness($value);
                    break;
                case "pieces":
                    $this->setPieces($value);
                    break;
                case "version":
                    $this->setVersion($value);
                    break;
                case "des":
                    $this->setDes($value);
                    break;
                case "cba":
                    $this->makeAttributes($value);
                    break;
            }
        }

        //make attributes
    }

    /**
     * @param array $attributes
     */
    public function makeAttributes(array $attributes)
    {
        $data = [];
        foreach ($attributes as $a) {
            $data[] = $a;
        }

        $this->attributes = json_encode($data);
    }

    /**
     * @return string
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @return int
     */
    public function getMpwId(): int
    {
        return $this->mpw_id;
    }

    /**
     * @param int $mpw_id
     */
    public function setMpwId(int $mpw_id)
    {
        $this->mpw_id = $mpw_id;
    }


    /**
     * @return mixed
     */
    public function getMpwDirectory()
    {
        return $this->mpw_directory;
    }

    /**
     * @param mixed $mpw_directory
     */
    public function setMpwDirectory($mpw_directory)
    {
        $this->mpw_directory = intval($mpw_directory);
    }

    /**
     * @return mixed
     */
    public function getMpwProject()
    {
        return $this->mpw_project;
    }

    /**
     * @param mixed $mpw_project
     */
    public function setMpwProject($mpw_project)
    {
        $this->mpw_project = intval($mpw_project);
    }

    /**
     * @return mixed
     */
    public function getMpwDetails()
    {
        return $this->mpw_details;
    }

    /**
     * @param mixed $mpw_details
     */
    public function setMpwDetails($mpw_details)
    {
        $this->mpw_details = $mpw_details;
    }

    /**
     * @return mixed
     */
    public function getMaterial()
    {
        return $this->material;
    }

    /**
     * @param mixed $material
     */
    public function setMaterial($material)
    {
        $this->material = intval($material);
    }

    /**
     * @return mixed
     */
    public function getThickness()
    {
        return $this->thickness;
    }

    /**
     * @param mixed $thickness
     */
    public function setThickness($thickness)
    {
        $this->thickness = floatval($thickness);
    }

    /**
     * @return mixed
     */
    public function getPieces()
    {
        return $this->pieces;
    }

    /**
     * @param mixed $pieces
     */
    public function setPieces($pieces)
    {
        $this->pieces = intval($pieces);
    }

    /**
     * @return mixed
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param mixed $version
     */
    public function setVersion($version)
    {
        $this->version = intval($version);
    }

    /**
     * @return mixed
     */
    public function getDes()
    {
        return $this->des;
    }

    /**
     * @param mixed $des
     */
    public function setDes($des)
    {
        $this->des = $des;
    }

    public function makeDetails()
    {
        global $data_src, $db;

        if (is_null($this->getMpwDetails())) {
            throw new \Exception("Brak detail!");
        }

        $details = json_decode($this->getMpwDetails(), true);

        if (count($details) == 0) {
            throw new \Exception("Brak detali!");
        }

        if ($this->getMpwId() == 0) {
            throw new \Exception("Brak mpw id!");
        }


        //Robimy glowny folder wyceny
        $mpwPath = $data_src . "multipart/" . date("m") . "/" . $this->getMpwId();
        mkdir($mpwPath, 0777, true);

        $materialQuery = $db->query("SELECT `name` FROM material WHERE id = " . $this->getMaterial());
        $materialName = $materialQuery->fetch()["name"];

        $attributes = "";
        $attributesData = json_decode($this->getAttributes(), true);
        foreach ($attributesData as $attribute) {
            $attributes .= _getChecboxText($attribute);
        }

        $projectData = $db->query("SELECT src FROM projects WHERE id = " . $this->getMpwProject());
        $projectPath = $projectData->fetch()["src"];

        $insertQuery = $db->prepare("INSERT INTO plate_multiPartDetails (mpw, did, src) VALUES (:mpw, :did, :src)");

        foreach ($details as $detail) {
            $detailQuery = $db->query("SELECT src FROM details WHERE id = $detail");
            $detailName = $detailQuery->fetch()["src"];
            $detailNameExploded = explode(".", $detailName);
            $detailExt = end($detailNameExploded);

            $detailNewName = "MP-" . $this->getPieces() . "-" . $this->getThickness() . "MM-$materialName-$detail";
            if ($attributes != "") {
                $detailNewName .= "-" . $attributes;
            }
            $detailNewName .= "." . $detailExt;

            $detailOldPath = $projectPath . "/V" . $this->getVersion() . "/dxf/" . $detailName;
            copy($detailOldPath, $mpwPath . "/" . $detailNewName);

            $insertQuery->bindValue(":mpw", $this->getMpwId(), PDO::PARAM_INT);
            $insertQuery->bindValue(":did", $detail, PDO::PARAM_INT);
            $insertQuery->bindValue(":src", $detailNewName, PDO::PARAM_STR);
            $insertQuery->execute();
        }

        $db->query("UPDATE mpw SET src = '$mpwPath' WHERE id = " . $this->getMpwId());
    }
}