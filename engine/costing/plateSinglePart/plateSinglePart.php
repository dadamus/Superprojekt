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

