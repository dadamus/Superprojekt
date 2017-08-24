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
    private $pid;

    /**
     * @var string
     */
    private $src;

    /**
     * @var int
     */
    private $frame;

    /**
     * @var string
     */
    private $code;

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

    /**
     * @var string
     */
    private $date;

    /**
     * @var int
     */
    private $type;

    public function __construct(array $data = [])
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
     * @param int $mpwId
     * @return bool
     * @throws Exception
     */
    public function findById(int $mpwId): bool
    {
        global $db;
        $mpwQuery = $db->prepare('SELECT * FROM mpw WHERE id = :id');
        $mpwQuery->bindValue(':id', $mpwId, PDO::PARAM_INT);
        $mpwQuery->execute();

        $mpwData = $mpwQuery->fetch();
        if ($mpwData === false) {
            throw new Exception('Brak mpw o id: ' . $mpwId);
        }

        $this->setMpwId($mpwId);
        foreach ($mpwData as $key => $value) {
            switch ($key) {
                case "pid":
                    $this->setPid(intval($value));
                    break;

                case "src":
                    $this->setSrc($value);
                    break;

                case "frame":
                    $this->setFrame(intval($value));
                    break;

                case "code":
                    $this->setCode($value);
                    break;

                case "version":
                    $this->setVersion(intval($value));
                    break;

                case "material":
                    $this->setMaterial(intval($value));
                    break;

                case "thickness":
                    $this->setThickness(floatval($value));
                    break;

                case "pieces":
                    $this->setPieces($value);
                    break;

                case "atribute":
                    $this->setAttributes($value);
                    break;

                case "desc":
                    $this->setDes($value);
                    break;

                case "date":
                    $this->setDate($value);
                    break;

                case "type":
                    $this->setType(intval($value));
                    break;

                case "plate_multiDirectory":
                    $this->setMpwDirectory(intval($value));
                    break;
            }
        }

        return true;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType(int $type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getDate(): string
    {
        return $this->date;
    }

    /**
     * @param string $date
     */
    public function setDate(string $date)
    {
        $this->date = $date;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode(string $code)
    {
        $this->code = $code;
    }

    /**
     * @return int
     */
    public function getFrame(): int
    {
        return $this->frame;
    }

    /**
     * @param int $frame
     */
    public function setFrame(int $frame)
    {
        $this->frame = $frame;
    }

    /**
     * @return string
     */
    public function getSrc(): string
    {
        return $this->src;
    }

    /**
     * @param string $src
     */
    public function setSrc(string $src)
    {
        $this->src = $src;
    }

    /**
     * @return int
     */
    public function getPid(): int
    {
        return $this->pid;
    }

    /**
     * @param int $pid
     */
    public function setPid(int $pid)
    {
        $this->pid = $pid;
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
     * @param string $data
     */
    public function setAttributes(string $data)
    {
        $this->attributes = $data;
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

        $searchDirQuery = $db->prepare("
            SELECT * 
            FROM plate_multiPartDirectories
            WHERE
            id = :id
        ");
        $searchDirQuery->bindValue(":id", $_POST["mpw_directory"], PDO::PARAM_INT);
        $searchDirQuery->execute();

        $dirData = $searchDirQuery->fetch();
        if ($dirData === false) {
            throw new \Exception("Brak folderu o id: " . $dirData);
        }

        $dirDataParts = explode("/", $dirData["dir_name"]);
        $dirNr = $dirDataParts[0];

        //Robimy glowny folder wyceny
        $mpwPath = $data_src . "multipart/" . date("m") . "/" . $dirNr;
        mkdir($mpwPath, 0777, true);
        chmod($mpwPath, 0777);

        $materialQuery = $db->query("SELECT `name` FROM material WHERE id = " . $this->getMaterial());
        $materialName = $materialQuery->fetch()["name"];

        $attributes = "";
        $attributesData = json_decode($this->getAttributes(), true);
        if (count($attributesData) > 0) {
            foreach ($attributesData as $attribute) {
                $attributes .= _getChecboxText($attribute);
            }
        }

        $projectData = $db->query("SELECT src FROM projects WHERE id = " . $this->getMpwProject());
        $projectPath = $projectData->fetch()["src"];

        $insertQuery = $db->prepare("INSERT INTO plate_multiPartDetails (mpw, dirId, did, src, `name`) VALUES (:mpw, :dirId, :did, :src, :name)");

        foreach ($details as $detail) {
            $detailQuery = $db->query("SELECT src FROM details WHERE id = $detail");
            $detailName = $detailQuery->fetch()["src"];
            $detailNameExploded = explode(".", $detailName);
            $detailExt = end($detailNameExploded);

            $detailNewName = "MP-" . $this->getPieces() . "-" . $this->getThickness() . "MM-$materialName-$detail";
            if ($attributes != "") {
                $detailNewName .= "-" . $attributes;
            }
            $detailNewNameWithoutExt = $detailNewName;
            $detailNewName .= "." . $detailExt;

            $detailOldPath = $projectPath . "/V" . $this->getVersion() . "/dxf/" . $detailName;
            echo $detailOldPath ." new: " . $mpwPath . "/" . $detailNewName;
            copy($detailOldPath, $mpwPath . "/" . $detailNewName);

            $insertQuery->bindValue(":mpw", $this->getMpwId(), PDO::PARAM_INT);
            $insertQuery->bindValue(":dirId", $_POST["mpw_directory"], PDO::PARAM_INT);
            $insertQuery->bindValue(":did", $detail, PDO::PARAM_INT);
            $insertQuery->bindValue(":src", $detailNewName, PDO::PARAM_STR);
            $insertQuery->bindValue(":name", $detailNewNameWithoutExt, PDO::PARAM_STR);
            $insertQuery->execute();
        }

        $db->query("UPDATE mpw SET src = '$mpwPath' WHERE id = " . $this->getMpwId());
    }

    public function save()
    {
        global $db;
        $insert = true;
        $sqlBuilder = new sqlBuilder(sqlBuilder::INSERT, "mpw");

        if ($this->getMpwId() > 0) {
            $insert = false;
            $sqlBuilder = new sqlBuilder(sqlBuilder::UPDATE, "mpw");
            $sqlBuilder->addCondition('id = ' . $this->getMpwId());
        }

        $sqlBuilder->bindValue("pid", $this->getPid(), PDO::PARAM_INT);
        $sqlBuilder->bindValue("src", $this->getSrc(), PDO::PARAM_STR);
        $sqlBuilder->bindValue("frame", $this->getFrame(), PDO::PARAM_INT);
        $sqlBuilder->bindValue("code", $this->getCode(), PDO::PARAM_STR);
        $sqlBuilder->bindValue("version", $this->getVersion(), PDO::PARAM_INT);
        $sqlBuilder->bindValue("material", $this->getMaterial(), PDO::PARAM_INT);
        $sqlBuilder->bindValue("thickness", $this->getThickness(), PDO::PARAM_STR);
        $sqlBuilder->bindValue("pieces", $this->getPieces(), PDO::PARAM_INT);
        $sqlBuilder->bindValue("atribute", $this->getAttributes(), PDO::PARAM_STR);
        $sqlBuilder->bindValue("des", $this->getDes(), PDO::PARAM_STR);
        $sqlBuilder->bindValue("date", $this->getDate(), PDO::PARAM_STR);
        $sqlBuilder->bindValue("type", $this->getType(), PDO::PARAM_INT);
        $sqlBuilder->bindValue("plate_multiDirectory", $this->getMpwDirectory(), PDO::PARAM_INT);
        $sqlBuilder->flush();

        if ($insert) {
            $this->setMpwId($db->lastInsertId());
        }
    }
}