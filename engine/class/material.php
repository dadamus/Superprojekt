<?php

class Material {

    public $name = array();
    public $weight = array();
    public $price = array();
    public $length = array();
    public $cubic = array();
    public $waste = array();

    public function __construct() {
        global $db;
        $query = $db->prepare("SELECT * FROM `material`");
        $query->execute();

        foreach ($query as $row) { // GET data from base
            $this->name[$row["id"]] = $row["name"];
            $this->weight[$row["id"]] = $row["weight"];
            $this->cubic[$row["id"]] = $row["cubic"];
            $this->price[$row["id"]] = $row["price"];
            $this->length[$row["id"]] = $row["length"];
            $this->waste[$row["id"]] = $row["waste"];
        }
    }

}