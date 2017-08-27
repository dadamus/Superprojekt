<?php
/**
 * Created by PhpStorm.
 * User: dawidadamus
 * Date: 24.08.2017
 * Time: 12:06
 */

require_once dirname(__FILE__) . "/mainCardProjectModel.php";
require_once dirname(__FILE__) . "/mainCardDetailModel.php";

class mainCardClientModel
{
    /** @var  int */
    private $clientId;

    /** @var  string */
    private $clientName;

    /** @var  mainCardDetailModel[] */
    private $details;

    /** @var  MaterialData[] */
    private $materials;

    /**
     * @param int $detailId
     * @return mainCardProjectModel
     * @throws Exception
     */
    public function getByDetailId(int $detailId)
    {
        global $db;
        $searchQuery = $db->prepare("
            SELECT 
            p.id as pid,
            p.name as project_name,
            p.nr as project_nr,
            c.name as client_name,
            c.id as cid,
            d.src as detail_name
            FROM
            details d
            LEFT JOIN projects p ON p.id = d.pid
            LEFT JOIN clients c ON c.id = p.cid
            WHERE 
            d.id = :did
        ");
        $searchQuery->bindValue(":did", $detailId, PDO::PARAM_INT);
        $searchQuery->execute();

        $clientData = $searchQuery->fetch();

        if ($clientData === false) {
            throw new \Exception("Brak detalu: " . $detailId);
        }

        $project = new mainCardProjectModel($clientData["pid"], $clientData["project_nr"] ,$clientData["project_name"], $clientData["detail_name"]);

        $this->setClientId($clientData["cid"]);
        $this->setClientName($clientData["client_name"]);

        return $project;
    }

    /**
     * @param int $detailId
     * @param mainCardDetailModel $detail
     */
    public function addDetail(int $detailId, mainCardDetailModel $detail)
    {
        $this->details[$detailId] = $detail;
    }

    /**
     * @param int $detailId
     * @return bool|mainCardDetailModel
     */
    public function getDetail(int $detailId)
    {
        if (isset($this->details[$detailId])) {
            return $this->details[$detailId];
        }
        return false;
    }

    /**
     * @return mainCardDetailModel[]
     */
    public function getDetails(): array
    {
        return $this->details;
    }

    /**
     * @return int
     */
    public function getClientId(): int
    {
        return $this->clientId;
    }

    /**
     * @param int $clientId
     */
    public function setClientId(int $clientId)
    {
        $this->clientId = $clientId;
    }

    /**
     * @return string
     */
    public function getClientName(): string
    {
        return $this->clientName;
    }

    /**
     * @param string $clientName
     */
    public function setClientName(string $clientName)
    {
        $this->clientName = $clientName;
    }

    /**
     * @return MaterialData[]
     */
    public function getMaterials(): array
    {
        return $this->materials;
    }

    /**
     * @param MaterialData $materialData
     * @param ProgramData $programData
     */
    public function addMaterial(MaterialData $materialData, ProgramData $programData)
    {
        if (!isset($this->materials[$materialData->getSheetCode()])) {
            $this->materials[$materialData->getSheetCode()] = $materialData;
        }

        $this->materials[$materialData->getSheetCode()]->addProgram($programData);
    }

    /**
     * @param MaterialData[] $materials
     */
    public function setMaterials(array $materials)
    {
        $this->materials = $materials;
    }
}