<?php

require_once dirname(__FILE__) . '/../../config.php';
require_once dirname(__FILE__) . '/../../engine/protect.php';
require_once dirname(__FILE__) . '/mpc_inputs.php';
require_once dirname(__FILE__) . '/mpc_count.php';

$mpc_id = @$_GET["mpc"];
$action = @$_GET["action"];
$reload_id = @$_GET["reload_id"];

$history_view = false;

function getData($name, $row) {
    $GLOBALS[$name] = $row[$name];
}

//History view
if ($action == 2) {
    $history_view = true;
    $backup_id = $_GET["h_id"];
    $qbackup = $db->query("SELECT `snapshot`, `comments` FROM `backup` WHERE `id` = '$backup_id'");
    $fbackup = $qbackup->fetch();
    $snapshot = $fbackup["snapshot"];
    $bcomments = $fbackup["comments"];

    $response = Array();
    $response["reload_id"] = $reload_id;

    $dsnapshot = stripslashes(html_entity_decode($snapshot));
    $djsnapshot = json_decode($dsnapshot);

    foreach ($djsnapshot as $key => $val) {
        $GLOBALS[$key] = $val;
        $_POST[$key] = $val;
    }

    $edit = true;

    INPUT_INIT();

    foreach ($djsnapshot as $key => $val) {
        $GLOBALS[$key] = $val;
        $_POST[$key] = $val;
    }
} else {
    $settings = array();
    $qsettings = $db->query("SELECT * FROM `settings`");
    foreach ($qsettings as $row) {
        $settings[$row["name"]] = floatval($row["value"]);
    }

    $mid = $_POST["material"];
    $qmtype = $db->query("SELECT `sname` FROM `material` WHERE `id` = '$mid'");
    $fmtype = $qmtype->fetch();
    $mtype = $fmtype["sname"];

    $qmpc = $db->query("SELECT * FROM `mpc` WHERE `id` = '$mpc_id'");
    foreach ($qmpc as $row) {//Load data froam db
        getData("oq", $row);
        getData("d_qty", $row);
        getData("tps", $row);
        getData("tpl", $row);
        getData("oq", $row);
        getData("name", $row);
        getData("sotime", $row);
        getData("socost", $row);
        getData("wh", $row);
        getData("diameter", $row);
        getData("type", $row);
        getData("thickness", $row);
        getData("mname", $row);
        getData("d_weight", $row);
        getData("cut_all_time", $row);
        getData("c_mb", $row);
        getData("dlugosc_mat", $row);
        getData("zajetosc_mat", $row);
        getData("mat_discount", $row);
        getData("wasteMode", $row);
        getData("paramI", $row);
        getData("AreaWithoutHoles", $row);
        getData("AreaWithHoles", $row);
        getData("AreaWithoutHolesSHD", $row);


        //Atributes
        $atributes = json_decode($row["atributes"]);
        if (count(@$atributes) > 0) {
            foreach ($atributes as $key => $val) {
                $GLOBALS["a" . $key . "i2"] = $val;
            }
        }
    }

    $edit = true;

    $contents = file_get_contents("php://input");
    $json_inputs = stripslashes(html_entity_decode($_POST["json_inputs"]));

    $inputs = json_decode($json_inputs, true);

    mpc_count();
    INPUT_INIT();

    if ($action == 1) { // Save data
        $qmpc = $db->query("SELECT * FROM `mpc` WHERE `id` = '$mpc_id'");
        $fetch = $qmpc->fetch();
        $fetch["atributes"] = json_decode(stripslashes($fetch["atributes"]), true);
        var_dump($fetch);
        $snapshot = json_encode($fetch);
        $date = date("Y-m-d H:i:s");
        $uid = $_SESSION["login"];

        foreach ($inputs as $key => $val) {
            if ($key == "cut_all_time" || $key == "sotime") {
                $inputs[$key] = _timeToSec($val);
            }
        }

        //Atribute save
        $atributes = "";
        if ($_POST["atribute"] != null) {
            foreach ($_POST["atribute"] as $key) {
                $atributes .= $key . ":" . @$_POST["a" . $key . "i2"] . "|";
            }
        }

        $comments = json_encode($inputs);

        $db->query("INSERT INTO `backup` (`type`, `item`, `user`, `snapshot`, `comments`, `date`) VALUES ('1', '$mpc_id', '$uid', '$snapshot', '$comments', '$date')");

        $update_s = "UPDATE `mpc` SET ";
        foreach ($_INPUTS->inputs as $input) {
            $update_s .= "`" . $input->name . "` = :" . $input->name . ", ";
        }

        $update_s .= "`udate` = :udate, `atributes` = '$atributes' WHERE `id` = '$mpc_id'";

        $uquery = $db->prepare($update_s);
        $_INPUTS->BindInputs($uquery);
        $uquery->bindValue(":udate", $date, PDO::PARAM_STR);
        $uquery->execute();

        die("Zapisałem");
    }
}

$_INPUTS->Add($_INPUTS->input, "d_clean_cut");
$_INPUTS->Add($_INPUTS->input, "tweight");
$_INPUTS->Add($_INPUTS->input, "d_przeladunek");
$_INPUTS->Add($_INPUTS->input, "d_last_price_n_brutto");
//Atibute add

for ($i = 1; $i <= 6; $i++) {
    $_INPUTS->Add($_INPUTS->input, "a$i" . "i1");
    $_INPUTS->Add($_INPUTS->input, "a$i" . "i2");
}

foreach ($_INPUTS->inputs as $input) {
    if ($input->name == "cut_all_time" || $input->name == "sotime") {
        $input->val = _secToTime($input->val);
    }
}

$response = Array();
$response["reload_id"] = $reload_id;
$response["inputs"] = JSON_ENCODE($_INPUTS->inputs);

if ($history_view == true) {
    $response["c_input_name_backup"] = $bcomments;
}

echo JSON_ENCODE($response);
