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
     * @return string
     */
    public function viewMainCard(int $mpwId): string
    {
        $frameSetup = false;
        $alerts = [];

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
                $frameSetup = true;
            }
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
}