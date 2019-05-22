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
            m.MaterialTypeName
            FROM
            plate_warehouse pw
            LEFT JOIN T_material m ON m.MaterialName = pw.MaterialName
            WHERE
            pw.SheetCode = "' . $sheetCode . '"
        ');

        $materialData = $materialQuery->fetch(PDO::FETCH_ASSOC);

        return $this->render('printView.php', [
            'sheet_code' => $sheetCode,
            'material' => $materialData['MaterialTypeName']
        ]);
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


        $db->query('UPDATE plate_warehouse SET QtyAvailable = ' . $toSave . ' WHERE id = "' . $data['SheetId'] . '"');

        SheetTrash::trash();

        return 'ok';
    }
}