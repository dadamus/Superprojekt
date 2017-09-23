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
     * @param array $programs
     */
    public function syncAction(array $programs)
    {
        global $db;

        foreach ($programs as $program) {
            $sheetName = urldecode($program["SheetName"]);
            $sheetCount = $program["SheetCount"];
            $details = $program["Details"];

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
            $programQuery->bindValue('	name', $sheetName, PDO::PARAM_STR);
            $programQuery->flush();
        }
    }

    /**
     * @param string $detailName
     * @return int
     * @throws Exception
     */
    private function getOItemIdByDetailName(string $detailName): int
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