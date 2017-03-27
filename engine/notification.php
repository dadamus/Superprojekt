<?php

require_once dirname(__FILE__) . '/../config.php';
require_once dirname(__FILE__) . '/protect.php';
require_once dirname(__FILE__) . '/class/notification.php';

$action = @$_GET["act"];

if ($action == 1) { //Get notification
    $notifi = getNotification();
    die(json_encode($notifi));
} else if ($action == 2) { //Get form
    $eid = $_GET["eid"];
    $qemail = $db->query("SELECT `pid`, `program`, `waring`, `type`, `done` FROM `email` WHERE `id` = '$eid'");
    $email = $qemail->fetch();

    $content = array();
    $errors = array();

    $form_type = 0;
    $jmpw = array();

    if ($email["pid"] == 0 && $email["done"] == 0) { //No project
        $form_type = 1;
        array_push($errors, array("title" => "Brak programu w bazie", "content" => "Nie znalazłem programu który odpowiada nazwie: <strong>" . $email["program"] . "</strong>"));

        $html = '<select class="form-control select2" style="width: 100%;" id="sf1n">';
        $programs = $db->query("SELECT `id`, `name`, `multiplier` FROM `programs` ORDER BY `id` DESC");
        foreach ($programs as $program) {
            $qcp = $db->query("SELECT count(*) FROM `email` WHERE `pid` = '" . $program["id"] . "'");
            $cp = $qcp->fetchColumn();

            $html .= '<option value="' . $program["id"] . '">' . $program["name"] . ' | ' . $cp . '/' . $program["multiplier"] . '</option>';
        }
        $html .= '</select>';
        array_push($content, array("title" => "Wybierz program z listy", "content" => $html));
    } else if ($email["type"] == 1 || $email["type"] == 2 || $email["type"] == 4) {//Error
        if (count($content) == 0) {
            if ($email["done"] == 0) {
                $form_type = 2;

                if ($email["type"] == 1) {
                    array_push($errors, array("title" => "Ciecie poprawne", "content" => "Wszystkie elementy programu: <strong>" . $email["program"] . "</strong> zostały wycięte?"));
                } else {
                    array_push($errors, array("title" => "Błąd w cięciu", "content" => "Określi liczbę wyciętych elementów programu: <strong>" . $email["program"] . "</strong>"));
                }

                $qpr = $db->query("SELECT `mpw` FROM `programs` WHERE `id` = '" . $email["pid"] . "'");
                $pr = $qpr->fetch();
                $pmpw = $pr["mpw"];

                $html = "";

                if ($pmpw !== 0) {
                    $mpw = json_decode($pmpw, true);
                    $jmpw = $mpw;
                    foreach ($mpw as $key => $value) {
                        $qoit = $db->query("SELECT `code` FROM `oitems` WHERE `mpw` = '$key'");
                        $oid = $qoit->fetch();
                        $code = $oid["code"];

                        $html .= '<div class="col-md-5"><div class="panel panel-primary"><div class="panel-heading" style="text-align: center;"><h3 class="panel-title">' . $code . '</h3></div><div class="panel-body"><input class="knob" data-angleoffset=-125 data-anglearc=250 value="' . $value . '" data-max=' . $value . ' id="' . $key . '_knob"/> </div></div></div>';
                    }
                }

                array_push($content, array("title" => "Wpisz liczbę", "content" => $html));

                //Operator choose
                $html_operator = '<select class="form-control select2" style="width: 100%" id="sf2n">';

                $operators = $db->query("SELECT `id`, `name` FROM `accounts` WHERE `type` = '1'");
                foreach ($operators as $operator) {
                    $html_operator .= '<option value="' . $operator["id"] . '">' . $operator["name"] . '</option>';
                }
                $html_operator .= '</select>';
                array_push($content, array("title" => "Wybierz operatora", "content" => $html_operator));
            }
        }
    }

    //Waring log
    array_push($content, array("title" => "Warnings", "content" => '<div class="note note-success"><p>' . nl2br($email["waring"]) . '</p></div>'));

    $for_js = array();
    $return = "";

    for ($i = 0; $i < count($content); $i++) {
        $return .= '<div class="row"><div class="col-md-12"><div class="portlet light bordered">';
        $return .= '<div class="portlet-title"><div class="caption"><i class="fa fa-warning"></i> ' . $content[$i]["title"] . '</div></div>';
        $return .= '<div class="portlet-body form" stlye="text-align: center;">';
        if (array_key_exists($i, $errors)) {
            $return .= '<div class="alert alert-danger"><strong>' . $errors[$i]["title"] . '</strong> ' . $errors[$i]["content"] . '</div>';
        }
        $return .= '<form role="form" class="form-horizontal form-bordered"><div class="form-body">' . $content[$i]["content"] . '<div style="clear: both;"></div></div>';
        $return .= '</form></div>';
        $return .= '</div></div></div>';
    }

    $j_return = array("form_type" => $form_type, "content" => $return, "mpw" => $jmpw);
    die(json_encode($j_return));
} else if ($action == 3) { // Set project
    $eid = $_GET["eid"];
    $val = $_GET["val"];

    $qemail = $db->query("SELECT `type` FROM `email` WHERE `id` = '$eid'");
    $email = $qemail->fetch();

    $db->query("UPDATE `email` SET `pid` = '$val' WHERE `id` = '$eid'");
    die("1");
} else if ($action == 4) { //Set pro
    $eid = $_GET["eid"];
    $val = preg_replace('/\s+/', '', $_GET["val"]);
    $mpw = json_decode(stripslashes($val), true);

    foreach ($mpw as $key => $value) {
        if ($key == "operator") {
            $db->query("UPDATE `email` SET `operator` = '$value' WHERE `id` = '$eid'");
            continue;
        }
        $db->query("UPDATE `oitems` SET `dct` = `dct` + '$value', `stored` = `stored` + '$value' WHERE `mpw` = '$key'");
    }

    $qpr = $db->query("SELECT `pid` FROM `email` WHERE `id` = '$eid'");
    $pr = $qpr->fetch();
    $pid = $pr["pid"];

    $db->query("UPDATE `programs` SET `status` = '1' WHERE `id` = '$pid'");
    $db->query("UPDATE `email` SET `done` = '1' WHERE `id` = '$eid'");
    die("1");
}