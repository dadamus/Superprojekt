<?php
/**
 * Created by PhpStorm.
 * User: dawidadamus
 * Date: 29.10.2017
 * Time: 13:30
 */

/**
 * Class SheetTrash
 */
class SheetTrash
{
    public static function trash()
    {
        global $db;

        $trashQuery = $db->prepare("
          SELECT 
          id,
          SheetCode
          FROM 
          plate_warehouse
          WHERE
          QtyAvailable <= 0
          AND state != 'deleted'
          AND SheetCode NOT LIKE 'NEST%'
        ");
        $trashQuery->execute();

        while ($sheet = $trashQuery->fetch()) {
            $updateQuery = $db->prepare("UPDATE plate_warehouse SET state = 'deleted' WHERE id = :id");
            $updateQuery->bindValue(":id", $sheet['id'], PDO::PARAM_INT);
            $updateQuery->execute();

            WarehouseLogService::trash($sheet['SheetCode']);

            $jobQuery = new sqlBuilder(sqlBuilder::INSERT, 'plate_warehouse_jobs');
            $jobQuery->bindValue('SheetCode', $sheet['SheetCode'], PDO::PARAM_STR);
            $jobQuery->bindValue('job', 'trash', PDO::PARAM_STR);
            $jobQuery->bindValue('created_at', date("Y-m-d H:i:s"), PDO::PARAM_STR);
            $jobQuery->flush();
        }
    }

    public static function fromTrash() {
        global $db;

        $fromTrashQuery = $db->prepare("
            SELECT
            *
            FROM
            plate_warehouse
            WHERE
            QtyAvailable >= 0
            AND state = 'deleted'
        ");
        $fromTrashQuery->execute();

        while ($sheet = $fromTrashQuery->fetch()) {
            $updateQuery = $db->prepare("UPDATE plate_warehouse SET state = 'default' WHERE id = :id");
            $updateQuery->bindValue(":id", $sheet['id'], PDO::PARAM_INT);
            $updateQuery->execute();

            PlateWarehouseJob::NewJob(PlateWarehouseJob::JOB_NEW, $sheet['id'], [
                'SheetCode' => $sheet['SheetCode'],
                'MaterialName' => $sheet["MaterialName"],
                'QtyAvailable' => $sheet['QtyAvailable'],
                'GrainDirection' => $sheet['GrainDirection'],
                'Width' => $sheet['Width'],
                'Height' => $sheet['Height'],
                'SpecialInfo' => $sheet['SpecialInfo'],
                'Comment' => '',
                'SheetType' => $sheet['SheetType'],
                'SkeletonFile' => '',
                'SkeletonData' => '',
                'MD5' => '',
                'Price' => 0,
                'Priority' => $sheet['Priority'],
            ]);

            WarehouseLogService::fromTrash($sheet['SheetCode']);
        }
    }
}