<?php

/**
 * Created by PhpStorm.
 * User: dawid
 * Date: 20.08.2017
 * Time: 22:58
 */
class ImgFrame
{
    /** @var  int */
    private $id;

    /** @var  int */
    private $imgId;

    /** @var  string */
    private $type;

    /** @var  string */
    private $points;

    /** @var  float */
    private $value; 

    /** @var  int */
    private $programId;

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
     * @return int
     */
    public function getImgId(): int
    {
        return $this->imgId;
    }

    /**
     * @param int $imgId
     */
    public function setImgId(int $imgId)
    {
        $this->imgId = $imgId;
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

    /**
     * @return string
     */
    public function getPoints(): string
    {
        return $this->points;
    }

    /**
     * @param string $points
     */
    public function setPoints(string $points)
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