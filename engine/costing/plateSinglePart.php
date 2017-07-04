<?php

/**
 * Created by PhpStorm.
 * User: Dawid
 * Date: 29.03.2017
 * Time: 23:11
 */
class plateSinglePart
{
    /**
     * @var plateSinglePartData $data
     */
    private $data;

    /**
     * @var int $data_id
     */
    private $data_id;

    /**
     * @var plateSinglePartCostingData $costingData
     */
    private $costingData;

    /**
     * @var float
     */
    private $frameAreaValue;

    /**
     * @var float
     */
    private $scrapPrice;

    /**
     * @var float
     */
    private $scrapFactor;

    /**
     * @var float
     */
    private $cutPrice;

    /**
     * @var float
     */
    private $pFactor;

    /**
     * @var float
     */
    private $oTime;

    /**
     * @var float
     */
    private $oCost;

    /**
     * @var float
     */
    private $weightPrice;

    /**
     * @var float
     */
    private $materialPrice;

    /**
     * @var int
     */
    private $costringMaterialId;

    /**
     * @var plateSinglePartCostingAttributes
     */
    private $attributes;

    /**
     * plateSinglePart constructor.
     * @param string $fileUrl
     * @param bool $file
     */
    public function __construct($fileUrl, $file = true)
    {
        $plateData = new plateSinglePartData();
        $this->attributes = new plateSinglePartCostingAttributes();

        $this->data_id = 1;

        if ($file == false) {
            $this->data_id = intval($fileUrl);
            $this->getInputData();
            $this->attributes->getFromDb($this->data_id);
            return true;
        }


        if (($file = fopen($fileUrl, "r")) !== false) {
            while (($data = fgetcsv($file)) !== false) {
                $value = $data[1];

                switch ($data[0]) {
                    case "MatName":
                        $plateData->setMaterialType($value);
                        break;
                    case "thickness":
                        $plateData->setSheetThickness($value);
                        break;
                    case "detailCode":
                        $plateData->setDetalName($value);
                        break;
                    case "SheetSize":
                        $sizeData = explode("X", str_replace(" ", "", $value));
                        $plateData->setSheetSizeX($sizeData[0]);
                        $plateData->setSheetSizeY($sizeData[1]);
                        break;
                    case "SheetCode":
                        $plateData->setSheetCode($value);
                        break;
                    case "SheetName":
                        $plateData->setSheetName($value);
                        break;
                    case "PartCount":
                        $plateData->setPartCount($value);
                        break;
                    case "CPartName":
                        $plateData->setCPartName($value);
                        break;
                    case "UnfoldDimensionSizeX":
                        $plateData->setExtSizeX($value);
                        break;
                    case "UnfoldDimensionSizeY":
                        $plateData->setExtSizeY($value);
                        break;
                    case "AreaWithHoles":
                        $plateData->setRealSizeUnf($value);
                        break;
                    case "AreaWithoutHoles":
                        $plateData->setExtSizeUnf($value);
                        break;
                    case "db_SheetCount":
                        $plateData->setSheetCount($value);
                        break;
                    case "db_LaserMaterialName":
                        $plateData->setLaserMaterialName($value);
                        break;
                    case "db_UsedRatio":
                        $plateData->setUsedRatio($value);
                        break;
                    case "db_CutPathTime":
                        $plateData->setCutPathTime($value);
                        break;
                    case "db_MoveTime":
                        $plateData->setMoveTime($value);
                        break;
                    case "db_SHCutTime":
                        $plateData->setSHCutTime($value);
                        break;
                    case "db_PierceTime":
                        $plateData->setPierceTime($value);
                        break;
                    case "db_Image":
                        $plateData->setDbImage($value);
                        break;
                }
            }
            fclose($file);

            $plateData->calculateSheetUnfold();

            unlink($fileUrl); //todo usunac koment
        } else {
            throw new \Exception("Brak pliku!");
        }

        $this->data = $plateData;
        return true;
    }

    public function getMaterialData()
    {
        global $db;

        $materialQuery = $db->prepare("
				SELECT 
				*
				FROM plate_singlePartCostingMaterial
				WHERE
				plate_singlePartCosting = :id
			");
        $materialQuery->bindValue(":id", $this->data_id, PDO::PARAM_INT);
        $materialQuery->execute();
        $materialData = $materialQuery->fetch();

        if ($this->data_id === 0 || !$materialData) {
            $materialQuery = $db->prepare("
				SELECT
				m.waste as scrapPrice,
				m.cubic as weightPrice,
				m.price as materialPrice,
				s.value as scrapFactor
				FROM
				material m
				LEFT JOIN settings s ON s.id = 5
				WHERE
				m.`name` = :materialType
			");
            $materialQuery->bindValue(":materialType", $this->data->getMaterialType(), PDO::PARAM_STR);
            $materialQuery->execute();
            $materialData = $materialQuery->fetch();

            $cutPriceQuery = $db->query("SELECT `value` as cutPrice FROM settings WHERE id = 1");
            $cutPriceData = $cutPriceQuery->fetch();

            $pFactorQuery = $db->query("SELECT `value` as pFactor FROM settings WHERE id = 2");
            $pFactorData = $pFactorQuery->fetch();

            $oTimeQuery = $db->query("SELECT `value` as oTime FROM settings WHERE id = 3");
            $oTimeData = $oTimeQuery->fetch();

            $oCostQuery = $db->query("SELECT `value` as oCost FROM settings WHERE id = 4");
            $oCostData = $oCostQuery->fetch();

            $this->materialPrice = $materialData["materialPrice"];
            $this->weightPrice = $materialData["weightPrice"];
            $this->scrapFactor = $materialData["scrapFactor"];
            $this->scrapPrice = $materialData["scrapPrice"];
            $this->cutPrice = $cutPriceData["cutPrice"] / 60;
            $this->pFactor = $pFactorData["pFactor"];
            $this->oTime = $oTimeData["oTime"];
            $this->oCost = $oCostData["oCost"];
        } else {
            $this->materialPrice = $materialData["materialPrice"];
            $this->weightPrice = $materialData["weightPrice"];
            $this->scrapFactor = $materialData["scrapFactor"];
            $this->scrapPrice = $materialData["scrapPrice"];
            $this->cutPrice = $materialData["cutPrice"];
            $this->pFactor = $materialData["pFactor"];
            $this->oTime = $materialData["oTime"];
            $this->oCost = $materialData["oCost"];
            $this->costringMaterialId = $materialData["id"];
        }

        return [
            "materialPrice" => $this->materialPrice,
            "weightPrice" => $this->weightPrice,
            "scrapFactor" => $this->scrapFactor,
            "scrapPrice" => $this->scrapPrice,
            "cutPrice" => $this->cutPrice,
            "pFactor" => $this->pFactor,
            "oTime" => $this->oTime,
            "oCost" => $this->oCost,
            "costringMaterialId" => $this->costringMaterialId
        ];
    }

    public function checkMPW()
    {
        global $db;
        $name = $this->data->getDetalName();

        $mpwQuery = $db->prepare("SELECT * FROM `mpw` WHERE code = :detailName");
        $mpwQuery->bindValue(":detailName", $name, PDO::PARAM_STR);
        $mpwQuery->execute();

        $mpwData = $mpwQuery->fetchAll();
        if (count($mpwData) == 0) {
            throw new \Exception("Brak wyceny o nazwie: $name!");
        }
        $mpw = $mpwData[0];

        if ($mpw["pieces"] != $this->data->getPartCount()) {
            throw new \Exception("Liczba detali (" . $this->data->getPartCount() . ") sie nie zgadza: " . $mpw["pieces"]);
        }

        if ($mpw["thickness"] != $this->data->getSheetThickness()) {
            throw new \Exception("Grubosc blachy (" . $this->data->getSheetThickness() . ") jest inna: " . $mpw["thickness"]);
        }
    }

    /**
     * @param int $frameId
     */
    public function updateMpwImg(int $frameId)
    {
        global $db;

        $query = $db->prepare("UPDATE mpw SET frame = :frameId WHERE code = :code");
        $query->bindValue(":frameId", $frameId, PDO::PARAM_INT);
        $query->bindValue(":code", $this->data->getDetalName(), PDO::PARAM_STR);
        $query->execute();
    }

    /**
     * @param int $costingId
     * @throws Exception
     */
    public function saveImage(int $costingId)
    {
        global $data_src, $db;

        $imageInfo = pathinfo(str_replace("\\", "/", $this->data->getDbImage()));

        $costingName = $this->data->getDetalName();
        $newImageName = substr(md5(date("Y-m-d H:i:s")), 0, 6) . "_" . $costingName . "." . $imageInfo["extension"];
        $filePath = $data_src . "temp/" . $imageInfo["basename"];

        if (!file_exists($filePath)) {
            throw new Exception("Plik: $filePath nie istnieje!");
        }

        $newPath = $data_src . "temp/plateData/" . $newImageName;
        if (!file_exists($data_src . 'temp/plateData/')) {
            mkdir($data_src . 'temp/plateData/', 0777, true);
        }
        rename($filePath, $newPath);
        $query = $db->prepare("INSERT INTO `plate_singlePartCosting_image` (`path`, `plate_costingId`, `plate_costingType`, `costing_name`) VALUES (:newPath, :plateCostingId, :plate_costingType, :costingName)");
        $query->bindValue(":newPath", $newPath, PDO::PARAM_STR);
        $query->bindValue(":plateCostingId", $costingId, PDO::PARAM_INT);
        $query->bindValue(":plate_costingType", "singePartCosting", PDO::PARAM_STR);
        $query->bindValue(":costingName", $costingName, PDO::PARAM_STR);
        $query->execute();

        $imgId = $db->lastInsertId();
        $frameId = $this->createFrame($imgId);
        $this->updateMpwImg($frameId);
    }

    /**
     * @return int
     */
    public function saveInputData(): int
    {
        global $db;
        $sqlBuilder = new sqlBuilder("INSERT", "plate_singlePartCosting");

        $sqlBuilder->bindValue("detal_code", $this->data->getDetalName(), PDO::PARAM_STR);
        $sqlBuilder->bindValue("ext_size_X", $this->data->getExtSizeX(), PDO::PARAM_STR);
        $sqlBuilder->bindValue("ext_size_Y", $this->data->getExtSizeY(), PDO::PARAM_STR);
        $sqlBuilder->bindValue("ext_size_unf", $this->data->getExtSizeUnf(), PDO::PARAM_STR);
        $sqlBuilder->bindValue("real_size_unf", $this->data->getRealSizeUnf(), PDO::PARAM_STR);
        $sqlBuilder->bindValue("part_count", $this->data->getPartCount(), PDO::PARAM_INT);
        $sqlBuilder->bindValue("sheet_name", $this->data->getSheetName(), PDO::PARAM_STR);
        $sqlBuilder->bindValue("material_type", $this->data->getMaterialType(), PDO::PARAM_STR);
        $sqlBuilder->bindValue("sheet_thickness", $this->data->getSheetThickness(), PDO::PARAM_STR);
        $sqlBuilder->bindValue("sheet_size_x", $this->data->getSheetSizeX(), PDO::PARAM_STR);
        $sqlBuilder->bindValue("sheet_size_y", $this->data->getSheetSizeY(), PDO::PARAM_STR);
        $sqlBuilder->bindValue("sheet_code", $this->data->getSheetCode(), PDO::PARAM_STR);
        $sqlBuilder->bindValue("sheet_unfold", $this->data->getSheetUnfold(), PDO::PARAM_STR);
        $sqlBuilder->bindValue("cut_path_time", $this->data->getCutPathTime(), PDO::PARAM_STR);
        $sqlBuilder->bindValue("move_time", $this->data->getMoveTime(), PDO::PARAM_STR);
        $sqlBuilder->bindValue("sh_cut_time", $this->data->getSHCutTime(), PDO::PARAM_STR);
        $sqlBuilder->bindValue("pierce_time", $this->data->getPierceTime(), PDO::PARAM_STR);

        $sqlBuilder->flush();
        return $db->lastInsertId();
    }

    public function getInputData()
    {
        $sqlBuilder = new sqlBuilder('SELECT', "plate_singlePartCosting");

        $sqlBuilder->addCondition("id = " . $this->data_id);

        $sqlBuilder->addBind("detal_code");
        $sqlBuilder->addBind("ext_size_X");
        $sqlBuilder->addBind("ext_size_Y");
        $sqlBuilder->addBind("ext_size_unf");
        $sqlBuilder->addBind("real_size_unf");
        $sqlBuilder->addBind("part_count");
        $sqlBuilder->addBind("sheet_name");
        $sqlBuilder->addBind("material_type");
        $sqlBuilder->addBind("sheet_thickness");
        $sqlBuilder->addBind("sheet_size_x");
        $sqlBuilder->addBind("sheet_size_y");
        $sqlBuilder->addBind("sheet_code");
        $sqlBuilder->addBind("sheet_unfold");

        $sqlBuilder->addBind("cut_path_time");
        $sqlBuilder->addBind("move_time");
        $sqlBuilder->addBind("sh_cut_time");
        $sqlBuilder->addBind("pierce_time");

        $queryResult = $sqlBuilder->getData();
        $data = reset($queryResult);
        if ($data != false) {
            $inputData = new plateSinglePartData();
            $inputData->setDetalName($data['detal_code']);
            $inputData->setExtSizeX($data["ext_size_X"]);
            $inputData->setExtSizeY($data["ext_size_Y"]);
            $inputData->setExtSizeUnf($data["ext_size_unf"]);
            $inputData->setRealSizeUnf($data["real_size_unf"]);
            $inputData->setPartCount($data["part_count"]);
            $inputData->setSheetName($data["sheet_name"]);
            $inputData->setMaterialType($data["material_type"]);
            $inputData->setSheetThickness($data["sheet_thickness"]);
            $inputData->setSheetSizeX($data["sheet_size_x"]);
            $inputData->setSheetSizeY($data["sheet_size_y"]);
            $inputData->setSheetCode($data["sheet_code"]);
            $inputData->calculateSheetUnfold();
            $inputData->setCutPathTime($data["cut_path_time"]);
            $inputData->setMoveTime($data["move_time"]);
            $inputData->setSHCutTime($data["sh_cut_time"]);
            $inputData->setPierceTime($data["pierce_time"]);

            $this->data = $inputData;
        } else {
            ob_start();
            var_dump($queryResult);
            throw new \Exception("Brak danych dla wyceny: !" . $this->data_id . ob_get_clean());
        }
    }

    public function saveAttributes()
    {
        $this->attributes->saveAtributes($this->data_id);
    }

    /**
     * @param int $imageId
     * @param string $frameType
     * @return int
     */
    public function createFrame(int $imageId, string $frameType = 'singePartCosting')
    {
        global $db;
        $imageQuery = new sqlBuilder("INSERT", "plate_costingFrame");
        $imageQuery->bindValue("imgId", $imageId, PDO::PARAM_INT);
        $imageQuery->bindValue("type", $frameType, PDO::PARAM_STR);
        $imageQuery->flush();
        return $db->lastInsertId();
    }

    /**
     * @param $areaValue
     */
    public function setFrameData($areaValue)
    {
        $this->frameAreaValue = $areaValue;
    }

    public function getSerializedData()
    {
        $inputData = $this->data;
        $calulateData = $this->costingData;
        $attributes = $this->attributes;

        return [
            "inputData" => $inputData->getData(),
            "outputData" => $calulateData->getData()
        ];
    }

    public function setDataFromForm()
    {
        $input = $_POST;

        $sheetPrice = floatval($input["inputData"]["sheet_price_all"]);
        if ($sheetPrice > 0) {
            $this->data->setSheetPriceAll($sheetPrice);
        }

        $this->materialPrice = floatval($input["settings"]["materialPrice"]);

        $costingData = new plateSinglePartCostingData();
        $costingData->setData($input["outputData"]);

        //AttributeFormWrappper
        $attribute = [];
        foreach ($_POST["attribute"] as $a) {
            $attribute[$a] = [
                "active" => 1,
                "price" => floatval($_POST["a" . $a . "i1"])
            ];
        }

        $this->attributes->setData($attribute);

        $this->costingData = $costingData;
    }

    public function calculate()
    {
        if (get_class($this->costingData) == "plateSinglePartCostingData") {
            $costingData = $this->costingData;
        } else {
            $costingData = new plateSinglePartCostingData();
        }
        $costingData->setDetailsAllUnf($this->data->getPartCount(), $this->data->getExtSizeX(), $this->data->getExtSizeY());
        $costingData->setDetailsAllUnfPer($costingData->getDetailsAllUnf(), $this->data->getSheetUnfold());
        $costingData->setDetailsExtUnf($this->data->getExtSizeX(), $this->data->getExtSizeY(), $this->data->getRealSizeUnf(), $this->data->getPartCount());
        $costingData->setDetailsExtUnfPer($costingData->getDetailsExtUnf(), $this->data->getSheetUnfold());
        $costingData->setDetailsIntUnf($this->data->getRealSizeUnf(), $this->data->getExtSizeUnf(), $this->data->getPartCount());
        $costingData->setDetailsIntUnfPer($costingData->getDetailsIntUnf(), $this->data->getSheetUnfold());
        $costingData->setDetailsRealUnf($this->data->getExtSizeUnf(), $this->data->getPartCount());
        $costingData->setDetailsRealUnfPer($costingData->getDetailsRealUnf(), $this->data->getSheetUnfold());

        $costingData->setRemnantUnfPer($costingData->getDetailsRealUnfPer(), ($this->frameAreaValue / $this->data->getSheetUnfold() * 100));

        $this->getMaterialData();

        if (is_null($this->data->getSheetWeight())) {
            $sheetWeight = floatval($this->data->getSheetSizeX()) * floatval($this->data->getSheetSizeY()) * floatval($this->data->getSheetThickness()) * $this->weightPrice / 1000;
            $this->data->setSheetWeight($sheetWeight);
        }

        $costingData->setRemnantUnf($costingData->getRemnantUnfPer(), $this->data->getSheetWeight());
        $costingData->setRemnantUnfValue($costingData->getRemnantUnf(), $this->scrapPrice, $this->scrapFactor);

        if (is_null($this->data->getSheetPriceAll())) {
            $sheetPrice = $this->data->getSheetWeight() * floatval($this->materialPrice);
            $this->data->setSheetPriceAll($sheetPrice);
        }

        $costingData->setDetailsMatPrice($this->data->getSheetPriceAll(), $costingData->getRemnantUnfValue());

        $costingData->setCutTime($this->data->getCutPathTime(), $this->data->getMoveTime(), $this->data->getSHCutTime(), $this->data->getPierceTime());
        $costingData->setCleanCut($costingData->getCutTime(), $this->cutPrice);
        $costingData->setCutKompN($costingData->getCleanCut(), $this->pFactor, $this->oTime, $this->oCost);
        $costingData->setCutDetalN($costingData->getCutKompN(), $this->data->getPartCount());

        $costingData->setPriceKomN($costingData->getDetailsMatPrice(), $costingData->getCutKompN(), $this->attributes->getPrice($this->data->getPartCount()));
        $costingData->setPriceKomB($costingData->getPriceKomN());
        $costingData->setPriceDetN($costingData->getPriceKomN(), $this->data->getPartCount());
        $costingData->setPriceDetB($costingData->getPriceDetN());

        $this->costingData = $costingData;
    }

    public function getCostingData()
    {
        $sqlBuilder = new sqlBuilder("SELECT", "plate_singlePartCostingCalculate");

        $sqlBuilder->addCondition("plate_singlePartCosting = " . $this->data_id);
        $sqlBuilder->addBind("details_all_unf");
        $sqlBuilder->addBind("details_all_unf_per");
        $sqlBuilder->addBind("details_ext_unf");
        $sqlBuilder->addBind("details_ext_unf_per");
        $sqlBuilder->addBind("details_int_unf");
        $sqlBuilder->addBind("details_int_unf_per");
        $sqlBuilder->addBind("details_real_unf");
        $sqlBuilder->addBind("details_real_unf_per");
        $sqlBuilder->addBind("remnant_unf_per");
        $sqlBuilder->addBind("remnant_unf");
        $sqlBuilder->addBind("remnant_unf_value");
        $sqlBuilder->addBind("detail_mat_price");
        $sqlBuilder->addBind("cut_time");
        $sqlBuilder->addBind("clean_cut");
        $sqlBuilder->addBind("cut_komp_n");
        $sqlBuilder->addBind("cut_detal_n");
        $sqlBuilder->addBind("price_kom_n");
        $sqlBuilder->addBind("price_kom_b");
        $sqlBuilder->addBind("price_det_n");
        $sqlBuilder->addBind("price_det_b");

        $data = $sqlBuilder->getData()[0];

        $this->costingData = new plateSinglePartCostingData();
        $this->costingData->setData($data);
    }

    public function saveCostingData()
    {
        global $db;

        $checkQuery = $db->query("SELECT id FROM plate_singlePartCostingCalculate WHERE plate_singlePartCosting = $this->data_id");
        $result = $checkQuery->fetch();

        $sqlBuilder = new sqlBuilder("INSERT", "plate_singlePartCostingCalculate");
        if ($result) {
            $sqlBuilder = new sqlBuilder("UPDATE", "plate_singlePartCostingCalculate");
            $sqlBuilder->addCondition("plate_singlePartCosting = $this->data_id");
        }

        $sqlBuilder->bindValue("plate_singlePartCosting", $this->data_id, PDO::PARAM_INT);
        $sqlBuilder->bindValue("details_all_unf", $this->costingData->getDetailsAllUnf(), PDO::PARAM_STR);
        $sqlBuilder->bindValue("details_all_unf_per", $this->costingData->getDetailsAllUnfPer(), PDO::PARAM_STR);
        $sqlBuilder->bindValue("details_ext_unf", $this->costingData->getDetailsExtUnf(), PDO::PARAM_STR);
        $sqlBuilder->bindValue("details_ext_unf_per", $this->costingData->getDetailsExtUnfPer(), PDO::PARAM_STR);
        $sqlBuilder->bindValue("details_int_unf", $this->costingData->getDetailsIntUnf(), PDO::PARAM_STR);
        $sqlBuilder->bindValue("details_int_unf_per", $this->costingData->getDetailsIntUnfPer(), PDO::PARAM_STR);
        $sqlBuilder->bindValue("details_real_unf", $this->costingData->getDetailsRealUnf(), PDO::PARAM_STR);
        $sqlBuilder->bindValue("details_real_unf_per", $this->costingData->getDetailsRealUnfPer(), PDO::PARAM_STR);
        $sqlBuilder->bindValue("remnant_unf_per", $this->costingData->getRemnantUnfPer(), PDO::PARAM_STR);
        $sqlBuilder->bindValue("remnant_unf", $this->costingData->getRemnantUnf(), PDO::PARAM_STR);
        $sqlBuilder->bindValue("remnant_unf_value", $this->costingData->getRemnantUnfValue(), PDO::PARAM_STR);
        $sqlBuilder->bindValue("detail_mat_price", $this->costingData->getDetailsMatPrice(), PDO::PARAM_STR);
        $sqlBuilder->bindValue("cut_time", $this->costingData->getCutTime(), PDO::PARAM_STR);
        $sqlBuilder->bindValue("clean_cut", $this->costingData->getCleanCut(), PDO::PARAM_STR);
        $sqlBuilder->bindValue("cut_komp_n", $this->costingData->getCutKompN(), PDO::PARAM_STR);
        $sqlBuilder->bindValue("cut_detal_n", $this->costingData->getCutDetalN(), PDO::PARAM_STR);
        $sqlBuilder->bindValue("price_kom_n", $this->costingData->getPriceKomN(), PDO::PARAM_STR);
        $sqlBuilder->bindValue("price_kom_b", $this->costingData->getPriceKomB(), PDO::PARAM_STR);
        $sqlBuilder->bindValue("price_det_n", $this->costingData->getPriceDetN(), PDO::PARAM_STR);
        $sqlBuilder->bindValue("price_det_b", $this->costingData->getPriceDetB(), PDO::PARAM_STR);

        $sqlBuilder->flush();
        $dataID = $db->lastInsertId();

        //Update MPW
        $updateMPWQuery = $db->prepare("UPDATE mpw SET `type` = :type WHERE code = :code");
        $updateMPWQuery->bindValue(":type", OT::$AUTO_WYCENA_BLACH_SINGLE_WYCENIONE, PDO::PARAM_INT);
        $updateMPWQuery->bindValue(":code", $this->data->getDetalName(), PDO::PARAM_STR);
        $updateMPWQuery->execute();

        //Update Material
        $materialQuery = new sqlBuilder("INSERT", "plate_singlePartCostingMaterial");
        if ($this->costringMaterialId > 0) {
            $materialQuery = new sqlBuilder("UPDATE", "plate_singlePartCostingMaterial");
            $materialQuery->addCondition("id = " . $this->costringMaterialId);
        }

        $materialQuery->bindValue("plate_singlePartCosting", $this->data_id, PDO::PARAM_INT);
        $materialQuery->bindValue("materialPrice", $this->materialPrice, PDO::PARAM_STR);
        $materialQuery->bindValue("weightPrice", $this->weightPrice, PDO::PARAM_STR);
        $materialQuery->bindValue("scrapFactor", $this->scrapFactor, PDO::PARAM_STR);
        $materialQuery->bindValue("scrapPrice", $this->scrapPrice, PDO::PARAM_STR);
        $materialQuery->bindValue("cutPrice", $this->cutPrice, PDO::PARAM_STR);
        $materialQuery->bindValue("pFactor", $this->pFactor, PDO::PARAM_STR);
        $materialQuery->bindValue("oTime", $this->oTime, PDO::PARAM_STR);
        $materialQuery->bindValue("oCost", $this->oCost, PDO::PARAM_STR);
        $materialQuery->flush();

        $this->attributes->saveAtributes($this->data_id);

        return $dataID;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes->serialize();
    }
}

class  plateSinglePartCostingAttributes
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var bool
     */
    private $a1;
    /**
     * @var float
     */
    private $a1_value;
    /**
     * @var bool
     */
    private $a2;
    /**
     * @var float
     */
    private $a2_value;
    /**
     * @var bool
     */
    private $a3;
    /**
     * @var float
     */
    private $a3_value;
    /**
     * @var bool
     */
    private $a4;
    /**
     * @var float
     */
    private $a4_value;
    /**
     * @var bool
     */
    private $a5;
    /**
     * @var float
     */
    private $a5_value;
    /**
     * @var bool
     */
    private $a6;
    /**
     * @var float
     */
    private $a6_value;
    /**
     * @var bool
     */
    private $a7;

    /**
     * @return array
     */
    public function serialize()
    {
        $data = [];

        for ($i = 1; $i <= 7; $i++) {
            $name = "a" . $i;
            $data[$i]["checked"] = $this->$name;

            $valueName = $name . "_value";
            if (property_exists("plateSinglePartCostingAttributes", $valueName)) {
                $data[$i]["value"] = $this->$valueName;
            }
        }

        return $data;
    }

    /**
     * @param int $detailCount
     * @return float|int
     */
    public function getPrice(int $detailCount)
    {
        $price = 0;

        if ($this->isA1()) {
            $price += $this->getA1Value();
        }

        if ($this->isA2()) {
            $price += $this->getA2Value();
        }

        if ($this->isA3()) {
            $price += $this->getA3Value();
        }

        if ($this->isA4()) {
            $price += $this->getA4Value();
        }

        if ($this->isA5()) {
            $price += $this->getA5Value();
        }

        if ($this->isA6()) {
            $price += $this->getA6Value();
        }

        //echo $price . "|" . $detailCount;die;

        return $price * $detailCount;
    }

    /**
     * @param array $data
     */
    public function setData($data)
    {
        foreach ($data as $key => $value) {
            switch ($key) {
                case 1:
                    $this->setA1($value["active"]);
                    $this->setA1Value($value["price"]);
                    break;
                case 2:
                    $this->setA2($value["active"]);
                    $this->setA2Value($value["price"]);
                    break;
                case 3:
                    $this->setA3($value["active"]);
                    $this->setA3Value($value["price"]);
                    break;
                case 4:
                    $this->setA4($value["active"]);
                    $this->setA4Value($value["price"]);
                    break;
                case 5:
                    $this->setA5($value["active"]);
                    $this->setA5Value($value["price"]);
                    break;
                case 6:
                    $this->setA6($value["active"]);
                    $this->setA6Value($value["price"]);
                    break;
                case 7:
                    $this->setA7($value["active"]);
                    break;
            }
        }
    }

    public function saveAtributes($plate_singlePartCosting)
    {
        global $db;
        $type = "INSERT";
        if ($this->getId() > 0) {
            $type = "UPDATE";
        }

        $sqlBuilder = new sqlBuilder($type, "plate_singlePartCostingAttribute");

        $sqlBuilder->bindValue("plate_singlePartCosting", $plate_singlePartCosting, PDO::PARAM_INT);
        $sqlBuilder->bindValue("a1", $this->isA1(), PDO::PARAM_BOOL);
        $sqlBuilder->bindValue("a2", $this->isA2(), PDO::PARAM_BOOL);
        $sqlBuilder->bindValue("a3", $this->isA3(), PDO::PARAM_BOOL);
        $sqlBuilder->bindValue("a4", $this->isA4(), PDO::PARAM_BOOL);
        $sqlBuilder->bindValue("a5", $this->isA5(), PDO::PARAM_BOOL);
        $sqlBuilder->bindValue("a6", $this->isA6(), PDO::PARAM_BOOL);
        $sqlBuilder->bindValue("a7", $this->isA7(), PDO::PARAM_BOOL);

        $sqlBuilder->bindValue("a1_value", $this->getA1Value(), PDO::PARAM_STR);
        $sqlBuilder->bindValue("a2_value", $this->getA2Value(), PDO::PARAM_STR);
        $sqlBuilder->bindValue("a3_value", $this->getA3Value(), PDO::PARAM_STR);
        $sqlBuilder->bindValue("a4_value", $this->getA4Value(), PDO::PARAM_STR);
        $sqlBuilder->bindValue("a5_value", $this->getA5Value(), PDO::PARAM_STR);
        $sqlBuilder->bindValue("a6_value", $this->getA6Value(), PDO::PARAM_STR);

        if ($type == "UPDATE") {
            $sqlBuilder->addCondition("id = " . $this->getId());
        }

        $sqlBuilder->flush();
    }

    /**
     * @param int $costingId
     * @throws Exception
     */
    public function getFromDb($costingId)
    {
        global $db;
        if ($this->getId() < 1) {
            $selectQuery = $db->prepare("SELECT `id` FROM plate_singlePartCostingAttribute WHERE plate_singlePartCosting = :id LIMIT 1");
            $selectQuery->bindValue(":id", $costingId, PDO::PARAM_INT);
            $selectQuery->execute();

            $responseData = $selectQuery->fetch();
            if ($responseData) {
                $this->setId($responseData["id"]);
            } else {
                throw new \Exception("Brak atrybutow!");
            }
        }

        $sqlBuilder = new sqlBuilder("SELECT", "plate_singlePartCostingAttribute");
        $sqlBuilder->addCondition("id = " . $this->getId());

        $sqlBuilder->addBind('a1');
        $sqlBuilder->addBind('a2');
        $sqlBuilder->addBind('a3');
        $sqlBuilder->addBind('a4');
        $sqlBuilder->addBind('a5');
        $sqlBuilder->addBind('a6');
        $sqlBuilder->addBind('a7');

        $sqlBuilder->addBind('a1_value');
        $sqlBuilder->addBind('a2_value');
        $sqlBuilder->addBind('a3_value');
        $sqlBuilder->addBind('a4_value');
        $sqlBuilder->addBind('a5_value');
        $sqlBuilder->addBind('a6_value');

        $result = $sqlBuilder->getData();
        $attributesData = reset($result);

        if ($attributesData != false) {
            $this->setA1($attributesData["a1"]);
            $this->setA2($attributesData["a2"]);
            $this->setA3($attributesData["a3"]);
            $this->setA4($attributesData["a4"]);
            $this->setA5($attributesData["a5"]);
            $this->setA6($attributesData["a6"]);
            $this->setA7($attributesData["a7"]);

            $this->setA1Value($attributesData["a1_value"]);
            $this->setA2Value($attributesData["a2_value"]);
            $this->setA3Value($attributesData["a3_value"]);
            $this->setA4Value($attributesData["a4_value"]);
            $this->setA5Value($attributesData["a5_value"]);
            $this->setA6Value($attributesData["a6_value"]);
        }
    }

    /**
     * @return int|null
     */
    public function getId()
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
     * @return bool
     */
    public function isA1(): bool
    {
        return $this->a1;
    }

    /**
     * @param bool $a1
     */
    public function setA1(bool $a1)
    {
        $this->a1 = $a1;
    }

    /**
     * @return float
     */
    public function getA1Value(): float
    {
        return $this->a1_value;
    }

    /**
     * @param float $a1_value
     */
    public function setA1Value(float $a1_value)
    {
        $this->a1_value = $a1_value;
    }

    /**
     * @return bool
     */
    public function isA2(): bool
    {
        return $this->a2;
    }

    /**
     * @param bool $a2
     */
    public function setA2(bool $a2)
    {
        $this->a2 = $a2;
    }

    /**
     * @return float
     */
    public function getA2Value(): float
    {
        return $this->a2_value;
    }

    /**
     * @param float $a2_value
     */
    public function setA2Value(float $a2_value)
    {
        $this->a2_value = $a2_value;
    }

    /**
     * @return bool
     */
    public function isA3(): bool
    {
        return $this->a3;
    }

    /**
     * @param bool $a3
     */
    public function setA3(bool $a3)
    {
        $this->a3 = $a3;
    }

    /**
     * @return float
     */
    public function getA3Value(): float
    {
        return $this->a3_value;
    }

    /**
     * @param float $a3_value
     */
    public function setA3Value(float $a3_value)
    {
        $this->a3_value = $a3_value;
    }

    /**
     * @return bool
     */
    public function isA4(): bool
    {
        return $this->a4;
    }

    /**
     * @param bool $a4
     */
    public function setA4(bool $a4)
    {
        $this->a4 = $a4;
    }

    /**
     * @return float
     */
    public function getA4Value(): float
    {
        return $this->a4_value;
    }

    /**
     * @param float $a4_value
     */
    public function setA4Value(float $a4_value)
    {
        $this->a4_value = $a4_value;
    }

    /**
     * @return bool
     */
    public function isA5(): bool
    {
        return $this->a5;
    }

    /**
     * @param bool $a5
     */
    public function setA5(bool $a5)
    {
        $this->a5 = $a5;
    }

    /**
     * @return float
     */
    public function getA5Value(): float
    {
        return $this->a5_value;
    }

    /**
     * @param float $a5_value
     */
    public function setA5Value(float $a5_value)
    {
        $this->a5_value = $a5_value;
    }

    /**
     * @return bool
     */
    public function isA6(): bool
    {
        return $this->a6;
    }

    /**
     * @param bool $a6
     */
    public function setA6(bool $a6)
    {
        $this->a6 = $a6;
    }

    /**
     * @return float
     */
    public function getA6Value(): float
    {
        return $this->a6_value;
    }

    /**
     * @param float $a6_value
     */
    public function setA6Value(float $a6_value)
    {
        $this->a6_value = $a6_value;
    }

    /**
     * @return bool
     */
    public function isA7(): bool
    {
        return $this->a7;
    }

    /**
     * @param bool $a7
     */
    public function setA7(bool $a7)
    {
        $this->a7 = $a7;
    }
}

class plateSinglePartCostingData
{
    private $details_all_unf;
    private $details_all_unf_per;
    private $details_ext_unf;
    private $details_ext_unf_per;
    private $details_int_unf;
    private $details_int_unf_per;
    private $details_real_unf;
    private $details_real_unf_per;
    private $remnant_unf_per;
    private $remnant_unf;
    private $remnant_unf_value;
    private $details_mat_price;
    private $cut_time;
    private $clean_cut;
    private $cut_komp_n;
    private $cut_detal_n;
    private $price_kom_n;
    private $price_kom_b;
    private $price_det_n;
    private $price_det_b;

    /**
     * @param array $data
     */
    public function setData(array $data)
    {
        foreach ($data as $key => $value) {
            $this->$key = floatval($value);
        }
    }

    /**
     * @return array
     */
    public function getData()
    {
        return get_object_vars($this);
    }

    /**
     * @return mixed
     */
    public function getPriceDetB()
    {
        return $this->price_det_b;
    }

    /**
     * @param $price_det_n
     */
    public function setPriceDetB($price_det_n)
    {
        if ($this->getPriceDetB() == 0) {
            $this->price_det_b = $price_det_n * 1.23;
        }
    }

    /**
     * @return mixed
     */
    public function getPriceDetN()
    {
        return $this->price_det_n;
    }

    /**
     * @param $price_kom_n
     * @param $partCount
     */
    public function setPriceDetN($price_kom_n, $partCount)
    {
        if ($this->getPriceDetN() == 0) {
            $this->price_det_n = $price_kom_n / intval($partCount);
        }
    }

    /**
     * @return mixed
     */
    public function getPriceKomB()
    {
        return $this->price_kom_b;
    }

    /**
     * @param $price_kom_n
     */
    public function setPriceKomB($price_kom_n)
    {
        if ($this->getPriceKomB() == 0) {
            $this->price_kom_b = $price_kom_n * 1.23;
        }
    }

    /**
     * @return mixed
     */
    public function getPriceKomN()
    {
        return $this->price_kom_n;
    }

    /**
     * @param $details_mat_price
     * @param $cut_komp_n
     * @param $checkbox
     */
    public function setPriceKomN($details_mat_price, $cut_komp_n, $checkbox)
    {
        if ($this->getPriceKomN() == 0) {
            $this->price_kom_n = $details_mat_price + $cut_komp_n + $checkbox;
        }
    }

    /**
     * @return mixed
     */
    public function getCutDetalN()
    {
        return $this->cut_detal_n;
    }

    /**
     * @param $cut_komp_n
     * @param $qty
     */
    public function setCutDetalN($cut_komp_n, $qty)
    {
        if ($this->getCutDetalN() == 0) {
            $this->cut_detal_n = $cut_komp_n / intval($qty);
        }
    }

    /**
     * @return mixed
     */
    public function getCutKompN()
    {
        return $this->cut_komp_n;
    }

    /**
     * @param $clean_cut
     * @param $p_factor
     * @param $czas_przelad
     * @param $cena_przeladunku
     */
    public function setCutKompN($clean_cut, $p_factor, $czas_przelad, $cena_przeladunku)
    {
        if ($this->getCutKompN() == 0) {
            $this->cut_komp_n = $clean_cut * $p_factor + ($czas_przelad * $cena_przeladunku);
        }
    }

    /**
     * @return mixed
     */
    public function getCleanCut()
    {
        return $this->clean_cut;
    }

    /**
     * @param $cut_time
     * @param $cutPrice
     */
    public function setCleanCut($cut_time, $cutPrice)
    {
        if ($this->getCleanCut() == 0) {
            $this->clean_cut = $cut_time * $cutPrice;
        }
    }

    /**
     * @return mixed
     */
    public function getCutTime()
    {
        return $this->cut_time;
    }

    /**
     * @param $CutPathTime
     * @param $MoveTime
     * @param $SHCutTime
     * @param $PierceTime
     */
    public function setCutTime($CutPathTime, $MoveTime, $SHCutTime, $PierceTime)
    {
        if ($this->getCutTime() == 0) {
            $this->cut_time = globalTools::calculate_second($CutPathTime) + globalTools::calculate_second($MoveTime) + globalTools::calculate_second($SHCutTime) + globalTools::calculate_second($PierceTime);
        }
    }

    /**
     * @return mixed
     */
    public function getDetailsMatPrice()
    {
        return $this->details_mat_price;
    }

    /**
     * @param $sheet_price_all
     * @param $remnant_unf_value_sum
     */
    public function setDetailsMatPrice($sheet_price_all, $remnant_unf_value)
    {
        if ($this->getDetailsMatPrice() == 0) {
            $this->details_mat_price = (floatval($sheet_price_all) - $remnant_unf_value);
        }
    }

    /**
     * @return mixed
     */
    public function getRemnantUnfValue()
    {
        return $this->remnant_unf_value;
    }

    /**
     * @param $remnant_unf
     * @param $scrapPrice
     * @param $scrapFactor
     */
    public function setRemnantUnfValue($remnant_unf, $scrapPrice, $scrapFactor)
    {
        if ($this->getRemnantUnfValue() == 0) {
            $this->remnant_unf_value = $remnant_unf * floatval($scrapPrice) * floatval($scrapFactor);
        }
    }

    /**
     * @return mixed
     */
    public function getRemnantUnf()
    {
        return $this->remnant_unf;
    }

    /**
     * @param $remnant_unf_per
     * @param $sheet_weight
     */
    public function setRemnantUnf($remnant_unf_per, $sheet_weight)
    {
        if ($this->getRemnantUnf() == 0) {
            $this->remnant_unf = $remnant_unf_per * floatval($sheet_weight) / 100;
        }
    }

    /**
     * @return mixed
     */
    public function getRemnantUnfPer()
    {
        return $this->remnant_unf_per;
    }

    /**
     * @param $details_real_unf_per
     * @param $ramka_per
     */
    public function setRemnantUnfPer($details_real_unf_per, $ramka_per)
    {
        if ($this->getRemnantUnfPer() == 0) {
            $this->remnant_unf_per = 100 - $details_real_unf_per + floatval($ramka_per);
        }
    }

    /**
     * @return mixed
     */
    public function getDetailsRealUnfPer()
    {
        return $this->details_real_unf_per;
    }

    /**
     * @param $details_real_unf
     * @param $sheet_unfold
     */
    public function setDetailsRealUnfPer($details_real_unf, $sheet_unfold)
    {
        if ($this->getDetailsRealUnfPer() == 0) {
            $this->details_real_unf_per = $details_real_unf / floatval($sheet_unfold) * 100;
        }
    }

    /**
     * @return mixed
     */
    public function getDetailsRealUnf()
    {
        return $this->details_real_unf;
    }

    /**
     * @param $AreaWithOutHoles
     * @param $partCount
     */
    public function setDetailsRealUnf($AreaWithOutHoles, $partCount)
    {
        if ($this->getDetailsRealUnf() == 0) {
            $this->details_real_unf = floatval($AreaWithOutHoles) * intval($partCount);
        }
    }

    /**
     * @return mixed
     */
    public function getDetailsIntUnfPer()
    {
        return $this->details_int_unf_per;
    }

    /**
     * @param $details_int_unf
     * @param $sheet_unfold
     */
    public function setDetailsIntUnfPer($details_int_unf, $sheet_unfold)
    {
        if ($this->getDetailsIntUnfPer() == 0) {
            $this->details_int_unf_per = $details_int_unf / floatval($sheet_unfold) * 100;
        }
    }

    /**
     * @return mixed
     */
    public function getDetailsIntUnf()
    {
        return $this->details_int_unf;
    }

    /**
     * @param $AreaWithHoles
     * @param $AreaWithOutHoles
     * @param $PartCount
     */
    public function setDetailsIntUnf($AreaWithHoles, $AreaWithOutHoles, $PartCount)
    {
        if ($this->getDetailsIntUnf() == 0) {
            $this->details_int_unf = (floatval($AreaWithOutHoles) - floatval($AreaWithHoles)) * intval($PartCount);
        }
    }

    /**
     * @return mixed
     */
    public function getDetailsExtUnfPer()
    {
        return $this->details_ext_unf_per;
    }

    /**
     * @param $details_ext_unf
     * @param $sheet_unfold
     */
    public function setDetailsExtUnfPer($details_ext_unf, $sheet_unfold)
    {
        if ($this->getDetailsExtUnfPer() == 0) {
            $this->details_ext_unf_per = $details_ext_unf / floatval($sheet_unfold) * 100;
        }
    }

    /**
     * @return mixed
     */
    public function getDetailsExtUnf()
    {
        return $this->details_ext_unf;
    }

    /**
     * @param $dimensionSizeX
     * @param $dimensionSizeY
     * @param $areaWithHoles
     * @param $partCount
     */
    public function setDetailsExtUnf($dimensionSizeX, $dimensionSizeY, $areaWithHoles, $partCount)
    {
        if ($this->getDetailsExtUnf() == 0) {
            $this->details_ext_unf = ((floatval($dimensionSizeX) * floatval($dimensionSizeY)) - floatval($areaWithHoles)) * intval($partCount);
        }
    }

    /**
     * @return mixed
     */
    public function getDetailsAllUnfPer()
    {
        return $this->details_all_unf_per;
    }

    /**
     * @param $allUnf
     * @param $sheetUnfold
     */
    public function setDetailsAllUnfPer($allUnf, $sheetUnfold)
    {
        if ($this->getDetailsAllUnfPer() == 0) {
            $this->details_all_unf_per = floatval($allUnf) / floatval($sheetUnfold) * 100;
        }
    }

    /**
     * @return mixed
     */
    public function getDetailsAllUnf()
    {
        return $this->details_all_unf;
    }

    /**
     * @param $partCount
     * @param $dimensionSizeX
     * @param $dimensionSizeY
     */
    public function setDetailsAllUnf($partCount, $dimensionSizeX, $dimensionSizeY)
    {
        if ($this->getDetailsAllUnf() == 0) {
            $this->details_all_unf = intval($partCount) * floatval($dimensionSizeX) * floatval($dimensionSizeY);
        }
    }
}

class plateSinglePartData
{
    private $detal_name;
    private $ext_size_X;
    private $ext_size_Y;
    private $ext_size_unf;
    private $real_size_unf;
    private $part_count;
    private $sheet_name;
    private $material_type;
    private $sheet_thickness;
    private $sheet_size_x;
    private $sheet_size_y;
    private $sheet_code;
    private $sheet_unfold;
    private $sheet_price_mm;
    private $sheet_price_all;
    private $sheet_weight;
    private $c_part_name;
    private $sheet_count;
    private $laser_material_name;
    private $used_ratio;
    private $cut_path_time;
    private $move_time;
    private $SHCutTime;
    private $db_image;
    private $pierce_time;

    /**
     * @return array
     */
    public function getData()
    {
        return get_object_vars($this);
    }

    /**
     * @return mixed
     */
    public function getPierceTime()
    {
        return $this->pierce_time;
    }

    /**
     * @param mixed $pierce_time
     */
    public function setPierceTime($pierce_time)
    {
        $this->pierce_time = $pierce_time;
    }

    /**
     * @return mixed
     */
    public function getSHCutTime()
    {
        return $this->SHCutTime;
    }

    /**
     * @param mixed $SHCutTime
     */
    public function setSHCutTime($SHCutTime)
    {
        $this->SHCutTime = $SHCutTime;
    }

    /**
     * @return mixed
     */
    public function getDbImage()
    {
        return $this->db_image;
    }

    /**
     * @param mixed $db_image
     */
    public function setDbImage($db_image)
    {
        $this->db_image = $db_image;
    }

    /**
     * @return mixed
     */
    public function getMoveTime()
    {
        return $this->move_time;
    }

    /**
     * @param mixed $move_time
     */
    public function setMoveTime($move_time)
    {
        $this->move_time = $move_time;
    }

    /**
     * @return mixed
     */
    public function getCutPathTime()
    {
        return $this->cut_path_time;
    }

    /**
     * @param mixed $cut_path_time
     */
    public function setCutPathTime($cut_path_time)
    {
        $this->cut_path_time = $cut_path_time;
    }

    /**
     * @return mixed
     */
    public function getUsedRatio()
    {
        return $this->used_ratio;
    }

    /**
     * @param mixed $used_ratio
     */
    public function setUsedRatio($used_ratio)
    {
        $this->used_ratio = floatval($used_ratio);
    }

    /**
     * @return mixed
     */
    public function getLaserMaterialName()
    {
        return $this->laser_material_name;
    }

    /**
     * @param mixed $laser_material_name
     */
    public function setLaserMaterialName($laser_material_name)
    {
        $this->laser_material_name = $laser_material_name;
    }

    /**
     * @return mixed
     */
    public function getSheetCount()
    {
        return $this->sheet_count;
    }

    /**
     * @param mixed $sheet_count
     */
    public function setSheetCount($sheet_count)
    {
        $this->sheet_count = floatval($sheet_count);
    }

    /**
     * @return mixed
     */
    public function getCPartName()
    {
        return $this->c_part_name;
    }

    /**
     * @param mixed $c_part_name
     */
    public function setCPartName($c_part_name)
    {
        $this->c_part_name = $c_part_name;
    }

    /**
     * @return mixed
     */
    public function getDetalName()
    {
        return $this->detal_name;
    }

    /**
     * @param mixed $detal_name
     */
    public function setDetalName($detal_name)
    {
        $this->detal_name = $detal_name;
    }

    /**
     * @return mixed
     */
    public function getMaterialType()
    {
        return $this->material_type;
    }

    /**
     * @param mixed $material_name
     */
    public function setMaterialType($material_name)
    {
        $this->material_type = str_replace([
            "0", "1", "2", "3", "4", "5", "6", "7", "8", "9", ".", ","
        ], "", $material_name);
    }

    /**
     * @return mixed
     */
    public function getExtSizeX()
    {
        return $this->ext_size_X;
    }

    /**
     * @param mixed $ext_size_X
     */
    public function setExtSizeX($ext_size_X)
    {
        $this->ext_size_X = floatval($ext_size_X);
    }

    /**
     * @return mixed
     */
    public function getExtSizeY()
    {
        return $this->ext_size_Y;
    }

    /**
     * @param mixed $ext_size_Y
     */
    public function setExtSizeY($ext_size_Y)
    {
        $this->ext_size_Y = $ext_size_Y;
    }

    /**
     * @return mixed
     */
    public function getExtSizeUnf()
    {
        return $this->ext_size_unf;
    }

    /**
     * @param mixed $ext_size_unf
     */
    public function setExtSizeUnf($ext_size_unf)
    {
        $this->ext_size_unf = floatval($ext_size_unf);
    }

    /**
     * @return mixed
     */
    public function getRealSizeUnf()
    {
        return $this->real_size_unf;
    }

    /**
     * @param mixed $real_size_unf
     */
    public function setRealSizeUnf($real_size_unf)
    {
        $this->real_size_unf = floatval($real_size_unf);
    }

    /**
     * @return mixed
     */
    public function getPartCount()
    {
        return $this->part_count;
    }

    /**
     * @param mixed $part_count
     */
    public function setPartCount($part_count)
    {
        $this->part_count = intval($part_count);
    }

    /**
     * @return mixed
     */
    public function getSheetName()
    {
        return $this->sheet_name;
    }

    /**
     * @param mixed $sheet_name
     */
    public function setSheetName($sheet_name)
    {
        $this->sheet_name = $sheet_name;
    }

    /**
     * @return mixed
     */
    public function getSheetThickness()
    {
        return $this->sheet_thickness;
    }

    /**
     * @param mixed $sheet_thickness
     */
    public function setSheetThickness($sheet_thickness)
    {
        $this->sheet_thickness = floatval($sheet_thickness);
    }

    /**
     * @return mixed
     */
    public function getSheetSizeX()
    {
        return $this->sheet_size_x;
    }

    /**
     * @param mixed $sheet_size_x
     */
    public function setSheetSizeX($sheet_size_x)
    {
        $this->sheet_size_x = floatval($sheet_size_x);
    }

    /**
     * @return mixed
     */
    public function getSheetSizeY()
    {
        return $this->sheet_size_y;
    }

    /**
     * @param mixed $sheet_size_y
     */
    public function setSheetSizeY($sheet_size_y)
    {
        $this->sheet_size_y = floatval($sheet_size_y);
    }

    /**
     * @return mixed
     */
    public function getSheetCode()
    {
        return $this->sheet_code;
    }

    /**
     * @param mixed $sheet_code
     */
    public function setSheetCode($sheet_code)
    {
        $this->sheet_code = $sheet_code;
    }

    /**
     * @return mixed
     */
    public function getSheetUnfold()
    {
        return $this->sheet_unfold;
    }

    public function calculateSheetUnfold()
    {
        if ($this->getSheetSizeX() == 0 || $this->getSheetSizeY() == 0) {
            throw new Exception("SheetSizeX lub SheetSizeY jest rowne zero!");
        }

        $this->sheet_unfold = $this->getSheetSizeX() * $this->getSheetSizeY();
    }

    /**
     * @return mixed
     */
    public function getSheetPriceMm()
    {
        return $this->sheet_price_mm;
    }

    /**
     * @param mixed $sheet_price_mm
     */
    public function setSheetPriceMm($sheet_price_mm)
    {
        $this->sheet_price_mm = $sheet_price_mm;
    }

    /**
     * @return mixed
     */
    public function getSheetPriceAll()
    {
        return $this->sheet_price_all;
    }

    /**
     * @param mixed $sheet_price_all
     */
    public function setSheetPriceAll($sheet_price_all)
    {
        $this->sheet_price_all = $sheet_price_all;
    }

    /**
     * @return mixed
     */
    public function getSheetWeight()
    {
        return $this->sheet_weight;
    }

    /**
     * @param mixed $sheet_weight
     */
    public function setSheetWeight($sheet_weight)
    {
        $this->sheet_weight = $sheet_weight;
    }
}