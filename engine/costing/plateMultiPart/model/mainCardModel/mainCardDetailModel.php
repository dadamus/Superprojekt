<?php
/**
 * Created by PhpStorm.
 * User: dawidadamus
 * Date: 24.08.2017
 * Time: 12:32
 */

require_once __DIR__ . '/mainCardProjectModel.php';
require_once __DIR__. '/../ProgramCardPartData.php';
require_once __DIR__ . '/../MaterialData.php';

class mainCardDetailModel
{
    /** @var  mainCardProjectModel */
    private $project;

    /** @var  int */
    private $detailId;

    /** @var  MaterialData */
    private $material;

    /** @var CheckboxModel */
    private $checkbox;

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

    /** @var int float */
    private $AllWeight = 0;

    /** @var  float */
    private $PrcKgN;

    /** @var  float */
    private $PrcKgB;

    /** @var  float */
    private $Weight;

    /** @var  string */
    private $checkboxLabels;

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
     * @param int $dirId
     */
    public function Make(ProgramCardPartData $data, int $sheetCount, int $dirId)
    {
        $this->setDetailId(
            $data->getDetailId()
        );

        $this->setCheckbox();
        $this->setCheckboxLabels($dirId);

        $this->setWeight($data->getWeight() / 1000);
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
            $this->getAllWeight() + ($data->getWeight() / 1000 * $data->getPartCount() * $sheetCount)
        );
    }

    private function setCheckboxLabels(int $dirId)
    {
        global $db;

        $checkboxQuery = $db->prepare("
            SELECT 
            mpw.attributes
            FROM
            plate_multiPartDetails d
            LEFT JOIN mpw mpw ON mpw.id = d.mpw
            WHERE
            d.did = :did
            AND dirId = :dirId
        ");
        $checkboxQuery->bindValue(":did", $this->getDetailId(), PDO::PARAM_INT);
        $checkboxQuery->bindValue(":dirId", $dirId, PDO::PARAM_INT);
        $checkboxQuery->execute();

        $checkboxQueryData = $checkboxQuery->fetch(PDO::FETCH_ASSOC);
        $checkboxString = $checkboxQueryData["attributes"];

        if (strlen($checkboxString) > 0) {
            $checkboxData = json_decode($checkboxString, true);

            foreach ($checkboxData as $item) {
                $this->checkboxLabels .= _getChecboxText($item) . " ";
            }
        }
    }

    /**
     * @return string|null
     */
    public function getCheckboxLabels()
    {
        return $this->checkboxLabels;
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
            round($this->getMatAll() + $this->getCutAll() + ($this->checkbox->getValue() * $this->getCountAll()), 2)
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
     * @return CheckboxModel
     */
    public function getCheckbox(): CheckboxModel
    {
        return $this->checkbox;
    }

    public function setCheckbox()
    {
        $this->checkbox = new CheckboxModel();
        $this->checkbox->getFromDb($this->getDetailId(), CheckboxModel::TYPE_PLATE_MULTIPART);
        if (isset($_POST["detail_id"])) {
           $this->checkbox->setFromPost($_POST);
        }

        if (isset($_POST["save"])) {
            if (isset($_POST["detail_id"])) {
                if ($_POST["detail_id"] == $this->getDetailId()) {
                    $this->checkbox->saveData($this->getDetailId(), CheckboxModel::TYPE_PLATE_MULTIPART);
                }
            }
        }
    }

    /**
     * @return string|null
     */
    public function getImg()
    {
        global $db;
        $searchImg = $db->prepare("
            SELECT img
            FROM details
            WHERE
            id = :did
        ");
        $searchImg->bindValue(":did", $this->getDetailId(), PDO::PARAM_INT);
        $searchImg->execute();

        $data = $searchImg->fetch();

        if ($data === false) {
            return null;
        }

        return str_replace("/var/www/html", "", $data["img"]);
    }

    /**
     * @param int $dirId
     * @param bool $init
     * @return bool
     */
    public function saveDetailSettings(int $dirId, $init = false)
    {
        $settingsId = $this->getDetailSettingsId($dirId);

        $saveQuery = new sqlBuilder(sqlBuilder::INSERT, "plate_multiPartCostingDetailsSettings");

        if ($settingsId > 0) {
            if ($init == true) {
                return false;
            }

            $saveQuery = new sqlBuilder(sqlBuilder::UPDATE, "plate_multiPartCostingDetailsSettings");
            $saveQuery->addCondition("id = " . $settingsId);
        }

        $saveQuery->bindValue("p_factor", $this->getPriceFactor(), PDO::PARAM_STR);
        $saveQuery->bindValue("directory_id", $dirId, PDO::PARAM_INT);
        $saveQuery->bindValue("detaild_id", $this->getDetailId(), PDO::PARAM_INT);
        $saveQuery->bindValue("price", $this->getSztN(), PDO::PARAM_STR);
        $saveQuery->flush();
        return true;
    }


    private function getDetailSettingsId(int $dirId) {
        global $db;

        $searchQuery = $db->prepare("
            SELECT id
            FROM plate_multiPartCostingDetailsSettings
            WHERE
            directory_id = :dirId
            AND detaild_id = :detailId
        ");
        $searchQuery->bindValue(":dirId", $dirId, PDO::PARAM_INT);
        $searchQuery->bindValue(":detailId", $this->getDetailId(), PDO::PARAM_INT);
        $searchQuery->execute();

        $data = $searchQuery->fetch();

        if ($data === false) {
            return 0;
        }

        return $data["id"];
    }

    /**
     * @return int
     */
    public function getDetailId(): int
    {
        return $this->detailId;
    }

    /**
     * @param int $detailId
     */
    public function setDetailId(int $detailId)
    {
        $this->detailId = $detailId;
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
        return round($this->CutAll, 2);
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
        return round($this->Cut, 2);
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
    public function getKomN()
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
    public function getKomB()
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
    public function getSztN()
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
    public function getSztB()
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