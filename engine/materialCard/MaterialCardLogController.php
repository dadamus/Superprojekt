<?php

require_once __DIR__ . '/../mainController.php';

class MaterialCardLogController extends mainController
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
    public function showAction(string $sheetCode)
    {
        global $db;

        $plateLogQuery = $db->query('
            SELECT 
            l.*,
            a.name
            FROM plate_warehouse_log l
            LEFT JOIN accounts a ON a.id = l.user
            WHERE l.sheetcode = "' . $sheetCode . '"
            ORDER BY l.date DESC
        ');

        return $this->render('logView.php', [
            'sheetCode' => $sheetCode,
            'logs' => $plateLogQuery->fetchAll(PDO::FETCH_ASSOC)
        ]);
    }
}