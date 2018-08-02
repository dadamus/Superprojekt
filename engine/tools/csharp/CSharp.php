<?php

/**
 * Created by PhpStorm.
 * User: Dawid
 * Date: 24.04.2017
 * Time: 19:30
 */
class CSharp
{
    /**
     * @return string
     */
    public static function href_RefreshPlateWarehouse() : string
    {
        return "abl-sync:sync";
    }

    /**
     * @return string
     */
    public static function generatePlateData() : string
    {
        $plateWarehouse = new PlateWarehouse();
        return $plateWarehouse->getSyncData();
    }

	/**
	 * @param array $SheetCode
	 */
    public static function setPlateSynced(array $SheetCode)
	{
		$plateWarehouse = new PlateWarehouse();
		$plateWarehouse->setSynced($SheetCode);
	}
}