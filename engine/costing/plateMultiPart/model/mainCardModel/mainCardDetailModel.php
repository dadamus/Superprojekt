<?php
/**
 * Created by PhpStorm.
 * User: dawidadamus
 * Date: 24.08.2017
 * Time: 12:32
 */

require_once dirname(__FILE__) . "/mainCardProjectModel.php";
require_once dirname(__DIR__) . "/ProgramCardPartData.php";
require_once dirname(__DIR__) . "/MaterialData.php";

class mainCardDetailModel
{
    /** @var  mainCardProjectModel */
    private $project;

    /** @var  MaterialData */
    private $material;

    /** @var  float */
    private $CutAll = 0;

    /** @var  float */
    private $PriceFactor;

    /** @var  float */
    private $MatAll = 0;

    /** @var int */
    private $CountAll = 0;

    /** @var  float */
    private $Mat;

    /** @var  float */
    private $Cut;

    /** @var  float */
    private $KomN;

    /** @var  float */
    private $KomB;

    /** @var  float */
    private $SztN;

    /** @var  float */
    private $SztB;

    private $AllWeight;

    /** @var  float */
    private $PrcKgN;

    /** @var  float */
    private $PrcKgB;

    /** @var  float */
    private $Weight;

    /**
     * @return mainCardProjectModel
     */
    public function getProject(): mainCardProjectModel
    {
        return $this->project;
    }

    /**
     * @param mainCardProjectModel $project
     */
    public function setProject(mainCardProjectModel $project)
    {
        $this->project = $project;
    }

    /**
     * @param ProgramCardPartData $data
     * @param int $sheetCount
     */
    public function Make(ProgramCardPartData $data, int $sheetCount)
    {
        $this->setCutAll(
            $this->getCutAll() + ($data->getComplAllPrice() * $sheetCount * $this->getPriceFactor())
        );
        $this->setMatAll(
            $this->getMatAll() + ($data->getMatValAll() * $sheetCount)
        );
        $this->setCountAll(
            $this->getCountAll() + $data->getAllSheetQty()
        );
        $this->setAllWeight(
            $data->getWeight() / 1000 * $data->getAllSheetQty()
        );
    }

    /**
     * Liczmy sobie
     */
    public function Calculate()
    {
        $this->setMat(
            round($this->getMatAll() / $this->getCountAll(), 2)
        );
        $this->setCut(
            round($this->getCutAll() / $this->getCountAll(), 2)
        );
        $this->setKomN(
            round($this->getMatAll() + $this->getCutAll(), 2) // + checkboxy
        );
        $this->setKomB(
            round($this->getKomN() * 1.23, 2)
        );
        $this->setSztN(
            round($this->getKomN() / $this->getCountAll(), 2)
        );
        $this->setSztB(
            round($this->getSztN() * 1.23, 2)
        );
        $this->setPrcKgN(
            round($this->getKomN() / $this->getAllWeight(), 2)
        );
        $this->setPrcKgB(
            round($this->getPrcKgN() * 1.23, 2)
        );
    }

    /**
     * @return mixed
     */
    public function getAllWeight()
    {
        return $this->AllWeight;
    }

    /**
     * @param mixed $AllWeight
     */
    public function setAllWeight($AllWeight)
    {
        $this->AllWeight = $AllWeight;
    }

    /**
     * @return float
     */
    public function getWeight(): float
    {
        return $this->Weight;
    }

    /**
     * @param float $Weight
     */
    public function setWeight(float $Weight)
    {
        $this->Weight = $Weight;
    }

    /**
     * @return float
     */
    public function getPrcKgN(): float
    {
        return $this->PrcKgN;
    }

    /**
     * @param float $PrcKgN
     */
    public function setPrcKgN(float $PrcKgN)
    {
        $this->PrcKgN = $PrcKgN;
    }

    /**
     * @return float
     */
    public function getPrcKgB(): float
    {
        return $this->PrcKgB;
    }

    /**
     * @param float $PrcKgB
     */
    public function setPrcKgB(float $PrcKgB)
    {
        $this->PrcKgB = $PrcKgB;
    }

    /**
     * @return MaterialData
     */
    public function getMaterial(): MaterialData
    {
        return $this->material;
    }

    /**
     * @param MaterialData $material
     */
    public function setMaterial(MaterialData $material)
    {
        $this->material = $material;
    }

    /**
     * @return int
     */
    public function getCountAll(): int
    {
        return $this->CountAll;
    }

    /**
     * @param int $CountAll
     */
    public function setCountAll(int $CountAll)
    {
        $this->CountAll = $CountAll;
    }

    /**
     * @return float
     */
    public function getMatAll(): float
    {
        return round($this->MatAll, 2);
    }

    /**
     * @param float $MatAll
     */
    public function setMatAll(float $MatAll)
    {
        $this->MatAll = $MatAll;
    }

    /**
     * @return float
     */
    public function getCutAll(): float
    {
        return $this->CutAll;
    }

    /**
     * @param float $CutAll
     */
    public function setCutAll(float $CutAll)
    {
        $this->CutAll = $CutAll;
    }

    /**
     * @return float
     */
    public function getPriceFactor(): float
    {
        return $this->PriceFactor;
    }

    /**
     * @param float $PriceFactor
     */
    public function setPriceFactor(float $PriceFactor)
    {
        $this->PriceFactor = $PriceFactor;
    }

    /**
     * @return float
     */
    public function getMat(): float
    {
        return $this->Mat;
    }

    /**
     * @param float $Mat
     */
    public function setMat(float $Mat)
    {
        $this->Mat = $Mat;
    }

    /**
     * @return float
     */
    public function getCut(): float
    {
        return $this->Cut;
    }

    /**
     * @param float $Cut
     */
    public function setCut(float $Cut)
    {
        $this->Cut = $Cut;
    }

    /**
     * @return float
     */
    public function getKomN(): float
    {
        return $this->KomN;
    }

    /**
     * @param float $KomN
     */
    public function setKomN(float $KomN)
    {
        $this->KomN = $KomN;
    }

    /**
     * @return float
     */
    public function getKomB(): float
    {
        return $this->KomB;
    }

    /**
     * @param float $KomB
     */
    public function setKomB(float $KomB)
    {
        $this->KomB = $KomB;
    }

    /**
     * @return float
     */
    public function getSztN(): float
    {
        return $this->SztN;
    }

    /**
     * @param float $SztN
     */
    public function setSztN(float $SztN)
    {
        $this->SztN = $SztN;
    }

    /**
     * @return float
     */
    public function getSztB(): float
    {
        return $this->SztB;
    }

    /**
     * @param float $SztB
     */
    public function setSztB(float $SztB)
    {
        $this->SztB = $SztB;
    }
}