<?php

/**
 * Created by PhpStorm.
 * User: Dawid
 * Date: 24.04.2017
 * Time: 22:11
 */
class PlateWarehouse
{
	/**
	 * @return string
	 */
	public function getSyncData(): string
	{
		$sqlBuilder = new sqlBuilder("SELECT", "plate_warehouse");
		$sqlBuilder->addBind("*", "");
		$sqlBuilder->addCondition("`synced` = '0'");
		$warehouses = $sqlBuilder->getData();
		return json_encode($warehouses);
	}

	/**
	 * @param array $SheetCode
	 * @param bool $insertCreateDate
	 * @param bool $insertModifyDate
	 */
	public function setSynced(array $SheetCode, bool $insertCreateDate = false, bool $insertModifyDate = false)
	{
		$sqlBuilder = new sqlBuilder("UPDATE", "plate_warehouse");
		$sqlBuilder->bindValue("synced", 1, PDO::PARAM_INT);

		if ($insertCreateDate) {
			$sqlBuilder->bindValue("createDate", date("Y-m-d H:i:s"), PDO::PARAM_STR);
		}
		if ($insertModifyDate)
		{
			$sqlBuilder->bindValue("modifyDate", date("Y-m-d H:i:s"), PDO::PARAM_STR);
		}

		$sqlBuilder->addCondition("`SheetCode` in (" . implode(", ", array_map("globalTools::add_quotes", $SheetCode)) . ")");
		$sqlBuilder->flush();
	}

	public function parsePlate(array $plate): string
	{
		return implode(", ", array_map("globalTools::add_quotes", $plate));
	}
}