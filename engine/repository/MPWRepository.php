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
     * @param string $detailName
     * @return MPWModel
     * @throws Exception
     */
    public function getMpwByDetailName(string $detailName): MPWModel
    {
        global $db;

        $searchQuery = $db->prepare("
          SELECT mpw 
          FROM plate_multiPartDetails
          WHERE name = :name
          ");
        $searchQuery->bindValue(':name', $detailName, PDO::PARAM_STR);
        $searchQuery->execute();
        $searchQueryData = $searchQuery->fetch();

        if ($searchQueryData === false) {
            throw new Exception('Nie znalazłem MPW dla detalu o nazwie: ' . $detailName);
        }

        $mpwId = intval($searchQueryData["mpw"]);

        $mpw = new MPWModel();
        $mpw->findById($mpwId);

        return $mpw;
    }
}