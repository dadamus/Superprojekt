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
     * @param int $programId
     * @return string
     */
    public function viewProgramCard(int $programId): string
    {
        return "test";
    }
}