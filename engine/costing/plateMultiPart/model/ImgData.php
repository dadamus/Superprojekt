<?php
/**
 * Created by PhpStorm.
 * User: dawidadamus
 * Date: 21.08.2017
 * Time: 21:19
 */

class ImgData
{
    /** @var  int */
    private $id;

    /** @var  string */
    private $path;

    public function getDataByImgId(int $imgId)
    {
        global $db;

        $searchQuery = $db->prepare("
            SELECT 
            id,
            path
            FROM plate_CostingImage
            WHERE
            id = :id
        ");
        $searchQuery->bindValue(":id", $imgId, PDO::PARAM_INT);
        $searchQuery->execute();

        $data = $searchQuery->fetch();

        if ($data === false) {
            throw new \Exception("Brak img o id: " . $imgId);
        }

        $this->setData($data);
    }

    private function setData(array $data)
    {
        $this->setId($data["id"]);
        $this->setPath($data["path"]);
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
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path)
    {
        $this->path = $path;
    }
}