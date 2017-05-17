<?php

/**
 * Created by PhpStorm.
 * User: Dawid
 * Date: 02.05.2017
 * Time: 21:01
 */
class router
{
    public static function getRouting()
    {
    	return [
    		"1" => "PMAction",
            "2" => "PImgExistAction",
			"3" => "SaveImageAction",
			"4" => "CopyDbAction",
			"5" => "UploadMaterialTypeAction",
			"check_costing_line" => "CheckCostingLineAction",
			"tube_single" => "CostingTubeSingleAction",
			"add_plate_costing_single" => "AddSingleCostingAction",
			"plate_warehouse_sync_data" => "GetSyncDataAction",
			"plate_warehouse_sync_respond" => "SetSyncedAction",
			"plate_warehouse_sync_new" => "SyncFromMDBAction"
        ];
    }
}