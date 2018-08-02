<?php

/**
 * Created by PhpStorm.
 * User: dawid
 * Date: 15.07.2017
 * Time: 15:18
 */
class DetailModel
{
    protected $db;

    public function __construct()
    {
        global $db;
        $this->db = $db;
    }

    /**
     * @param int $projectId
     * @param array $details
     * @return array
     */
    public function getDetailsDataById(int $projectId, array $details)
    {
        $id = null;
        foreach ($details as $detail) {
            if (!is_null($id)) {
                $id .= ", ";
            }

            $id .= $detail;
        }

        $detailsQuery = $this->db->query("
          SELECT d.*,
          p.src as project_storage
          FROM details d
          LEFT JOIN projects p ON p.id = d.pid
          WHERE 
          d.pid = $projectId
          AND d.id IN ($id)
        ");

        $response = $detailsQuery->fetchAll();

        return $response;
    }

    /**
     * @param int $projectId
     * @param array $details
     * @return array
     */
    public function getDetailsVersion(int $projectId, array $details)
    {
        $detailsData = $this->getDetailsDataById($projectId, $details);
        $versions = [];

        foreach ($detailsData as $detail) {
            $dversions = $this->searchDetailVersions($detail["project_storage"], $detail["src"]);
            foreach ($dversions as $version) {
                $versions[$version][] = [
                    "id" => intval($detail["id"]),
                    "name" => $detail["src"]
                ];
            }
        }

        return $versions;
    }

    /**
     * @param string $mainDirectory
     * @param string $detailName
     * @return array
     */
    private function searchDetailVersions(string $mainDirectory, string $detailName)
    {
        $versionsScan = glob($mainDirectory . "/*", GLOB_ONLYDIR);
        $versions = [];
        foreach ($versionsScan as $v) {
            if (basename($v)[0] == "V") {
                if (file_exists("$v/dxf/" . $detailName)) {
                    $versions[] = basename($v);
                }
            }
        }

        return $versions;
    }
}