<?php

/*
 * __________________________________________
 *                B A C K U P
 *  1   -   Auto-costing change
 *  333 -   Img wait for upload
 * __________________________________________
 */

error_reporting(-1);

require_once dirname(__FILE__) . "/engine/tools/toolsEngine.php";

//DB SETTINGS
$user = "adrian";
$pass = "adrian123";
$dbname = "abl_manager";
//$user = "15760473_ablm";
//$pass = "ABLManager1";
//$dbname = "15760473_ablm";


$cpt = "profile_costing"; // Profile costing table name in db
$cplt = "plate_costing"; // Plate - || -
//SRC SETTING
$data_src = dirname(__FILE__) . "/data/";
$site_path = ""; //bez / na koncu
$user_name = "laser";
//$data_src = dirname(__FILE__) . "/DATA/";
//$site_path = "http://serwer1423535.home.pl/manager"; //bez / na koncu
//$user_name = "laser";

$mFolderName = array();
$mFolderName[1] = "blacha1";
$mFolderName[2] = "blacha2";
$mFolderName[3] = "blacha3";
$mFolderName[4] = "blacha4";

//TYPE SETTINGS

require_once dirname(__FILE__) . '/engine/class/client.php';
require_once dirname(__FILE__) . '/engine/status.php';

class Detail { // Deal

    public $i_type; //Typ  liczbowy
    public $s_type; //Typ tkstowy

    public function __construct($int) {
        $this->i_type = $int;
        switch ($int) {
            case 1:
                $this->s_type = 'B';
                break;
            case 2:
                $this->s_type = 'P';
                break;
            default:
                $this->s_type = "";
                break;
        }
    }

}

//DB INIT
try {
    $db = new PDO('mysql:host=127.0.0.1;dbname=' . $dbname, $user, $pass);
    $db->exec("set names utf8");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}

//IMAP INIT
$IMAP_IP = "{serwer1423535.home.pl:143/novalidate-cert}INBOX";
$IMAP_USER = "software@abl-tech.pl";
$IMAP_PASS = "MailTest123";

//SIMPLE FUNCTION
function make_dir($src) {
    global $user_name;
    mkdir($src, 0777, true);
    //chown($src, $user_name);
}

function _getChecboxText($id) {
    $id = intval($id);
    $t = null;
    switch ($id) {
        case 1:
            $t = "B";
            break;
        case 2:
            $t = "Pr";
            break;
        case 3:
            $t = "W";
            break;
        case 4:
            $t = "P";
            break;
        case 5:
            $t = "Z";
            break;
        case 6:
            $t = "R";
            break;
        case 7:
            $t = "Cc";
    }
    return $t;
}

function _secToTime($sec, $separator = "") {
    $cut_all_time_h = floor($sec / 3600);
    if ($cut_all_time_h < 10) {
        $cut_all_time_sh = "0" . $cut_all_time_h;
    } else {
        $cut_all_time_sh = $cut_all_time_h;
    }
    $cut_all_time_m = floor(($sec - ($cut_all_time_h * 3600)) / 60);
    if ($cut_all_time_m < 10) {
        $cut_all_time_sm = "0" . $cut_all_time_m;
    } else {
        $cut_all_time_sm = $cut_all_time_m;
    }
    $cut_all_time_s = floor($sec - ($cut_all_time_m * 60) - ($cut_all_time_h * 3600));
    if ($cut_all_time_s < 10) {
        $cut_all_time_ss = "0" . $cut_all_time_s;
    } else {
        $cut_all_time_ss = $cut_all_time_s;
    }
    $cut_all_time = $cut_all_time_sh . $separator . $cut_all_time_sm . $separator . $cut_all_time_ss;
    return $cut_all_time;
}

function _timeToSec($time, $separator = ":") {
    $e_time = explode($separator, $time);
    $sec = $time;
    if (count($e_time) > 1) {
        $sec = (intval(@$e_time[0]) * 3600) + (intval(@$e_time[1]) * 60) + intval(@$e_time[2]);
    }
    return $sec;
}

$orderStatusMax = 8;

function getOrderStatus($sid) {
    $_status = array();

    $status = "";
    $color = "";
    $enable = false;
    switch ($sid) {
        case 1:
            $status = "Puste";
            $color = "label-info";
            $enable = false;
            break;
        case 2:
            $status = "Oczekuje";
            $color = "label-info";
            $enable = true;
            break;
        case 3:
            $status = "Do zaprogramowania";
            $color = "label-info";
            $enable = true;
            break;
        case 4:
            $status = "W produkcji";
            $color = "label-success";
            $enable = true;
            break;
        case 5:
            $status = "Wstrzymane";
            $color = "label-danger";
            $enable = true;
            break;
        case 6:
            $status = "Do wydania";
            $color = "label-success";
            $enable = true;
            break;
        case 7:
            $status = "Zakończone";
            $color = "label-success";
            $enable = false;
            break;
        case 8:
            $status = "Anulowane";
            $color = "label-danger";
            $enable = false;
            break;
        default:
            $status = "Brak danych";
            $color = "label-info";
            $enable = true;
            break;
    }
    $_status["text"] = $status;
    $_status["color"] = $color;
    $_status["change"] = $enable;
    return $_status;
}

function programCheck($mpw) {
    global $db;

    $qpmpw = $db->query("SELECT `program`, `pieces` FROM `mpw` WHERE `id` = '$mpw'");
    $pmpw = $qpmpw->fetch();

    $program = explode("|", $pmpw["program"]);

    $pp = 0;
    for ($i = 0; $i < count($program); $i++) {
        if ($program[$i] != null) {
            $qpd = $db->query("SELECT `mpw`, `multiplier` FROM `programs` WHERE `id` = '$program[$i]'");
            $pd = $qpd->fetch();

            $ppmpw = json_decode($pd["mpw"], true);
            $pp += $ppmpw[$mpw] * $pd["multiplier"];
        }
    }

    $qsrc = $db->query("SELECT `path`, `code`, `src` FROM `oitems` WHERE `mpw` = '$mpw'");
    $fsrc = $qsrc->fetch();
    $src = $fsrc["path"] . "/" . $fsrc["code"];

    if ($pp >= $pmpw["pieces"]) {
        if (file_exists($src)) {
            unlink($src);
        }
        return true;
    }
    if (file_exists($src) == false) {
        mkdir($fsrc["path"], 0777, true);
        copy($fsrc["src"], $src);
    }

    return false;
}

function orderCheck($orders) {
    global $db;
    foreach ($orders as $ord) {
        $qoitem = $db->query("SELECT `mpw` FROM `oitems` WHERE `oid` = '$ord'");

        $program_test = array();
        foreach ($qoitem as $oi) {
            $mpw = $oi["mpw"];
            array_push($program_test, programCheck($mpw));
        }

        if (array_search(false, $program_test) === false) {
            $db->query("UPDATE `order` SET `status` = '4' WHERE `id` = '$ord'");
        }
    }
}
