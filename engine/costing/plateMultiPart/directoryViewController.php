<?php

/**
 * Created by PhpStorm.
 * User: dawid
 * Date: 10.07.2017
 * Time: 17:41
 */

require_once dirname(__DIR__) . "/../mainController.php";

/**
 * Class directoryViewController
 */
class directoryViewController extends mainController
{
    /**
     * directoryViewController constructor.
     */
    public function __construct()
    {
        $this->setViewPath(dirname(__FILE__) . "/view/directory/");
    }

    /**
     * @return string
     */
    public function getDirectoryForm()
    {
        global $db;

        $minDate = date("Y-m-01");
        $maxDate = date("Y-m-31");

        $partDirectories = $db->query("SELECT id FROM plate_multiPartDirectories WHERE created_at >= '$minDate' AND created_at <= '$maxDate' ORDER BY id DESC LIMIT 1");
        $lastRow = $partDirectories->fetch();

        $lastId = 1;
        if ($lastRow) {
            $lastId = $lastRow["id"]+1;
        }

        $folderName = $lastId . date("/m/Y");

        return $this->render("directoryView.php", [
            "folderName" => $folderName
        ]);
    }

    /**
     * @param string $filter
     * @return string
     */
    public function getDirectory($filter = '')
    {
        global $db;
        $minDate = date("Y-m-01");
        $maxDate = date("Y-m-31");
        $partDirectories = $db->query("SELECT id, dir_name, blocked FROM plate_multiPartDirectories WHERE dir_name LIKE \"%$filter%\"");

        $dirs = [];

        while ($d = $partDirectories->fetch()) {
            $dirs[] = [
                "name" => $d["dir_name"],
                "id" => $d["id"],
                "blocked" => $d["blocked"]
            ];
        }

        return $this->render("directoryContentView.php", $dirs);
    }

    /**
     * @param string $name
     * @return string
     */
    public function addDirectory($name)
    {
        global $db;
        $newDirQuery = $db->prepare("INSERT INTO plate_multiPartDirectories (dir_name, created_at) VALUES (:name, :created_at)");
        $newDirQuery->bindValue(":name", $name, PDO::PARAM_STR);
        $newDirQuery->bindValue(":created_at", date("Y-m-d H:i:s"), PDO::PARAM_STR);
        $newDirQuery->execute();

        return $db->lastInsertId();
}

}