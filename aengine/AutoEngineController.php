<?php

require_once dirname(__FILE__) . "/router.php";

/**
 * Created by PhpStorm.
 * User: Dawid
 * Date: 02.05.2017
 * Time: 21:29
 */

/**
 * Class AutoEngineController
 */
class AutoEngineController
{
	/**
	 * @var array
	 */
	private $router;

	/**
	 * AutoEngineController constructor.
	 */
	public function __construct()
	{
		$this->router = router::getRouting();
		$action = @$_GET["p_a"];
		$this->performAction($action);
	}

    /**
     * @param string $actionName
     * @return bool
     */
	protected function performAction(string $actionName)
	{
		if (is_null($actionName)) {
			echo "Brak akcji!";
			return false;
		}

		if (!isset($this->router[$actionName])) {
			echo "Brak akcji dla: " . $actionName;
			return false;
		}

		$action = $this->router[$actionName];
		echo $this->$action();
		return true;
	}

	private function PMAction()
	{
		global $db, $data_src;
		require_once dirname(__FILE__) . '/modules/pm.php';
	}

	private function PImgExistAction()
	{
		global $db, $data_src;

		$_parts = $_GET["parts"];

		$resp = "";
		$parts = explode('|', $_parts);
		foreach ($parts as $part) {
			$_name = str_replace(' ', '', $part);

			$did = null;

			$ne = explode("-", $_name);
			$did = $ne[2];

			/* $qmpw = $db->query("SELECT `did` FROM `mpw` WHERE `code` = '$_name'");
			  if ($mpw = $qmpw->fetch()) {
			  $did = $mpw["did"];
			  } else {
			  $qoitem = $db->query("SELECT `mpw` FROM `oitems` WHERE `code` = '$_name'");
			  if ($oitem = $qoitem->fetch()) {
			  $qmpw = $db->query("SELECT `did` FROM `mpw` WHERE `id` = '" . $oitem["mpw"] . "'");
			  $fmpw = $qmpw->fetch();
			  $did = $fmpw["did"];
			  }
			  } */

			if ($did == null) {
				continue;
			} else {
				$dquery = $db->query("SELECT count(*) FROM `details` WHERE `id` = '$did' AND `img` = ''");
				$selected = $dquery->fetchColumn();
				if ($selected > 0) {
					$resp .= $part . "#" . $did . "|";
				}
			}
		}

		return $resp;
	}

	private function SaveImageAction()
	{
		global $db, $data_src;

		$parts = $_GET["d"];
		$to_save = explode("|", $parts);
		foreach ($to_save as $img) {
			$db->query("UPDATE `details` SET `img` = '" . $img . ".bmp' WHERE `id` = '$img'");
		}

		return "Zapisałem obrazki.";
	}

	private function CopyDbAction()
	{
		global $db, $data_src;

		$new = $db->query("SELECT `id` FROM `plate_warehouse` WHERE `date` = '0000-00-00 00:00:00'");
		$date = date("Y-m-d H:i:s");

		foreach ($new as $row) {
			$db->query("UPDATE `plate_warehouse` SET `date` = '$date', `type` = '1' WHERE `id` = '" . $row['id'] . "'");
		}

		$nesting = $db->query("SELECT `id` FROM `plate_warehouse` WHERE UPPER(SheetCode) LIKE '%NEST%'");
		foreach ($nesting as $row) {
			$db->query("UPDATE `plate_warehouse` SET `type` = '4' WHERE `id` = '" . $row["id"] . "'");
		}

		return "php done";
	}

	private function CheckCostingLineAction()
	{
		global $db, $data_src;
		$code = $_GET["code"];

		$detail = $db->query("SELECT * FROM `mpw` WHERE `code` = '$code'");
		if ($data = $detail->fetch()) {
			return json_encode($data);
		}
		return json_encode(null);
	}

	private function CostingTubeSingleAction()
	{
		global $db, $data_src;
		require_once dirname(__FILE__) . '/modules/mpc.php';
	}

	private function AddSingleCostingAction()
	{
		global $db, $data_src;

		require_once dirname(__DIR__) . '/engine/costing/plateSinglePartFactor.php';

		$plateData = new plateSinglePart($data_src . "temp/plate.csv");
		try {
			$plateData->checkMPW();
			$costingId = $plateData->saveInputData();
			$plateData->saveImage($costingId);
			return "Dane zostały wysłane!";
		} catch(\Exception $ex) {
			return "Blad! $ex->getMessage()";
		}
	}

	private function GetSyncDataAction()
	{
		global $db, $data_src;
		return CSharp::generatePlateData();
	}

	private function SetSyncedAction()
	{
		global $db, $data_src;

		if (is_null(@$_POST["synced"])) {
			return "Brak danych do synchronizacji!";
		}

		$PlateSynced = [];
		try {
			$PlateSynced = json_decode($_POST["synced"], true);
		} catch (\Exception $ex) {
			return "Blad: " . $ex->getMessage();
		}

		CSharp::setPlateSynced($PlateSynced);

		return "201";
	}

	private function SyncFromMDBAction()
	{
		$input = iconv('UTF-8', 'UTF-8//IGNORE', utf8_encode($_POST["toSync"]));
		$data = json_decode($input, true);
		$plateToCheck = "";
		$plates = [];
		foreach ($data as $plate) {
			if (strlen($plateToCheck) > 0) {
				$plateToCheck .= ", ";
			}
			$plateToCheck .= "'" . $plate["SheetCode"] . "'";
			$plates[$plate["SheetCode"]] = $plate;
		}

		$sql = new sqlBuilder("SELECT", "plate_warehouse");
		$sql->addBind("*", "");
		$sql->addCondition("`SheetCode` in (" . $plateToCheck . ")");
		$sqlData = $sql->getData();

		$response = [
			"insert" => "",
			"update" => "",
			"delete" => "",
		];

		$plateTools = new PlateWarehouse();

		$response = new PlateSyncResponse();

		foreach ($sqlData as $row) {
			if (!isset($plates[$row["SheetCode"]]["SheetCode"])) {
				continue;
			}
			unset($row["id"]);
			unset($row["ndp"]);
			unset($row["date"]);
			unset($row["pdate"]);

			$data = $plates[$row["SheetCode"]];
			if ($row["synced"] == 0) {
				$data = $row;
			}

			unset($data["SkeletonData"]);
			unset($data["synced"]);
			if ($row["state"] == "default") {
				unset($data["state"]);
				$response->addUpdate($data, "SheetCode");
				unset($plates[$row["SheetCode"]]);
			} else if ($row["state"] == "deleted") {
				unset($data["state"]);
				$response->addDelete($data, "SheetCode");
				unset($plates[$row["SheetCode"]]);
			}
		}

		foreach ($plates as $values) {
			$response->addInsert($values, "SheetCode");
		}

		$dbConnector = new sqlBuilder();

		$mysqlTableName = "plate_warehouse";
		$updateQuery = $response->getUpdate($mysqlTableName);
		if (strlen($updateQuery) > 0) {
			$dbConnector->Query($updateQuery);
			$plateTools->setSynced($response->getUpdateId(), false, true);
		}

		$insertQuery = $response->getInsert($mysqlTableName);
		if (strlen($insertQuery) > 0) {
			$dbConnector->Query($insertQuery);
			$plateTools->setSynced($response->getInsertId(), true, false);
		}

		$response = [
			"insert" => $response->getInsert("platewarehousesynced"),
			"insert_id" => $response->getInsertId(),
			"update" => $response->getUpdate(),
			"update_id" => $response->getUpdateId(),
			"delete" => $response->getDelete(),
		];

		return json_encode($response);
	}

	private function SyncFromMDBError()
	{
		global $db;
		$input = iconv('UTF-8', 'UTF-8//IGNORE', utf8_encode($_POST["orders"]));
		$orders = json_decode($input, true);

		$query = $db->prepare("UPDATE `plate_warehouse` SET `SheetCode` = concat(`SheetCode`, '_bkp'), `state` = 'other' WHERE `SheetCode` = :sheetCode");
		foreach ($orders as $order) {
			$query->bindParam(":sheetCode", $order, PDO::PARAM_STR);
			$query->execute();
		}

		return json_encode([
			"ok"
		]);
	}

	private function SyncFromMDBMaterialAction()
	{

		$input = iconv('UTF-8', 'UTF-8//IGNORE', utf8_encode($_POST["toSync"]));
		$data = json_decode($input, true);
		$materialToCheck = "";
		$materials = [];
		foreach ($data as $material) {
			if (strlen($materialToCheck) > 0) {
				$materialToCheck .= ", ";
			}
			$materialToCheck .= "'" . $material["MaterialName"] . "'";
			$materials[$material["MaterialName"]] = $material;
		}

		$sql = new sqlBuilder("SELECT", "T_material");
		$sql->addBind("*", "");
		$sql->addCondition("`MaterialName` in (" . $materialToCheck . ")");
		$sqlData = $sql->getData();

		$response = [
			"insert" => "",
			"update" => "",
			"delete" => "",
		];

		$plateTools = new T_Material();

		$response = new PlateSyncResponse();

		foreach ($sqlData as $row) {
			if (!isset($materials[$row["MaterialName"]]["MaterialName"])) {
				continue;
			}

			$data = $materials[$row["MaterialName"]];
			if ($row["synced"] == 0) {
				$data = $row;
			}

			unset($data["synced"]);
			unset($data["state"]);
			$response->addUpdate($data, "MaterialName");
			unset($materials[$row["MaterialName"]]);
		}

		foreach ($materials as $values) {
			$response->addInsert($values, "MaterialName");
		}

		$dbConnector = new sqlBuilder();

		$mysqlTableName = "T_material";
		$updateQuery = $response->getUpdate($mysqlTableName);
		if (strlen($updateQuery) > 0) {
			$dbConnector->Query($updateQuery);
			$plateTools->setSynced($response->getUpdateId());
		}

		$insertQuery = $response->getInsert($mysqlTableName);
		if (strlen($insertQuery) > 0) {
			$dbConnector->Query($insertQuery);
			$plateTools->setSynced($response->getInsertId());
		}

		$response = [
			"insert" => $response->getInsert("tmaterialsynced"),
			"insert_id" => $response->getInsertId(),
			"update" => $response->getUpdate(),
			"update_id" => $response->getUpdateId(),
			"delete" => $response->getDelete(),
		];

		return json_encode($response);
	}

	private function SyncFromMDBMaterialError()
	{
		global $db;
		$input = iconv('UTF-8', 'UTF-8//IGNORE', utf8_encode($_POST["orders"]));
		$orders = json_decode($input, true);

		$query = $db->prepare("UPDATE `T_material` SET `MaterialName` = concat(`MaterialName`, '_bkp') WHERE `MaterialName` = :materialName");
		foreach ($orders as $order) {
			$query->bindParam(":materialName", $order, PDO::PARAM_STR);
			$query->execute();
		}

		return json_encode([
			"ok"
		]);
	}

	private function UpdateMultipartPlateCostingDetails()
    {
        global $db;
        $details = json_decode($_POST["details"], true);


        foreach ($details as $detail)
        {
            $did = $detail["detail_id"];
            $sheet = $detail["sheet"];
            $pretime = $detail["pretime"];
            $laser_mat_name = $detail["laser_mat_name"];

            $detailsSearchQuery = $db->query("SELECT id FROM plate_multiPartCostingDetails WHERE did = $did AND LaserMatName = '$laser_mat_name'");
            $detailsSearch = $detailsSearchQuery->fetch();

            $queryType = "INSERT";

            if ($detailsSearch !== false) {
                $queryType = "UPDATE";
            }

            $detailQuery = new sqlBuilder($queryType, "plate_multiPartCostingDetails");
            $detailQuery->bindValue("did", $did, PDO::PARAM_INT);
            $detailQuery->bindValue("LaserMatName", $laser_mat_name, PDO::PARAM_STR);
            $detailQuery->bindValue("PreTime", $pretime, PDO::PARAM_STR);
            $detailQuery->bindValue("upload_date", date("Y-m-d H:i:s"), PDO::PARAM_STR);

            if ($queryType == "UPDATE")
            {
                $detailQuery->addCondition("id = " . $detailsSearch["id"]);
            }

            $detailQuery->flush();
        }

        return "ok";
    }

    private function MultipartPlateCosting()
    {
        try {
            require_once dirname(__DIR__) . "/engine/costing/plateMultiPart/plateMultiPart.php";
            $plateMultiPart = new PlateMultiPart();
            $plateMultiPart->MakeFromData($_POST["data"]);
            return "ok";
        } catch (\Exception $ex) {
            return "Wystąpił błąd: " . $ex->getMessage();
        }
    }
}