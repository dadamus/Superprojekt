<?php
/**
 * Created by PhpStorm.
 * User: dawidadamus
 * Date: 15.08.2017
 * Time: 22:24
 */

require_once dirname(__DIR__) . "/../mainController.php";
require_once dirname(__FILE__) . "/plateMultiPart.php";

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
     * @param int $mpwId
     * @param int $programId
     * @return string
     */
    public function viewMainCard(int $mpwId, int $programId = 0): string
    {
        $frameSetup = false;
        $alerts = [];
        $missingFrames = 0;

        $plateMultiPart = new PlateMultiPart();
        $plateMultiPart->MakeFromMpwId($mpwId);

        $programs = $plateMultiPart->getPrograms();
        foreach ($programs as $program) {
            $frame = $program->getFrame();

            if ($frame->getValue() <= 0) {
                $alerts[] = [
                    "type" => "warning",
                    "message" => "Program " . $program->getSheetName() . " nie posiada określonej ramki!"
                ];
                $missingFrames++;
                $frameSetup = true;
            }
        }

        if (isset($_POST["dots"])) { //Zapis gotowej ramki
            $this->SaveFrameData($plateMultiPart, $programId);

            if ($missingFrames == 1) { //To byla ostatnia brakujaca ramka
                $plateMultiPart->Calculate();
            }

            return true;
        }

        $frameDiv = null;
        if ($frameSetup) {
            $frameDiv = $this->render("ImgFrameView.php", [
                "multiPart" => $plateMultiPart,
            ]);
        }

        return $this->render("mainView.php", [
            "multiPart" => $plateMultiPart,
            "alerts" => $alerts,
            "frameSetup" => $frameSetup,
            "frameView" => $frameDiv
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