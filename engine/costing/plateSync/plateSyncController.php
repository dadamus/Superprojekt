<?php
/**
 * Created by PhpStorm.
 * User: dawidadamus
 * Date: 07.09.2017
 * Time: 22:16
 */

/**
 * Class PlateSyncController
 */
class PlateSyncController
{
    /**
     * @param array $data
     */
    public function syncAction(array $data)
    {
        global $db;

        $programs = $data['programs'];
        $materials = $data['materials'];
        $materialId = 0;

        foreach ($programs as $program) {
            $sheetName = str_replace(['+', ' '], ['.', '.'], urldecode($program["SheetName"]));
            $sheetCount = $program["SheetCount"];
            $details = $program["Details"];
            $sheetNumber = $program["SheetId"];
            $queryBuilder = new sqlBuilder(sqlBuilder::INSERT, 'cutting_queue');
            $queryBuilder->bindValue('quantity', $sheetCount, PDO::PARAM_INT);
            $queryBuilder->bindValue('sheet_name', $sheetName, PDO::PARAM_STR);
            $queryBuilder->bindValue('created_at', date("Y-m-d H:i:s"), PDO::PARAM_STR);
            $queryBuilder->flush();

            $cuttingQueueId = $db->lastInsertId();

            foreach ($details as $detail) {
                $detailName = $detail["PartName"];
                $quantity = $detail["Quantity"];

                $oitemId = $this->getOItemIdByDetailName($detailName);

                $detailQuery = new sqlBuilder(sqlBuilder::INSERT, 'cutting_queue_details');
                $detailQuery->bindValue('cutting_queue_id', $cuttingQueueId, PDO::PARAM_INT);
                $detailQuery->bindValue('oitem_id', $oitemId, PDO::PARAM_INT);
                $detailQuery->bindValue('qantity', $quantity, PDO::PARAM_INT);
                $detailQuery->flush();
            }

            $programQuery = new sqlBuilder(sqlBuilder::INSERT, 'programs');
            $programQuery->bindValue('new_cutting_queue_id', $cuttingQueueId, PDO::PARAM_INT);
            $programQuery->bindValue('name', $sheetName, PDO::PARAM_STR);
            $programQuery->flush();

            $programId = $db->lastInsertId();

            $materialRow = $materials[$materialId];

            if (@$materialRow['UsedSheetNum'] <= 0) {
                $materialId++;
            }

            $materials[$materialId]['UsedSheetNum'] -= 1;
            $materialName = $materials[$materialId]['SheetCode'];
            $this->getImg($materialName, $programId, $sheetNumber);
        }
    }

    /**
     * @param string $sheetName
     * @param int $programId
     * @param int $sheetNumber
     * @return bool
     */
    private function getImg(string $sheetName, int $programId, int $sheetNumber): bool
    {
        global $data_src, $db;

//        try {
            $plateQuery = $db->prepare('SELECT id FROM plate_warehouse WHERE SheetCode = :sheetName');
            $plateQuery->bindValue(':sheetName', $sheetName, PDO::PARAM_STR);
            $plateQuery->execute();

            $plateData = $plateQuery->fetch();
            var_dump($plateData);
            if ($plateData === false) {
                return false;
            }

            $imgNumber = $sheetNumber + 1;
            $filePath = $data_src . 'temp/' . $imgNumber . '.bmp';
            $uploadPath = $data_src . 'program_image/';

            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $newName = $programId . '_' . date('Y_m_d_H_i_s') . '_' . rand() . '.bmp';
            $newPath = $uploadPath . $newName;

            rename($filePath, $newPath);
            $sqlBuilder = new sqlBuilder(sqlBuilder::INSERT, 'sheet_image');
            $sqlBuilder->bindValue('plate_warehouse_id', $plateData['id'], PDO::PARAM_INT);
            $sqlBuilder->bindValue('program_id', $programId, PDO::PARAM_INT);
            $sqlBuilder->bindValue('src', $newPath, PDO::PARAM_STR);
            $sqlBuilder->bindValue('upload_date', date('Y-m-d H:i:s'), PDO::PARAM_STR);
            $sqlBuilder->flush();
//        } catch (\Exception $ex) {
//            throw new \Exception($ex);
//            return false;
//        }

        return true;
    }

    /**
     * @param string $detailName
     * @return null|int
     * @throws Exception
     */
    private function getOItemIdByDetailName(string $detailName)
    {
        global $db;

        $searchQuery = $db->prepare("SELECT id FROM oitems WHERE `name` = :name");
        $searchQuery->bindValue(':name', $detailName, PDO::PARAM_STR);
        $searchQuery->execute();

        $searchData = $searchQuery->fetch();

        if (!$searchQuery) {
            throw new \Exception('Brak detalu: ' . $detailName);
        }

        return $searchData['id'];
    }
}