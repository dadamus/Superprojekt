<?php
/**
 * Created by PhpStorm.
 * User: dawid
 * Date: 12.07.2017
 * Time: 20:35
 */

require_once dirname(__DIR__) . "/../mainController.php";
require_once dirname( __FILE__) . "/model/DetailModel.php";
require_once dirname(__DIR__) . "/../model/MPWModel.php";

class createMPWController extends mainController
{
    /** @var  DetailModel */
    private $detailModel;

    /**
     * directoryViewController constructor.
     */
    public function __construct()
    {
        $this->detailModel = new DetailModel();
        $this->setViewPath(dirname(__FILE__) . "/view/mpw/");
    }

    /**
     * @param int $directoryId
     * @param int $projectId
     * @param array $details
     * @return string
     */
    public function addMPWForm(int $directoryId, int $projectId, array $details)
    {
        global $db;

        $material = $db->query("SELECT `id`,`name` FROM material");
        $versions = $this->detailModel->getDetailsVersion($projectId, $details);

        return $this->render("mpwView.php", [
            "project_id" => $projectId,
            "details" => $details,
            "directory" => $directoryId,
            "material" => $material,
            "versions" => $versions
        ]);
    }

    /**
     * @param int $materialId
     * @return array
     */
    public function getMaterialThickness(int $materialId)
    {
        global $db;
        $data = $db->query("
            SELECT 
            m.Thickness as thickness,
            m.MaterialName as material_name
            FROM
            T_material m
            LEFT JOIN material t ON t.name = m.MaterialTypeName
            WHERE
            t.id = $materialId
            GROUP BY thickness
            ORDER BY thickness ASC
        ");
        return $data->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @param string $material
     * @param float $thickness
     * @return array
     */
    public function getMaterialLaser(string $material, float $thickness)
    {
        global $db;

        $data = $db->query("
            SELECT
            t.MaterialName,
            t.Thickness,
            cc.matName as laserMaterialName,
            cc.id as ccId
            FROM
            T_material t
            LEFT JOIN cutting_conditions_names cc ON cc.matType = t.MaterialTypeName AND cc.thck = t.Thickness
            WHERE
            t.Thickness = '$thickness'
            AND t.MaterialTypeName = '$material'
        ");
        return $data->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @param array $data
     * @param int $dirId
     * @return string
     * @throws Exception
     */
    public function addMpw(array $data, int $dirId)
    {
        global $db;
        $originalMpw = new MPWModel($data);

        $mpwPerDetail = $originalMpw->mpwPerDetail();

        /** @var MPWModel $mpw */
        foreach ($mpwPerDetail as $mpw) {
            $mpwQuery = new sqlBuilder("INSERT", "mpw");
            $mpwQuery->bindValue("pid", $mpw->getMpwProject(), PDO::PARAM_INT);
            $mpwQuery->bindValue("code", "Plate Multi", PDO::PARAM_STR);
            $mpwQuery->bindValue("version", $mpw->getVersion(), PDO::PARAM_INT);
            $mpwQuery->bindValue("material", $mpw->getMaterial(), PDO::PARAM_INT);
            $mpwQuery->bindValue("thickness", $mpw->getThickness(), PDO::PARAM_STR);
            $mpwQuery->bindValue("pieces", $mpw->getPieces(), PDO::PARAM_INT);
            $mpwQuery->bindValue("des", $mpw->getDes(), PDO::PARAM_STR);
            $mpwQuery->bindValue("date", date("Y-m-d H:i:s"), PDO::PARAM_STR);
            $mpwQuery->bindValue("type", OT::AUTO_WYCENA_BLACH_MULTI_KROK_1, PDO::PARAM_INT);
            $mpwQuery->bindValue("attributes", $mpw->getAttributes(), PDO::PARAM_STR);
            $mpwQuery->flush();

            $mpw->setMpwId($db->lastInsertId());
            $mpw->makeDetails($dirId, $mpw->getMpwId());
        }

        return "ok";
    }
}