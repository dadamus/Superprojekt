<?php
/**
 * Created by PhpStorm.
 * User: dawidadamus
 * Date: 24.08.2017
 * Time: 11:57
 */

require_once dirname(__DIR__) . "/../plateMultiPart.php";
require_once dirname(__FILE__) . "/mainCardClientModel.php";
require_once dirname(__FILE__) . "/mainCardDetailModel.php";
require_once dirname(__DIR__) . "/MaterialData.php";

class mainCardModel
{
    /** @var  PlateMultiPart */
    private $plateMultiPart;

    /** @var  mainCardClientModel[] */
    private $clientModels;

    /**
     * mainCardModel constructor.
     * @param PlateMultiPart $plateMultiPart
     */
    public function __construct(PlateMultiPart $plateMultiPart)
    {
        $this->plateMultiPart = $plateMultiPart;
    }

    /**
     * Funkcja przerabia PlateMultiPart na mainCardModel
     * @param int $priceFactor
     */
    public function make(int $priceFactor)
    {
        $plateMultiPart = $this->getPlateMultiPart();
        $programs = $plateMultiPart->getPrograms();

        foreach ($programs as $program) {
            $parts = $program->getParts();
            foreach ($parts as $part) {
                $detailId = $part->getDetailId();

                $client = new mainCardClientModel();
                $project = $client->getByDetailId($detailId);

                if (isset($this->clientModels[$client->getClientId()])) {
                    $client = $this->clientModels[$client->getClientId()];
                } else {
                    $this->clientModels[$client->getClientId()] = $client;
                }

                $checkDetail = $client->getDetail($detailId);
                $addDetail = false;

                if (
                    $checkDetail != false
                    &&
                    $checkDetail->getProject()->getId() == $project->getId()
                ) {
                    $detailData = $checkDetail;
                } else {
                    $detailData = new mainCardDetailModel();
                    $detailData->setProject($project);
                    $detailData->setMaterial($program->getMaterial());
                    $detailData->setPriceFactor($priceFactor);
                    $addDetail = true;
                }

                $detailData->Make($part, $program->getSheetCount());
                $client->addMaterial($program->getMaterial(), $program);

                if ($addDetail) {
                    $client->addDetail($detailId, $detailData);
                }
            }

        }

        foreach ($this->clientModels as $client) {
            foreach ($client->getDetails() as $detail) {
                $detail->Calculate();
            }
        }

        $this->plateMultiPart = null;
    }

    /**
     * @return PlateMultiPart
     */

    public function getPlateMultiPart()
    {
        return $this->plateMultiPart;
    }

    /**
     * @param mainCardClientModel $mainCardClientModel
     */
    public function addClient(mainCardClientModel $mainCardClientModel)
    {
        $this->clientModels[$mainCardClientModel->getClientId()] = $mainCardClientModel;
    }

    /**
     * @return mainCardClientModel[]
     */
    public function getClients(): array
    {
        return $this->clientModels;
    }
}