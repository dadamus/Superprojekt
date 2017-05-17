<?php

/**
 * Created by PhpStorm.
 * User: Dawid
 * Date: 08.05.2017
 * Time: 18:28
 */
class PlateSyncResponse
{
	/** @var string $tableName */
	private $tableName;
	/** @var string $update */
	private $update;
	/** @var array $update_id */
	private $update_id = [];
	/** @var string $insert */
	private $insert;
	/** @var array $insert_id */
	private $insert_id = [];
	/** @var  string $delete */
	private $delete;
	/** @var array $delete_id */
	private $delete_id = [];

	/**
	 * @param array $data
	 */
	public function addUpdate($data)
	{
		$dataSet = "";
		foreach ($data as $name => $value) {
			if (is_int($name)) {
				continue;
			}

			if (strlen($dataSet) > 0) {
				$dataSet .= ",";
			}
			$dataSet .= "`$name` = '$value'";
		}

		$this->update_id[] = $data["SheetCode"];
		$this->update .= "UPDATE :TableName SET $dataSet WHERE SheetCode = '" . $data["SheetCode"] . "';";
	}

	/**
	 * @param array $data
	 */
	public function addInsert($data)
	{
		$dataHeader = "";
		$dataValues = "";

		foreach ($data as $name => $value) {
			if (intval($name) > 0) {
				continue;
			}

			if (strlen($dataHeader) > 0) {
				$dataHeader .= ",";
				$dataValues .= ",";
			}
			$dataHeader .= "`$name`";
			$dataValues .= "'$value'";
		}

		$this->insert_id[] = $data["SheetCode"];
		$this->insert .= "INSERT INTO :TableName ($dataHeader) VALUES ($dataValues);";
	}

	/**
	 * @param string $data
	 */
	public function addDelete($data)
	{
		$this->delete_id[] = $data["SheetCode"];
		$this->delete .= "DELETE FROM :TableName WHERE `SheetCode` = '" . $data["SheetCode"] . "';";
	}

	/**
	 * @param string $tableName
	 * @return string
	 */
	public function getUpdate($tableName = ":TableName"): string
	{
		$responseQuery = substr($this->update, 0, -1);
		return str_replace(":TableName", $tableName, $responseQuery);
	}

	/**
	 * @param string $tableName
	 * @return string
	 */
	public function getInsert($tableName = ":TableName"): string
	{
		$responseQuery = substr($this->insert, 0, -1);
		return str_replace(":TableName", $tableName, $responseQuery);
	}

	/**
	 * @param string $tableName
	 * @return string
	 */
	public function getDelete($tableName = ":TableName"): string
	{
		$responseQuery = substr($this->delete, 0, -1);
		return str_replace(":TableName", $tableName, $responseQuery);
	}

	/**
	 * @return array
	 */
	public function getUpdateId()
	{
		return $this->update_id;
	}

	/**
	 * @return array
	 */
	public function getInsertId()
	{
		return $this->insert_id;
	}
}