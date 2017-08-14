<?php
/**
 * Created by PhpStorm.
 * User: dawidadamus
 * Date: 14.08.2017
 * Time: 17:01
 */

require_once dirname(__DIR__) . '/model/MPWModel.php';

/**
 * Class MPWRepository
 */
class MPWRepository
{
    /**
     * @param int $detailId
     * @return MPWModel
     * @throws Exception
     */
    public function getMpwByDetailId(int $detailId): MPWModel
    {
        global $db;

        $searchQuery = $db->prepare("
          SELECT mpw 
          FROM plate_multiPartDetails
          WHERE did = :did
          ");
        $searchQuery->bindValue(':did', $detailId, PDO::PARAM_INT);
        $searchQuery->execute();
        $searchQueryData = $searchQuery->fetch();

        if ($searchQueryData === false) {
            throw new Exception('Nie znalazłem MPW dla detalu o id: ' . $detailId);
        }

        $mpwId = intval($searchQueryData["mpw"]);

        $mpw = new MPWModel();
        $mpw->findById($mpwId);

        return $mpw;
    }
}