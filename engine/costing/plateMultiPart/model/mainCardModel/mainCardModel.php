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

    /** @var bool  */
    private $blocked = false;

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
     * @param float $priceFactor
     * @param bool $count
     */
    public function make(float $priceFactor, bool $count = false)
    {
        global $db;

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

                    if (isset($_POST["p_factor"]) || $count) {
                        $detailData->setPriceFactor($_POST["p_factor"]);
                        $part->setPFactor($_POST["p_factor"]);
                    } else {
                        $detailData->setPriceFactor($part->getPFactor());
                    }

                    if ($part->getPFactor() == 0) {
                        $part->setPFactor($priceFactor);
                        $detailData->setPriceFactor($priceFactor);
                    }

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

                //Tylko pierwszy zapis bedzie dzial
                $detail->saveDetailSettings($plateMultiPart->getDirId(), true);

                if (isset($_POST["detail_id"]) && isset($_POST["save"])) {
                    if ($_POST["detail_id"] == $detail->getDetailId()) {
                        $detail->saveDetailSettings($plateMultiPart->getDirId());
                    }
                }
            }
        }

        if (isset($_POST["save"])) {
            if (isset($_POST["program_id"])) {
                $program = $plateMultiPart->getProgramById($_POST["program_id"]);
                $program->saveSettings();
            }
        }

        //Check if not blocked
        $checkBlockedQuery = $db->prepare("
            SELECT 
            mpw.type
            FROM
            plate_multiPartDetails mpd
            LEFT JOIN mpw mpw ON mpw.id = mpd.mpw
            WHERE
            mpd.dirId = :dirId
            LIMIT 1
        ");
        $checkBlockedQuery->bindValue(":dirId", $plateMultiPart->getDirId(), PDO::PARAM_INT);
        $checkBlockedQuery->execute();

        $checkBlockedData = $checkBlockedQuery->fetch(PDO::FETCH_ASSOC);
        if ($checkBlockedData["type"] >= OT::AUTO_WYCENA_BLACH_MULTI_ZABLOKOWANE) {
            $this->blocked = true;
        }
    }

    /**
     * @return bool
     */
    public function isBlocked(): bool
    {
        return $this->blocked;
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