<?php

/**
 * Created by PhpStorm.
 * User: Dawid
 * Date: 16.04.2017
 * Time: 17:59
 */
class sqlBuilder
{
    public  static $INSERT = "INSERT";

    /**
     * @var PDOStatement $_query
     */
    protected $_query;

    private $query;
    private $from;
    private $join;
    private $values;
    private $values_data;
    private $bind;
    private $where;
    private $type;

    /**
     * sqlBuilder constructor.
     * @param $type
     * @param $table
     */
    public function __construct($type, $table)
    {
        switch ($this->type)
        {
            case "INSERT":
                $this->from = "INSERT INTO `$table` ";
                break;
        }
        $this->type = $type;
    }

    public function generateQuery()
    {
        switch ($this->type) {
            case "INSERT":
                return $this->generateInsertQuery();
                break;
        }

        return false;
    }

    public function flush()
    {
        $this->generateQuery();
        $this->prepareQuery();
        $this->executeQuery();
    }

    public function prepareQuery()
    {
        switch ($this->type) {
            case "INSERT":
                return $this->prepareInsertQuery();
                break;
        }

        return false;
    }

    public function executeQuery()
    {
        return $this->_query->execute();
    }

    private function prepareInsertQuery()
    {
        global $db;

        $this->_query = $db->prepare($this->query);
        foreach($this->values_data as $key => $value) {
            $this->_query->bindValue(":$key", $value["value"], $value["type"]);
        }

        return true;
    }

    private function generateInsertQuery()
    {
        $this->query = $this->from . '(' . $this->bind . ') VALUES (' . $this->values . ')';
        return true;
    }

    /**
     * @param $name
     * @param $value
     * @param $type
     */
    public function bindValue($name, $value, $type)
    {
        $this->addValueData($name, $value, $type);
        $this->addBind($name);

        if ($this->values != null) {
            $this->values .= ", ";
        }

        $this->values .= "':$name'";
    }

    /**
     * @param $name
     */
    private function addBind($name)
    {
        if ($this->bind != null) {
            $this->bind .= ", ";
        }
        $this->bind .= "`$name`";
    }

    /**
     * @param $name
     * @param $value
     * @param $type
     */
    private function addValueData($name, $value, $type)
    {
        if (isset($this->values_data[$name])) {
            throw new Exception("Juz zbindowalem! $name");
        }
        $this->values_data[$name] = [
            "value" => $value,
            "type" => $type
        ];
    }
}