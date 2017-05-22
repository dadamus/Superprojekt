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
     * plateSinglePart constructor.
     * @param string $data
     */
    public function __construct($data, $file = true)
    {
        $plateData = new plateSinglePartData();

        $this->data_id = 1;
        $this->data = $this->getInputData();
        die;

        if ($file = false)
		{
			$this->data_id = intval($data);
			$this->data = $this->getInputData();
			return true;
		}

        if (($file = fopen($data, "r")) !== false) {
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
                    case "CutPathTime":
                        $plateData->setCutPathTime($value);
                        break;
                    case "MoveTime":
                        $plateData->setMoveTime($value);
                        break;
                    case "db_Image":
                        $plateData->setDbImage($value);
                        break;
                }
            }
            fclose($file);

            $plateData->calculateSheetUnfold();

            unlink($data); //todo usunac koment
        } else {
            throw new \Exception("Brak pliku!");
        }

        $this->data = $plateData;
        return true;
    }


    /**
     * @throws Exception
     */
    public function saveImage()
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
        rename($filePath, $newPath);
        $query = $db->prepare("INSERT INTO `plate_singlePartCosting_image` (`path`, `costing_name`) VALUES (:newPath, :costingName)");
        $query->bindValue(":newPath", $newPath, PDO::PARAM_STR);
        $query->bindValue(":costingName", $costingName, PDO::PARAM_STR);
        $query->execute();
    }

    public function saveInputData()
    {
        $sqlBuilder = new sqlBuilder("INSERT", "plate_singlePartCosting");

        $sqlBuilder->bindValue("detal_code",        $this->data->getDetalName(),        PDO::PARAM_STR);
        $sqlBuilder->bindValue("ext_size_X",        $this->data->getExtSizeX(),         PDO::PARAM_STR);
        $sqlBuilder->bindValue("ext_size_Y",        $this->data->getExtSizeY(),         PDO::PARAM_STR);
        $sqlBuilder->bindValue("ext_size_unf",      $this->data->getExtSizeUnf(),       PDO::PARAM_STR);
        $sqlBuilder->bindValue("real_size_unf",     $this->data->getRealSizeUnf(),      PDO::PARAM_STR);
        $sqlBuilder->bindValue("part_count",        $this->data->getPartCount(),        PDO::PARAM_INT);
        $sqlBuilder->bindValue("sheet_name",        $this->data->getSheetName(),        PDO::PARAM_STR);
        $sqlBuilder->bindValue("material_type",     $this->data->getMaterialType(),     PDO::PARAM_STR);
        $sqlBuilder->bindValue("sheet_thickness",   $this->data->getSheetThickness(),   PDO::PARAM_STR);
        $sqlBuilder->bindValue("sheet_size_x",      $this->data->getSheetSizeX(),       PDO::PARAM_STR);
        $sqlBuilder->bindValue("sheet_size_y",      $this->data->getSheetSizeY(),       PDO::PARAM_STR);
        $sqlBuilder->bindValue("sheet_code",        $this->data->getSheetCode(),        PDO::PARAM_STR);
        $sqlBuilder->bindValue("sheet_unfold",      $this->data->getSheetUnfold(),      PDO::PARAM_STR);

        $sqlBuilder->flush();
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

			$this->data = $inputData;
		} else {
			throw new \Exception("Brak danych dla wyceny: !" . $this->data_id);
		}
	}

	public function getMaterialData()
	{
		if (is_empty($this->data)) {
			throw new Exception("Brak danych wejsciowych!");
		}

		$sheetCode = $this->data->getSheetCode();

	}

    public function calculate()
	{

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
    private $db_image;

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