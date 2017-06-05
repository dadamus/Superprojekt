<?php

/**
 * Created by PhpStorm.
 * User: Dawid
 * Date: 23.05.2017
 * Time: 22:08
 */
class T_Material
{
	/**
	 * @return string
	 */
	public function getSyncData(): string
	{
		$sqlBuilder = new sqlBuilder("SELECT", "T_material");
		$sqlBuilder->addBind("*", "");
		$sqlBuilder->addCondition("`synced` = '0'");
		$warehouses = $sqlBuilder->getData();
		return json_encode($warehouses);
	}

	/**
	 * @param array $MaterialName
	 */
	public function setSynced(array $MaterialName)
	{
		$sqlBuilder = new sqlBuilder("UPDATE", "T_material");
		$sqlBuilder->bindValue("synced", 1, PDO::PARAM_INT);
		$sqlBuilder->addCondition("`MaterialName` in (" . implode(", ", array_map("globalTools::add_quotes", $MaterialName)) . ")");
		$sqlBuilder->flush();
	}

	public function parseMaterial(array $material): string
	{
		return implode(", ", array_map("globalTools::add_quotes", $material));
	}
}