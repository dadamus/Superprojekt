<?php

/**
 * Created by PhpStorm.
 * User: dawid
 * Date: 05.07.2017
 * Time: 20:49
 */

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