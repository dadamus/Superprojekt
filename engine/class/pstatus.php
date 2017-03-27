<?php

//PROJECT STATUS

class p_status {

    public $auto = 1;
    public $manual = 2;
    public $strategy = array();

    public function __construct() {
        global $db;
        //Get status
        $qstatus = $db->query("SELECT * FROM `pstatus_text`");
        foreach ($qstatus as $ws) {

            $local_perm = [];
            if ($ws["perm_auto"] == 1) {
                array_push($local_perm, $this->auto);
            }
            if ($ws["perm_manual"] == 1) {
                array_push($local_perm, $this->manual);
            }
            $this->strategy[$ws["id"]] = [
                "perm" => $local_perm,
                "text" => $ws["name"]
            ];
        }
    }

    public function getText($id) {
        return $this->strategy[$id]["text"];
    }

    private function checkPerm($id, $perm) {
        if (array_search($perm, $this->strategy[$id]["perm"]) === false) {
            return false;
        }
        return true;
    }

    public function Change($pid, $sid, $perm) {
        global $db;
        if ($this->checkPerms($sid, $perm)) {
            //TODO 
        } else {
            throw new Exception("Brak uprawnień");
            return false;
        }
    }
    
    public function checkPerms($id, $perms) {
        if (is_array($perms)) {
            foreach ($perms as $perm) {
                if (!$this->checkPerm($id, $perm)) {
                    return false;
                }
            }
        } else {
            if (!$this->checkPerm($id, $perms)) {
                return false;
            }
        }

        return true;
    }

}
