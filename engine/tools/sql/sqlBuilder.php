<?php

/**
 * Created by PhpStorm.
 * User: Dawid
 * Date: 16.04.2017
 * Time: 17:59
 */
class sqlBuilder
{
    const INSERT = "INSERT";
    const SELECT = "SELECT";
    const UPDATE = "UPDATE";

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
     * @param string $type
     * @param string $table
     */
    public function __construct($type = self::INSERT, $table = null)
    {
        switch ($type) {
            case "INSERT":
                $this->from = "INSERT INTO `$table` ";
                break;
            case "UPDATE":
                $this->from = "UPDATE `$table` ";
                break;
            case "SELECT":
                $this->from = "FROM `$table`";
                break;
        }
        $this->type = $type;
    }

    /**
     * @param string $table
     * @return sqlBuilder
     */
    public static function createInsert(string $table): sqlBuilder
    {
        return new self(self::INSERT, $table);
    }

    /**
     * @param string $table
     * @return sqlBuilder
     */
    public static function createUpdate(string $table): sqlBuilder
    {
        return new self(self::UPDATE, $table);
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param $query
     * @return PDOStatement
     */
    public function Query($query)
    {
        global $db;
        return $db->query($query);
    }

    public function generateQuery()
    {
        switch ($this->type) {
            case "INSERT":
                return $this->generateInsertQuery();
                break;
            case "UPDATE":
                return $this->generateUpdateQuery();
                break;
            case "SELECT":
                return $this->generateSelectQuery();
                break;
        }

        return false;
    }

    private function generateUpdateQuery()
    {
        $updateBidString = "";

        foreach ($this->values_data as $key => $value) {
            if (strlen($updateBidString) > 0) {
                $updateBidString .= ", ";
            }
            $updateBidString .= "`$key` = :$key";
        }

//        if (strlen($this->where) < 1) {
//            throw new \Exception("Update without where!");
//        }

        $this->query = $this->from . " SET " . $updateBidString . " WHERE " . $this->where;
        return true;
    }

    public function addCondition($sql)
    {
        $this->where = $sql;
    }

    private function generateSelectQuery()
    {
        $this->query = "SELECT " . $this->bind . " " . $this->from . "  " . $this->join . " WHERE " . $this->where;
        return true;
    }

    public function flush()
    {
        $this->generateQuery();
        $this->prepareQuery();
        $this->executeQuery();
    }

    /**
     * @return array
     */
    public function getData()
    {
        $this->flush();
        return $this->_query->fetchAll();
    }

    public function prepareQuery()
    {
        switch ($this->type) {
            case "INSERT":
                return $this->prepareInsertQuery();
                break;
            case "UPDATE":
                return $this->prepareInsertQuery();
                break;
            case "SELECT":
                return $this->prepareSelectQuery();
                break;
        }

        return false;
    }

    private function prepareSelectQuery()
    {
        global $db;

        $this->_query = $db->prepare($this->query);
        return true;
    }

    public function executeQuery()
    {
        return $this->_query->execute();
    }

    private function prepareInsertQuery()
    {
        global $db;

        $this->_query = $db->prepare($this->query);

        foreach ($this->values_data as $key => $value) {
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
     * @return $this
     */
    public function bindValue($name, $value, $type)
    {
        $this->addValueData($name, $value, $type);
        $this->addBind($name);

        if ($this->values != null) {
            $this->values .= ", ";
        }

        $this->values .= ":$name";
        return $this;
    }

    /**
     * @param $name
     */
    public function addBind($name, $mark = "`")
    {
        if ($this->bind != null) {
            $this->bind .= ", ";
        }
        $this->bind .= $mark . $name . $mark;
    }

    /**
     * @param array $names
     */
    public function addBinds($names)
    {
        if (is_array($names)) {
            foreach ($names as $name) {
                $this->addBind($name);
            }
        }
    }

    /**
     * @param string $name
     * @param mixed $value
     * @param $type
     * @throws Exception
     */
    private function addValueData($name, $value, $type)
    {
        if (isset($this->values_data[$name])) {
            throw new Exception("Juz zbindowalem! $name");
        }
        $this->values_data[$name] = array(
            "value" => $value,
            "type" => $type
        );
    }
}