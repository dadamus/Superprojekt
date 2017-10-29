<?php
/**
 * Created by PhpStorm.
 * User: dawidadamus
 * Date: 29.10.2017
 * Time: 16:34
 */

class PlateWarehouseJob
{
    CONST JOB_NEW = 'insert';

    /**
     * @param string $type
     * @param int $sheetId
     * @param array $data
     */
    public static function NewJob(string $type, int $sheetId, array $data = [])
    {
        global $db;

        $sheetCodeQuery = $db->prepare('SELECT SheetCode FROM plate_warehouse WHERE id = :id');
        $sheetCodeQuery->bindValue(':id', $sheetId, PDO::PARAM_INT);
        $sheetCodeQuery->execute();

        $sheetCodeData = $sheetCodeQuery->fetch();
        $sheetCode = $sheetCodeData['SheetCode'];

        $jobQuery = new sqlBuilder(sqlBuilder::INSERT, 'plate_warehouse_jobs');
        $jobQuery->bindValue('SheetCode', $sheetCode, PDO::PARAM_STR);
        $jobQuery->bindValue('job', $type, PDO::PARAM_STR);
        $jobQuery->bindValue('created_at', date("Y-m-d H:i:s"), PDO::PARAM_STR);
        $jobQuery->bindValue('data', json_encode($data), PDO::PARAM_STR);
        $jobQuery->flush();
    }
}