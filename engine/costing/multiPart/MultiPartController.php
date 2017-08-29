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
    /**
     * MultiPartController constructor.
     */
    public function __construct()
    {
        $this->setViewPath(dirname(__FILE__) . "/view/");
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
            mat.name as material_name,
            details.name as detail_name,
            details.did as detail_id,
            d.src as real_detail_name
            FROM 
            plate_multiPartDetails details
            LEFT JOIN mpw m ON m.id = details.mpw
            LEFT JOIN details d ON d.id = details.did
            LEFT JOIN material mat ON mat.id = m.material
            WHERE
            details.dirId = :dirId
            ORDER BY details.mpw ASC
        ");
        $detailsQuery->bindValue(":dirId", $dirId, PDO::PARAM_INT);
        $detailsQuery->execute();

        $detailsData = $detailsQuery->fetchAll(PDO::FETCH_ASSOC);

        $mpw = [];
        foreach ($detailsData as $detail) {
            //Szukamy takich samych mpw
            $mpwId = $detail["id"];
            foreach ($mpw as $i) {
                foreach ($i["details"] as $d) {
                    if (
                        $d["version"] == $detail["version"]
                        && $d["material_name"] == $detail["material_name"]
                        && $d["thickness"] == $detail["thickness"]
                        && $d["pieces"] == $detail["pieces"]
                        && $d["atribute"] == $detail["atribute"]
                    ) {
                        $mpwId = $d["id"];
                        break 2;
                    }
                }
            }

            $mpw[$mpwId]["details"][] = $detail;
        }

        //Jeszcze materialy do selectow
        $materialQuery = $db->query("
            SELECT 
            id, name
            FROM 
            material
        ");

        return $this->render("costingView.php", [
            "dirName" => $dirData["dir_name"],
            "dirId" => explode("/", $dirData["dir_name"])[0],
            "mpw" => $mpw,
            "materials" => $materialQuery->fetchAll(PDO::FETCH_ASSOC)
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