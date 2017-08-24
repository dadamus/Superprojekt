<?php
/**
 * Created by PhpStorm.
 * User: dawidadamus
 * Date: 24.08.2017
 * Time: 12:24
 */

class mainCardProjectModel
{
    /** @var  int */
    private $id;

    /** @var  int */
    private $number;

    /** @var  string */
    private $name;

    /** @var  string */
    private $detailName;

    /**
     * mainCardProjectModel constructor.
     * @param int $id
     * @param int $number
     * @param string $name
     * @param string $detailName
     */
    public function __construct(int $id, int $number, string $name, string $detailName)
    {
        $this->id = $id;
        $this->number = $number;
        $this->name = $name;
        $this->detailName = $detailName;
    }

    /**
     * @return string
     */
    public function getDetailName(): string
    {
        return $this->detailName;
    }

    /**
     * @return int
     */
    public function getNumber(): int
    {
        return $this->number;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}