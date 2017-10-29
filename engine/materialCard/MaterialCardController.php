<?php
/**
 * Created by PhpStorm.
 * User: dawidadamus
 * Date: 29.10.2017
 * Time: 17:46
 */

include __DIR__ . '/../mainController.php';

/**
 * Class MaterialCardController
 */
class MaterialCardController extends mainController
{
    public function __construct()
    {
        $this->setViewPath(__DIR__ . '/view/');
    }

    /**
     * @param string $sheetCode
     * @return string
     */
    public function indexAction(string $sheetCode): string
    {
        global $db;

        $sheetDataQuery = $db->prepare("SELECT * FROM plate_warehouse WHERE SheetCode = :sheetCode");
        $sheetDataQuery->bindValue(':sheetCode', $sheetCode, PDO::PARAM_STR);
        $sheetDataQuery->execute();

        $sheetData = $sheetDataQuery->fetch();

        return $this->render('mainView.php', [
            'sheetData' => $sheetData
        ]);
    }
}