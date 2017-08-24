<?php
/**
 * Created by PhpStorm.
 * User: dawidadamus
 * Date: 15.08.2017
 * Time: 22:24
 */

require_once dirname(__DIR__) . "/../mainController.php";
require_once dirname(__FILE__) . "/plateMultiPart.php";
require_once dirname(__FILE__) . "/model/mainCardModel/mainCardModel.php";

/**
 * Class plateMultiPartController
 */
class plateMultiPartController extends mainController
{
    /**
     * plateMultiPartController constructor.
     */
    public function __construct()
    {
        $this->setViewPath(dirname(__FILE__) . '/view/costing/');
    }

    /**
     * @param int $directoryId
     * @param int $programId
     * @return string
     */
    public function viewMainCard(int $directoryId, int $programId = 0): string
    {
        global $db;
        $frameSetup = false;
        $alerts = [];
        $missingFrames = 0;

        $plateMultiPart = new PlateMultiPart();
        $mainCardModel = new mainCardModel($plateMultiPart);
        $plateMultiPart->MakeFromDirId($directoryId);

        $programs = $plateMultiPart->getPrograms();
        foreach ($programs as $program) {
            $frame = $program->getFrame();

            if ($frame->getValue() <= 0) {
                $alerts[] = [
                    "type" => "warning",
                    "message" => "Program " . $program->getSheetName() . " nie posiada określonej ramki!"
                ];
                $missingFrames++;
                $frameSetup = $plateMultiPart;
            }
        }


        if (isset($_POST["dots"])) { //Zapis gotowej ramki
            $this->SaveFrameData($plateMultiPart, $programId);
            return true;
        }


        $frameDiv = null;
        if ($frameSetup !== false) {
            $frameDiv = $this->render("ImgFrameView.php", [
                "multiPart" => $frameSetup,
            ]);
        }

        if ($frameDiv == null) {
            $plateMultiPart->Calculate();
            var_dump($plateMultiPart);
            $mainCardModel->make($plateMultiPart->getRemnantFactor());
            var_dump($mainCardModel);
        }

        return $this->render("mainView.php", [
            "multiPart" => $plateMultiPart,
            "alerts" => $alerts,
            "frameSetup" => $frameSetup,
            "frameView" => $frameDiv,
            "main" => $mainCardModel
        ]);
    }

    /**
     * @param PlateMultiPart $plateMultiPart
     * @param int $programId
     */
    private function SaveFrameData(PlateMultiPart $plateMultiPart, int $programId) {
        $program = $plateMultiPart->getProgramById($programId);
        $frame = $program->getFrame();

        $frame->setPoints($_POST["dots"]);
        $frame->setValue($_POST["areaValue"]);
        $frame->save();
    }
}