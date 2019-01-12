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

            $jobQuery = new sqlBuilder(sqlBuilder::INSERT, 'plate_warehouse_jobs');
            $jobQuery->bindValue('SheetCode', $sheet['SheetCode'], PDO::PARAM_STR);
            $jobQuery->bindValue('job', 'trash', PDO::PARAM_STR);
            $jobQuery->bindValue('created_at', date("Y-m-d H:i:s"), PDO::PARAM_STR);
            $jobQuery->flush();
        }
    }
}