<?php
/**
 * Created by PhpStorm.
 * User: dawid
 * Date: 20.08.2017
 * Time: 22:58
 */

require_once dirname(__FILE__) . "/ImgData.php";

class ImgFrame
{
    /** @var  int */
    private $id;

    /** @var ImgData */
    private $img;

    /** @var  string */
    private $type;

    /** @var  string */
    private $points;

    /** @var  float */
    private $value; 

    /** @var  int */
    private $programId;

    /**
     * @param int $programId
     * @throws Exception
     */
    public function getDataByProgramId(int $programId)
    {
        global $db;

        $searchQuery = $db->prepare("
            SELECT 
            id,
            imgId,
            `type`,
            points,
            `value`,
            programId
            FROM 
            plate_costingFrame
            WHERE 
            programId = :programId
        ");
        $searchQuery->bindValue(":programId", $programId, PDO::PARAM_INT);
        $searchQuery->execute();

        $data = $searchQuery->fetch();

        if ($data === false) {
            throw new \Exception("Brak ramki dal programu: " . $programId);
        }

        $this->setData($data);
    }

    /**
     * @param array $data
     */
    private function setData(array $data)
    {
        $this->setId($data["id"]);
        $this->setType($data["type"]);
        $this->setPoints($data["points"]);
        $this->setValue(floatval($data["value"]));
        $this->setProgramId($data["programId"]);

        $img = new ImgData();
        $img->getDataByImgId($data["imgId"]);
        $this->setImg($img);
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
     * @return ImgData
     */
    public function getImg(): ImgData
    {
        return $this->img;
    }

    /**
     * @param ImgData $img
     */
    public function setImg(ImgData $img)
    {
        $this->img = $img;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }

    public function getPoints()
    {
        return $this->points;
    }

    /**
     * @param string|null $points
     */
    public function setPoints($points)
    {
        $this->points = $points;
    }

    /**
     * @return float
     */
    public function getValue(): float
    {
        return $this->value;
    }

    /**
     * @param float $value
     */
    public function setValue(float $value)
    {
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getProgramId(): int
    {
        return $this->programId;
    }

    /**
     * @param int $programId
     */
    public function setProgramId(int $programId)
    {
        $this->programId = $programId;
    }
}