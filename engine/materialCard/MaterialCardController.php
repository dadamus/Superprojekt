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

    /**
     * @param array $data
     * @return string
     */
    public function releaseAction(array $data): string
    {
        global $db;

        $qtyQuery = $db->prepare('SELECT QtyAvailable FROM plate_warehouse WHERE id = "' . $data['SheetId'] . '"');
        $qtyQuery->execute();

        $qtyData = $qtyQuery->fetch();

        $action = '+';
        $toSave = (int)$qtyData['QtyAvailable'] + (int)$data['quantity'];
        switch ($data['status']) {
            case 0: //Przyjęcie
            case 3: //Korekta dodająca
                $action = '+';
            $toSave = (int)$qtyData['QtyAvailable'] + (int)$data['quantity'];
                break;
            case 1: //Wydanie zewnętrzne
            case 2: //Wydanie wewnętrzne
            case 4: //Korekta odejmująca
            case 5: //Zagubiona
            case 6: //Złomowanie
                $action = '-';
            $toSave = (int)$qtyData['QtyAvailable'] - (int)$data['quantity'];
                break;
        }

        PlateWarehouseJob::NewJob(PlateWarehouseJob::JOB_CHANGE_QUANTITY, $data['SheetId'], [
            'quantity' => $toSave,
            'type' => $data['status']
        ]);


        $db->query('UPDATE plate_warehouse SET QtyAvailable = QtyAvailable ' . $action . $data['quantity'] . ' WHERE id = "' . $data['SheetId'] . '"');

        return 'ok';
    }
}