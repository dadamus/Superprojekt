<?php
/**
 * Created by PhpStorm.
 * User: dawidadamus
 * Date: 25.08.2017
 * Time: 13:05
 */

require_once dirname(__DIR__) . "/mainController.php";

class CheckboxModel extends mainController
{
    private $attributes = [
        1 => [
            "small" => "B",
            "name" => "Gięcie",
        ],
        2 => [
            "small" => "Pr",
            "name" => "Projekt"
        ],
        3 => [
            "small" => "W",
            "name" => "Spawanie"
        ],
        4 => [
            "small" => "P",
            "name" => "Malowanie"
        ],
        5 => [
            "small" => "Z",
            "name" => "Ocynkowanie"
        ],
        6 => [
            "small" => "R",
            "name" => "Gwintowanie"
        ],
        7 => [
            "small" => "Cc",
            "name" => "Common Cut",
            "not-inputs" => true
        ],
    ];

    /**
     * @param int $id
     * @param float $szt
     * @param float $kom
     * @throws Exception
     */
    public function setValue(int $id, float $szt, float $kom)
    {
        if (!isset($this->attributes[$id])) {
            throw new \Exception("Brak atrybutu: " . $id);
        }

        $this->attributes[$id]["szt"] = $szt;
        $this->attributes[$id]["kom"] = $kom;
    }

    public function __construct()
    {
        $this->setViewPath(dirname(__DIR__) . "/view/checkbox/");
    }

    public function renderAttributes(): string
    {
        foreach ($this->attributes as $key => $row) {
            if (!isset($this->attributes[$key]["szt"])) {
                $this->attributes[$key]["szt"] = 0;
            }

            if (!isset($this->attributes[$key]["kom"])) {
                $this->attributes[$key]["kom"] = 0;
            }
        }

        return $this->render("checkboxView.php", [
            "attributes" => $this->attributes
        ]);
    }
}