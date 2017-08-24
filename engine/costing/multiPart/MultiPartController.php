<?php
/**
 * Created by PhpStorm.
 * User: dawidadamus
 * Date: 24.08.2017
 * Time: 17:51
 */

require_once dirname(__DIR__) . "/../mainController.php";

/**
 * Class MultiPartController
 */
class MultiPartController extends mainController
{
    /**
     * MultiPartController constructor.
     */
    public function __construct()
    {
        $this->setViewPath(dirname(__FILE__) . "/view/");
    }

    public function getList()
    {
        global $db;

        $search = $db->prepare("
            SELECT 
            *
            FROM
            plate_multiPartDirectories
        ");
        $search->execute();

        return $this->render("listView.php", [
            "rows" => $search->fetchAll()
        ]);
    }
}