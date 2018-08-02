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
	 * @param string $columnKey
	 */
	public function addUpdate($data, $columnKey)
	{
		$dataSet = "";
		foreach ($data as $name => $value) {
			if (is_int($name)) {
				continue;
			}

			if (strlen($dataSet) > 0) {
				$dataSet .= ",";
			}

			$mark = "'";
			if (is_numeric($value)) {
				$mark = "";
			}
			$dataSet .= "`$name` = " . $mark . "$value" . $mark;
		}

		$this->update_id[] = $data[$columnKey];
		$this->update .= "UPDATE :TableName SET $dataSet WHERE $columnKey = '" . $data[$columnKey] . "';";
	}

	/**
	 * @param array $data
	 * @param string $columnKey
	 */
	public function addInsert($data, $columnKey)
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

			$mark = "'";
			if (is_numeric($value)) {
				$mark = "";
			}
			$dataValues .= $mark.$value.$mark;
		}

		$this->insert_id[] = $data[$columnKey];
		$this->insert .= "INSERT INTO :TableName ($dataHeader) VALUES ($dataValues);";
	}

	/**
	 * @param array $data
	 * @param string $columnKey
	 */
	public function addDelete($data, $columnKey)
	{
		$this->delete_id[] = $data[$columnKey];
		$mark = "'";
		if (is_numeric($data[$columnKey])) {
			$mark = "";
		}
		$this->delete .= "DELETE FROM :TableName WHERE `" . $columnKey . "` = " . $mark . $data[$columnKey] . $mark .";";
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