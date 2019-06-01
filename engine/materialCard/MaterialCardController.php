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
     * @throws Exception
     */
    public function indexAction(string $sheetCode): string
    {
        global $db;

        $sheetDataQuery = $db->prepare("
          SELECT 
          w.*,
          p.SheetCode as parentSheedCode,
          m.MaterialTypeName,
          rc.remnant_check,
          rc.text as remnant_text,
          i.src as image
          FROM plate_warehouse w
          LEFT JOIN plate_warehouse p ON p.id = w.parentId
          LEFT JOIN T_material m ON m.MaterialName = w.MaterialName
          LEFT JOIN warehouse_remnant_check rc ON rc.plate_warehouse_id = w.id
          LEFT JOIN sheet_image i ON i.plate_warehouse_id = w.id
          WHERE w.SheetCode = :sheetCode
        ");
        $sheetDataQuery->bindValue(':sheetCode', $sheetCode, PDO::PARAM_STR);
        $sheetDataQuery->execute();

        $sheetData = $sheetDataQuery->fetch(PDO::FETCH_ASSOC);

        $childrenDataQuery = $db->prepare("
            SELECT
            *
            FROM
            plate_warehouse
            WHERE
            parentId = :parentId
        ");
        $childrenDataQuery->bindValue(':parentId', $sheetData['id'], PDO::PARAM_INT);
        $childrenDataQuery->execute();

        return $this->render('mainView.php', [
            'sheetData' => $sheetData,
            'childrenData' => $childrenDataQuery->fetchAll(PDO::FETCH_ASSOC)
        ]);
    }

    public function printAction(string $sheetCode)
    {
        global $db;

        $materialQuery = $db->query('
            SELECT
            m.MaterialTypeName,
            m.Thickness
            FROM
            plate_warehouse pw
            LEFT JOIN T_material m ON m.MaterialName = pw.MaterialName
            WHERE
            pw.SheetCode = "' . $sheetCode . '"
        ');

        $materialData = $materialQuery->fetch(PDO::FETCH_ASSOC);

        $digits = $this->findSheetCodeDigits($sheetCode);

        $thickness = '';
        for ($i = 0; $i < 5; $i++) {
            $thickness .= $materialData['Thickness'] .'MM&nbsp-&nbsp';
        }

        return $this->render('printView.php', [
            'sheet_code' => $sheetCode,
            'material' => $materialData['MaterialTypeName'],
            'digits' => $digits,
            'thickness' => $thickness
        ]);
    }

    private function findSheetCodeDigits(string $sheetCode)
    {
        $sheetCodeParts = explode('-', $sheetCode);
        $firstPart = reset($sheetCodeParts);

        if ($sheetCode[4] === '-') {
            $response = '';
            for ($i = 0; $i < 5; $i++) {
                $response .= $firstPart .'&nbsp-&nbsp';
            }
            return $response;
        }

        return null;
    }

    /**
     * @param int $warehouseId
     * @param int $checbkox
     * @param string $text
     */
    public function remnantCheck(int $warehouseId, int $checkbox, string $text)
    {
        global $db;
        $sheetDataQuery = $db->prepare('
            SELECT
            w.SheetCode,
            c.operator_id
            FROM plate_warehouse w 
            LEFT JOIN warehouse_remnant_check c ON c.plate_warehouse_id = w.id
            WHERE
            w.id = :id
        ');
        $sheetDataQuery->bindValue(':id', $warehouseId, PDO::PARAM_INT);
        $sheetDataQuery->execute();

        $sheetData = $sheetDataQuery->fetch(PDO::FETCH_ASSOC);

        if ($sheetData['operator_id'] === null) {
            $sql = sqlBuilder::createInsert('warehouse_remnant_check');
            $sql->bindValue('plate_warehouse_id', $warehouseId, PDO::PARAM_INT);
        } else {
            $sql = sqlBuilder::createUpdate('warehouse_remnant_check');
            $sql->addCondition('plate_warehouse_id = ' . $warehouseId);
        }

        $sql->bindValue('remnant_check', $checkbox, PDO::PARAM_INT);
        $sql->bindValue('text', $text, PDO::PARAM_STR);
        $sql->bindValue('operator_id', $_SESSION["login"], PDO::PARAM_STR);
        $sql->flush();

        header('Location: /material/' . $sheetData['SheetCode'] . '/');
    }

    /**
     * @param array $data
     * @return string
     */
    public function releaseAction(array $data): string
    {
        global $db;

        $qtyQuery = $db->prepare('SELECT SheetCode, QtyAvailable FROM plate_warehouse WHERE id = "' . $data['SheetId'] . '"');
        $qtyQuery->execute();

        $qtyData = $qtyQuery->fetch();

        $userId = $_SESSION["login"];
        $sheetCode = $qtyData['SheetCode'];
        $quantity = (int)$data['quantity'];

        $positiveValue = (int)$qtyData['QtyAvailable'] + $quantity;
        $negativeValue = (int)$qtyData['QtyAvailable'] - $quantity;
        $toSave = $positiveValue;

        switch ($data['status']) {
            case 0: //Przyjęcie
                WarehouseLogService::newRow($sheetCode, $positiveValue, $userId);
                break;
            case 3: //Korekta dodająca
                WarehouseLogService::positiveCorrection($sheetCode, $quantity, $positiveValue, $userId);
                break;
            case 1: //Wydanie zewnętrzne
                WarehouseLogService::externalDispatch($sheetCode, $quantity, $negativeValue, $userId);
                $toSave = $negativeValue;
                break;
            case 2: //Wydanie wewnętrzne
                WarehouseLogService::internalDispatch($sheetCode, $quantity, $negativeValue, $userId);
                $toSave = $negativeValue;
                break;
            case 4: //Korekta odejmująca
                WarehouseLogService::negativeCorrection($sheetCode, $quantity, $negativeValue, $userId);
                $toSave = $negativeValue;
                break;
            case 5: //Zagubiona
                WarehouseLogService::loss($sheetCode, $quantity, $negativeValue, $userId);
                $toSave = $negativeValue;
                break;
            case 6: //Złomowanie
                WarehouseLogService::scrapping($sheetCode, $quantity, $negativeValue, $userId);
                $toSave = $negativeValue;
                break;
        }

        PlateWarehouseJob::NewJob(PlateWarehouseJob::JOB_CHANGE_QUANTITY, $data['SheetId'], [
            'quantity' => $toSave,
            'type' => $data['status']
        ]);

        $db->query('UPDATE plate_warehouse SET QtyAvailable = ' . $toSave . ' WHERE id = "' . $data['SheetId'] . '"');

        SheetTrash::trash();

        return 'ok';
    }
}