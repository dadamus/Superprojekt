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
    const TYPE_PLATE_MULTIPART = "plateMultiPart";

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
     * @param bool $checked
     * @param float $szt
     * @param float $kom
     * @throws Exception
     */
    public function setValue(int $id, bool $checked, float $szt, float $kom)
    {
        if (!isset($this->attributes[$id])) {
            throw new \Exception("Brak atrybutu: " . $id);
        }

        $this->attributes[$id]["szt"] = $szt;
        $this->attributes[$id]["kom"] = $kom;
        $this->attributes[$id]["checked"] = $checked;
    }

    public function __construct()
    {
        $this->setViewPath(dirname(__DIR__) . "/view/checkbox/");
    }

    /**
     * @param int $partCount
     * @return string
     */
    public function renderAttributes(int $partCount): string
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
            "attributes" => $this->attributes,
            "partCount" => $partCount
        ]);
    }

    /**
     * @param int $costingId
     * @param string $costingType
     * @return bool
     */
    public function getFromDb(int $costingId, string $costingType)
    {
        global $db;

        $searchQuery = $db->prepare("
            SELECT * 
            FROM costingAttributes
            WHERE item_id = :costingId
            AND `type` = :costingType
        ");
        $searchQuery->bindValue(":costingId", $costingId, PDO::PARAM_INT);
        $searchQuery->bindValue(":costingType", $costingType, PDO::PARAM_STR);
        $searchQuery->execute();

        $data = $searchQuery->fetch();
        if ($data === false) {
            return false;
        }

        $this->attributes[1]["checked"] = $data["a1"];
        $this->attributes[1]["szt"] = $data["a1_value"];
        $this->attributes[2]["checked"] = $data["a2"];
        $this->attributes[2]["szt"] = $data["a2_value"];
        $this->attributes[3]["checked"] = $data["a3"];
        $this->attributes[3]["szt"] = $data["a3_value"];
        $this->attributes[4]["checked"] = $data["a4"];
        $this->attributes[4]["szt"] = $data["a4_value"];
        $this->attributes[5]["checked"] = $data["a5"];
        $this->attributes[5]["szt"] = $data["a5_value"];
        $this->attributes[6]["checked"] = $data["a6"];
        $this->attributes[6]["szt"] = $data["a6_value"];
        $this->attributes[6]["checked"] = $data["a7"];
        return true;
    }

    /**
     * @param int $costingId
     * @param string $costingType
     */
    public function saveData(int $costingId, string $costingType)
    {
        $attributesId = $this->getId($costingId, $costingType);
        $SQLBuilder = new sqlBuilder(sqlBuilder::INSERT, "costingAttributes");

        if ($attributesId > 0) {
            $SQLBuilder = new sqlBuilder(sqlBuilder::UPDATE, "costingAttributes");
            $SQLBuilder->addCondition("id = " . $attributesId);
        }

        $SQLBuilder->bindValue("item_id", $costingId, PDO::PARAM_INT);
        $SQLBuilder->bindValue("type", $costingType, PDO::PARAM_STR);

        foreach ($this->attributes as $key => $attribute) {
            $SQLBuilder->bindValue("a" . $key, (isset($attribute["checked"])) ? $attribute["checked"]:0, PDO::PARAM_INT);
            if (!isset($attribute["not-inputs"])) {
                $SQLBuilder->bindValue("a" . $key . "_value", $attribute["szt"], PDO::PARAM_STR);
            }
        }

        $SQLBuilder->flush();
    }

    private function getId(int $costingId, string $costingType): int
    {
        global $db;

        $searchQuery = $db->prepare("
            SELECT id
            FROM costingAttributes
            WHERE item_id = :costingId
            AND `type` = :costingType
        ");
        $searchQuery->bindValue(":costingId", $costingId, PDO::PARAM_INT);
        $searchQuery->bindValue(":costingType", $costingType, PDO::PARAM_STR);
        $searchQuery->execute();
        $data = $searchQuery->fetch();

        if ($data === false) {
            return 0;
        }

        return $data["id"];
    }

    public function getValue(): float
    {
        /** @var float $value */
        $value = 0;

        foreach ($this->attributes as $attribute) {
            if (isset($attribute["szt"]) && isset($attribute["checked"])) {
                if ($attribute["checked"] > 0) {
                    $value += $attribute["szt"];
                }
            }
        }

        return $value;
    }

    public function setFromPost(array $data)
    {
        for ($i = 1; $i <= count($this->attributes); $i++) {
            $this->attributes[$i]["checked"] = 0;
            if (isset($data["a" . $i . "i1"])) {
                $this->attributes[$i]["szt"] = floatval($data["a" . $i . "i1"]);
            }
        }

        if (isset($data["attribute"])) {
            foreach ($data["attribute"] as $a) {
                $this->attributes[$a]["checked"] = true;
            }
        }
    }
}