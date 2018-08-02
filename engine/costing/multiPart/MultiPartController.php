<?php
/**
 * Created by PhpStorm.
 * User: dawidadamus
 * Date: 24.08.2017
 * Time: 17:51
 */

require_once dirname(__DIR__) . "/../mainController.php";

/**
 * Class MultiPartController
 */
class MultiPartController extends mainController
{
    private $materialThickness = [];

    /**
     * MultiPartController constructor.
     */
    public function __construct()
    {
        $this->setViewPath(__DIR__ . "/view/");
    }

    public function getList()
    {
        global $db;

        $search = $db->prepare("
          SELECT d.id, d.dir_name, d.created_at, m.type, SUM(settings.price) as price
          FROM plate_multiPartDirectories d
          LEFT JOIN plate_multiPartDetails details ON d.id = details.dirId
          LEFT JOIN plate_multiPartCostingDetailsSettings settings ON settings.directory_id = d.id
          AND settings.detaild_id = details.did
          LEFT JOIN mpw m ON m.id = details.mpw
          GROUP BY d.id
        ");
        $search->execute();

        return $this->render("listView.php", [
            "rows" => $search->fetchAll()
        ]);
    }

    public function getPlateCosting(int $dirId)
    {
        global $db;
        $dirDataQuery = $db->prepare("
            SELECT * 
            FROM 
            plate_multiPartDirectories
            WHERE
            id = :dirId
        ");
        $dirDataQuery->bindValue(":dirId", $dirId, PDO::PARAM_INT);
        $dirDataQuery->execute();
        $dirData = $dirDataQuery->fetch();

        $detailsQuery = $db->prepare("
            SELECT 
            m.*,
            d.pid,
            m.id as mpw_id,
            mat.id as material_id,
            mat.name as material_name,
            details.name as detail_name,
            details.did as detail_id,
            d.src as real_detail_name,
            cc.matName as laser_material_name,
            cc.id as laser_material_id,
            tm.MaterialName as material_type_name
            FROM 
            plate_multiPartDetails details
            LEFT JOIN mpw m ON m.id = details.mpw
            LEFT JOIN details d ON d.id = details.did
            LEFT JOIN material mat ON mat.id = m.material
            LEFT JOIN cutting_conditions_names cc ON cc.id = m.cutting_conditions_name_id
            LEFT JOIN T_material tm On tm.MaterialTypeName = cc.matType AND tm.thickness = cc.thck
            WHERE
            details.dirId = :dirId
            ORDER BY details.mpw ASC
        ");
        $detailsQuery->bindValue(":dirId", $dirId, PDO::PARAM_INT);
        $detailsQuery->execute();

        $detailsData = $detailsQuery->fetchAll(PDO::FETCH_ASSOC);

        $mpw = [];
        $dlp = 0;
        $detailDescriptionArray = [];
        foreach ($detailsData as $detail) {
            //Rozmiar aktualnego materialu
            if (isset($this->materialThickness[$detail['material_id']])) {
                $detail['material_thickness_info'] = $this->materialThickness[$detail['material_id']];
            } else {
                $materialThicknessQuery = $db->prepare('
                  SELECT 
                  tm.Thickness
                  FROM T_material tm
                  LEFT JOIN material m ON m.name = tm.MaterialTypeName
                  WHERE
                  m.id = :matId
                  GROUP BY tm.Thickness
                  ORDER BY tm.Thickness ASC
                ');
                $materialThicknessQuery->bindValue(':matId', $detail['material_id'], PDO::PARAM_INT);
                $materialThicknessQuery->execute();

                $this->materialThickness[$detail['material_id']] = $materialThicknessQuery->fetchAll(PDO::FETCH_ASSOC);
                $detail['material_thickness_info'] = $this->materialThickness[$detail['material_id']];
            }

            $mpw[$dlp]["details"][] = $detail;

            $detailDescriptionArray[] = [
                'id' => $detail['id'],
                'did' => $detail['id'],
                'mpw' => $detail['mpw_id'],
                'dirId' => $dirId,
                'pid' => $detail['pid']
            ];
            $dlp++;
        }

        //Jeszcze materialy do selectow
        $materialQuery = $db->query("
            SELECT 
            id, `name`
            FROM 
            material
        ");

        return $this->render("costingView.php", [
            "dirName" => $dirData["dir_name"],
            "dirId" => explode("/", $dirData["dir_name"])[0],
            "mpw" => $mpw,
            "materials" => $materialQuery->fetchAll(PDO::FETCH_ASSOC),
            'detailsDataJson' => json_encode($detailDescriptionArray)
        ]);
    }

    /**
     * @param int $dirId
     * @param int $mpwId
     * @param int $detailId
     * @return string
     */
    public function deleteDetail(int $dirId, int $mpwId, int $detailId) {
        global $db, $data_src;

        $dataQuery = $db->prepare("
            SELECT
            d.id,
            d.src,
            dir.dir_name
            FROM
            plate_multiPartDetails d
            LEFT JOIN plate_multiPartDirectories dir ON dir.id = d.dirId
            WHERE 
            d.mpw = :mpw
            AND did = :did
            AND dirid = :dirId
        ");
        $dataQuery->bindValue(":mpw", $mpwId, PDO::PARAM_INT);
        $dataQuery->bindValue(":did", $detailId, PDO::PARAM_INT);
        $dataQuery->bindValue(":dirId", $dirId, PDO::PARAM_INT);
        $dataQuery->execute();

        $data = $dataQuery->fetch();

        $dirDataParts = explode("/", $data["dir_name"]);
        $dirNr = $dirDataParts[0];

        $detailPath = $data_src . "multipart/" . date("m") . "/" . $dirNr . "/" . $data["src"];
        if (file_exists($detailPath)) {
            unlink($detailPath);
        }

        $db->query("DELETE FROM plate_multiPartDetails WHERE id = ". $data["id"]);
        return "ok";
    }
}