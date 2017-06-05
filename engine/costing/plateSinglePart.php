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
     * plateSinglePart constructor.
     * @param string $fileUrl
     * @param bool $file
     */
    public function __construct($fileUrl, $file = true)
    {
        $plateData = new plateSinglePartData();

        $this->data_id = 1;

        if ($file = false)
		{
			$this->data_id = intval($fileUrl);
			$this->data = $this->getInputData();
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

            //unlink($fileUrl); //todo usunac koment
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
			m.waste as scrapPrice,
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

		$cutPriceQuery = $db->query("SELECT value as cutPrice FROM settings WHERE id = 1");
		$cutPriceData = $cutPriceQuery->fetch();

		$pFactorQuery = $db->query("SELECT value as pFactor FROM settings WHERE id = 2");
		$pFactorData = $pFactorQuery->fetch();

		$oTimeQuery = $db->query("SELECT value as oTime FROM settings WHERE id = 3");
		$oTimeData = $oTimeQuery->fetch();

		$oCostQuery = $db->query("SELECT value as oCost FROM settings WHERE id = 4");
		$oCostData = $oCostQuery->fetch();

		$this->scrapFactor = $materialData["scrapFactor"];
		$this->scrapPrice = $materialData["scrapPrice"];
		$this->cutPrice = $cutPriceData["cutPrice"];
		$this->pFactor = $pFactorData["pFactor"];
		$this->oTime = $oTimeData["oTime"];
		$this->oCost = $oCostData["oCost"];
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

        $sqlBuilder->bindValue("detal_code",        	$this->data->getDetalName(),        PDO::PARAM_STR);
        $sqlBuilder->bindValue("ext_size_X",        	$this->data->getExtSizeX(),         PDO::PARAM_STR);
        $sqlBuilder->bindValue("ext_size_Y",        	$this->data->getExtSizeY(),         PDO::PARAM_STR);
        $sqlBuilder->bindValue("ext_size_unf",      	$this->data->getExtSizeUnf(),       PDO::PARAM_STR);
        $sqlBuilder->bindValue("real_size_unf",     	$this->data->getRealSizeUnf(),      PDO::PARAM_STR);
        $sqlBuilder->bindValue("part_count",        	$this->data->getPartCount(),        PDO::PARAM_INT);
        $sqlBuilder->bindValue("sheet_name",        	$this->data->getSheetName(),        PDO::PARAM_STR);
        $sqlBuilder->bindValue("material_type",     	$this->data->getMaterialType(),     PDO::PARAM_STR);
        $sqlBuilder->bindValue("sheet_thickness",   	$this->data->getSheetThickness(),   PDO::PARAM_STR);
        $sqlBuilder->bindValue("sheet_size_x",      	$this->data->getSheetSizeX(),       PDO::PARAM_STR);
        $sqlBuilder->bindValue("sheet_size_y",      	$this->data->getSheetSizeY(),       PDO::PARAM_STR);
        $sqlBuilder->bindValue("sheet_code",        	$this->data->getSheetCode(),        PDO::PARAM_STR);
        $sqlBuilder->bindValue("sheet_unfold",      	$this->data->getSheetUnfold(),      PDO::PARAM_STR);
		$sqlBuilder->bindValue("cut_path_time",		$this->data->getCutPathTime(),		PDO::PARAM_STR);
		$sqlBuilder->bindValue("move_time",			$this->data->getMoveTime(),			PDO::PARAM_STR);
		$sqlBuilder->bindValue("sh_cut_time",			$this->data->getSHCutTime(),		PDO::PARAM_STR);
		$sqlBuilder->bindValue("pierce_time", 		$this->data->getPierceTime(),		PDO::PARAM_STR);

        $sqlBuilder->flush();
        return $db->lastInsertId();
    }

    public function getInputData()
	{
		$sqlBuilder = new sqlBuilder('SELECT', "plate_singlePartCosting");

		$sqlBuilder->addCondition("id = ".$this->data_id);

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
		if (count($queryResult) >= 1) {
			$data = $queryResult[0];

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

	/**
	 * @param int $imageId
	 * @param string $frameType
	 * @return int
	 */
	public function createFrame(int $imageId, string $frameType = 'singePartCosting')
	{
		global $db;
		$imageQuery = new sqlBuilder("INSERT", "plate_costingFrame");
		$imageQuery->bindValue("imageId", $imageId, PDO::PARAM_INT);
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

    public function calculate()
	{
		$costingData = new plateSinglePartCostingData();
		$costingData->setDetailsAllUnf($this->data->getPartCount(), $this->data->getExtSizeX(), $this->data->getExtSizeY());
		$costingData->setDetailsAllUnfPer($costingData->getDetailsAllUnf(), $this->data->getSheetUnfold());
		$costingData->setDetailsExtUnf($this->data->getExtSizeX(), $this->data->getExtSizeY(), $this->data->getRealSizeUnf(), $this->data->getPartCount());
		$costingData->setDetailsExtUnfPer($costingData->setDetailsExtUnf(), $this->data->getSheetUnfold());
		$costingData->setDetailsIntUnf($this->data->getRealSizeUnf(), $this->data->getExtSizeUnf(), $this->data->getPartCount());
		$costingData->setDetailsIntUnfPer($costingData->getDetailsIntUnf(), $this->data->getSheetUnfold());
		$costingData->setDetailsRealUnf($this->data->getExtSizeUnf(), $this->data->getPartCount());
		$costingData->setDetailsRealUnfPer($costingData->getDetailsRealUnf(), $this->data->getSheetUnfold());

		$costingData->setRemnantUnfPer($costingData->getDetailsRealUnfPer(), $this->frameAreaValue);
		$costingData->setRemnantUnf($costingData->getRemnantUnfPer(), $this->data->getSheetWeight());

		$this->getMaterialData();
		$costingData->setRemnantUnfValue($costingData->getRemnantUnf(), $this->scrapPrice, $this->scrapFactor);

		$costingData->setDetailMatPrice($this->data->getSheetPriceAll(), $costingData->getRemnantUnfValue(), $this->data->getPartCount());

		$costingData->setCutTime($this->data->getCutPathTime(), $this->data->getMoveTime(), $this->data->getSHCutTime(), $this->data->getPierceTime());
		$costingData->setCleanCut($costingData->getCutTime(), $this->cutPrice);
		$costingData->setCutKompN($costingData->getCleanCut(), $this->pFactor, $this->oTime, $this->oCost);
		$costingData->setCutDetalN($costingData->getCutKompN(), $this->data->getPartCount());
		$costingData->setPriceKomN($costingData->getDetailMatPrice(), $costingData->getCutKompN(), 0);//Todo checkbox
		$costingData->setPriceKomB($costingData->getPriceKomN());
		$costingData->setPriceDetN($costingData->getPriceKomN(), $this->data->getPartCount());
		$costingData->setPriceDetB($costingData->getPriceDetN());

		$this->costingData = $costingData;
	}

	public function saveCostingData()
	{
		global $db;
		$sqlBuilder = new sqlBuilder("INSERT", "plate_singlePartCostingCalculate");
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
		$sqlBuilder->bindValue("detail_mat_price", $this->costingData->getDetailMatPrice(), PDO::PARAM_STR);
		$sqlBuilder->bindValue("cut_time", $this->costingData->getCutTime(), PDO::PARAM_STR);
		$sqlBuilder->bindValue("clean_cut", $this->costingData->getCleanCut(), PDO::PARAM_STR);
		$sqlBuilder->bindValue("cut_komp_n", $this->costingData->getCutKompN(), PDO::PARAM_STR);
		$sqlBuilder->bindValue("cut_detal_n", $this->costingData->getCutDetalN(), PDO::PARAM_STR);
		$sqlBuilder->bindValue("price_kom_n", $this->costingData->getPriceKomN(), PDO::PARAM_STR);
		$sqlBuilder->bindValue("price_kom_b", $this->costingData->getPriceKomB(), PDO::PARAM_STR);
		$sqlBuilder->bindValue("price_det_n", $this->costingData->getPriceDetN(), PDO::PARAM_STR);
		$sqlBuilder->bindValue("price_det_b", $this->costingData->getPriceDetB(), PDO::PARAM_STR);

		$sqlBuilder->flush();
		return $db->lastInsertId();
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
	private $detail_mat_price;
	private $cut_time;
	private $clean_cut;
	private $cut_komp_n;
	private $cut_detal_n;
	private $price_kom_n;
	private $price_kom_b;
	private $price_det_n;
	private $price_det_b;

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
		$this->price_det_b = $price_det_n * 1.23;
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
		$this->price_det_n = $price_kom_n / intval($partCount);
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
		$this->price_kom_b = $price_kom_n * 1.23;
	}

	/**
	 * @return mixed
	 */
	public function getPriceKomN()
	{
		return $this->price_kom_n;
	}

	/**
	 * @param $detail_mat_price
	 * @param $cut_komp_n
	 * @param $checkbox
	 */
	public function setPriceKomN($detail_mat_price, $cut_komp_n, $checkbox)
	{
		$this->price_kom_n = $detail_mat_price + $cut_komp_n + $checkbox;
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
		$this->cut_detal_n = $cut_komp_n * intval($qty);
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
		$this->cut_komp_n = $clean_cut * $p_factor + ($czas_przelad * $cena_przeladunku);
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
		$this->clean_cut = $cut_time * $cutPrice;
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
		$this->cut_time = globalTools::calculate_second($CutPathTime) + globalTools::calculate_second($MoveTime) + globalTools::calculate_second($SHCutTime) + globalTools::calculate_second($PierceTime);
	}

	/**
	 * @return mixed
	 */
	public function getDetailMatPrice()
	{
		return $this->detail_mat_price;
	}

	/**
	 * @param $sheet_price_all
	 * @param $remnant_unf_value_sum
	 * @param $PartCount
	 */
	public function setDetailMatPrice($sheet_price_all, $remnant_unf_value, $PartCount)
	{
		$this->detail_mat_price = floatval($sheet_price_all) - $remnant_unf_value / intval($PartCount);
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
		$this->remnant_unf_value = $remnant_unf * floatval($scrapPrice) * floatval($scrapFactor);
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
	public function setRemnantUnf($remnant_unf_per , $sheet_weight)
	{
		$this->remnant_unf = $remnant_unf_per * floatval($sheet_weight) / 100;
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
		$this->remnant_unf_per = 100 - $details_real_unf_per + floatval($ramka_per);
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
		$this->details_real_unf_per = $details_real_unf / floatval($sheet_unfold) * 100;
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
		$this->details_real_unf = floatval($AreaWithOutHoles) * intval($partCount);
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
		$this->details_int_unf_per = $details_int_unf / floatval($sheet_unfold) * 100;
	}

	/**
	 * @return mixed
	 */
	public function getDetailsIntUnf()
	{
		return $this->detals_int_unf;
	}

	/**
	 * @param $AreaWithHoles
	 * @param $AreaWithOutHoles
	 * @param $PartCount
	 */
	public function setDetailsIntUnf($AreaWithHoles, $AreaWithOutHoles, $PartCount)
	{
		$this->details_int_unf = (floatval($AreaWithHoles) - floatval($AreaWithOutHoles)) * intval($PartCount);
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
		$this->details_ext_unf_per = $details_ext_unf / floatval($sheet_unfold) * 100;
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
		$this->details_ext_unf = (floatval($dimensionSizeX) * floatval($dimensionSizeY) - floatval($areaWithHoles)) * intval($partCount);
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
		$this->details_all_unf_per = floatval($allUnf) / floatval($sheetUnfold) * 100;
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
		$this->details_all_unf = intval($partCount) * floatval($dimensionSizeX) * floatval($dimensionSizeY);
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
        $this->cut_path_time = floatval($cut_path_time);
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