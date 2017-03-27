<?php

ob_start();
//AJAX CONTENT

require_once dirname(__FILE__) . '/../../config.php';
require_once dirname(__FILE__) . '/../protect.php';

$action = @$_GET["id"];

$pid = @$_COOKIE["plProjectId"];
if ($pid > 0) {
    $cquery = $db->prepare("SELECT `cid` FROM `projects` WHERE `id` = :pid");
    $cquery->bindValue(":pid", $pid, PDO::PARAM_INT);
    $cquery->execute();
    $fquery = $cquery->fetch();
    $cid = $fquery["cid"];
}

//Get order list
if ($action == 1) {
    $oquery = $db->query("SELECT * FROM `order` WHERE `cid` = '$cid'");

    $table = "";
    foreach ($oquery as $order) {
        $oid = $order["id"];
        $itemsq = $db->query("SELECT COUNT(*) FROM `oitems` WHERE `oid` = '$oid'");
        $items = $itemsq->fetchColumn();

        $cost = 0;
        if ($items > 0) {
            $oitems = $db->query("SELECT `mpw` FROM `oitems` WHERE `oid` = '$oid'");
            foreach ($oitems as $item) {
                $mpwq = $db->query("SELECT `mcp`, `type` FROM `mpw` WHERE `id` = '" . $item["mpw"] . "'");
                $mpw = $mpwq->fetch();
                if ($mpw["type"] == 2 || $mpw["type"] == 7) {
                    $wid = $item["mpw"];
                    $mpcq = $db->query("SELECT `last_price_all_netto` FROM `mpc` WHERE `wid` = '$wid'");
                    $mpc = $mpcq->fetch();
                    $cost += floatval($mpc["last_price_all_netto"]);
                } else if ($mpw["type"] == 4 || $mpw["type"] == 8) {
                    $qpc = $db->query("SELECT `priceset` FROM `profile_costing` WHERE `id` = '" . $mpw["mcp"] . "'");
                    $pc = $qpc->fetch();
                    $cost += floatval($pc["priceset"]);
                }
            }
        }

        $order_status[1] = '<i class="fa fa-folder-open-o"></i>';
        $order_status[2] = '<i class="fa fa-cubes"></i>';
        $order_status[3] = '<i class="fa fa-puzzle-piece"></i>';
        $order_status[4] = '<i class="fa fa-industry"></i>';

        $table .= "<tr style=\"cursor: pointer \" class=\"o_click\" id=\"" . $order['id'] . "_oi\">"
                . "<td>" . $order['id'] . "</td>"
                . "<td>" . $order['on'] . "</td>"
                . "<td>" . $items . "</td>"
                . "<td>" . $cost . " zł</td>"
                . "<td>" . $order_status[$order['status']] . "</td>"
                . "<td>" . $order['cdate'] . "</td>"
                . "<td class=\"dob\">Usuń <i class=\"fa fa-trash\"></i></td>"
                . "</tr>";
    }
    die($table);
}

//New order
if ($action == 2) {
    $date = $_POST["odate"];
    $edate = explode("-", $date);
    $des = $_POST["odes"];
    $cdate = date("Y-m-d H:i:s");

    $oquery = $db->prepare("INSERT INTO `order` (`cid`, `des`, `status`, `date`, `uid`, `cdate`) VALUES (:cid, :des, :status, :date, :uid, :cdate)");
    $oquery->bindValue(":cid", $cid, PDO::PARAM_INT);
    $oquery->bindValue(":des", $des, PDO::PARAM_STR);
    $oquery->bindValue(":status", 1, PDO::PARAM_INT);
    $oquery->bindValue(":date", $date, PDO::PARAM_STR);
    $oquery->bindValue(":uid", $_SESSION["login"], PDO::PARAM_INT);
    $oquery->bindValue(":cdate", $cdate, PDO::PARAM_STR);
    $oquery->execute();

    $oid = $db->lastInsertId();
    $on = "ZAM " . $oid . "/" . $edate[1] . "/" . $edate[0];
    $onquery = $db->query("UPDATE `order` SET `on` = '$on' WHERE `id` = '$oid'");

    die("1");
}

//Add to order form
if ($action == 3) {
    $form = '<div class="input-group"><input type="text" id="osinput" class="form-control" placeholder="ZAM 0/mm/rrrr"><span class="input-group-addon"><i class="fa fa-search"></i></span></div>';
    $form .= '<div style="width:100%; margin-top: 10px;"></div><table class="table table-bordered table-striped table-condensed flip-content"><thead class="flip-content"><tr><th>ID</th><th>Numer</th><th>Data dodania</th></tr></thead><tbody id="oscontent"></tbody></table>';
    die($form);
}

//Order search
if ($action == 4) {
    $key = $_GET["key"];
    $table = '';

    $search = $db->query("SELECT * FROM `order` WHERE `on` LIKE '%$key%'");
    foreach ($search as $row) {
        $table .= "<tr style=\"cursor: pointer;\" class=\"osc\" id=\"" . $row['id'] . "_osc\"><td>" . $row['id'] . "</td><td class=\"oname\">" . $row['on'] . "</td><td>" . $row['date'] . "</td></tr>";
    }
    die($table);
}

//Add items to order
if ($action == 5) {
    $oid = intval($_GET["oid"]);
    $items = explode("|", $_GET["items"]);

    foreach ($items as $item) {
        $mpwq = $db->query("SELECT `src`, `did`, `pid`, `atribute`, `pieces`, `material`, `type`, `code`, `version`, `radius`, `mcp` FROM `mpw` WHERE `id` = '$item'");
        $mpw = $mpwq->fetch();
        $pid = $mpw["pid"];
        $projq = $db->query("SELECT `cid`, `src` FROM `projects` WHERE `id` = '$pid'");
        $proj = $projq->fetch();
        $cid = $proj["cid"];

        $mpcq = $db->query("SELECT `type`, `mtype`, `thickness`, `wh` FROM `mpc` WHERE `wid` = '$item'");
        $mpc = $mpcq->fetch();

        $did = $mpw["did"];
        $dq = $db->query("SELECT `type`, `src` FROM `details` WHERE `id` = '$did'");
        $d = $dq->fetch();

        $main = "";
        $dim = "";

        $new_type = 2;
        if ($mpw["type"] == 1) { //Profil
            $main = "roto";
            $thickness = floatval($mpc["thickness"]);

            //DIR
            if ($mpc["type"] == 0) { //Profil
                $wh = explode("X", $mpc["wh"]);
                $dim = floatval($wh[0]) . "x" . floatval($wh[1]) . "x" . floatval($mpc["thickness"]);
            } else if ($mpc["type"] == 1) { //Rura
                $dim = "fi" . floatval($mpc["wh"]) . "x" . floatval($mpc["thickness"]);
            } else { //Inne
                $dim = "k" . floatval($mpc["thickness"]);
            }
        } else if ($mpw["type"] == 3) {//Profil manual
            $main = "roto";
            $new_type = 4;
            $qpc = $db->query("SELECT `dimension`, `type` FROM `profile_costing` WHERE `id` = '" . $mpw["mcp"] . "'");
            $pc = $qpc->fetch();

            $exdim = explode("x", $pc["dimension"]);
            $thickness = floatval(end($exdim));

            if ($pc["type"] == 1) {
                $dim = "fi" . $pc["dimension"];
            } else {
                $dim = $pc["dimension"];
            }
        } else if ($mpw["type"] == 5) { // Blacha
            $main = "sheet";

            $dim = floatval($mpc["thickness"]);
        } else {
            continue;
        }

        $dpath = $data_src . "cutting/" . $main;

        //Get material folder
        if ($mpw["type"] == 3) {
            $materialId = $mpw["material"];
            $mq = $db->query("SELECT `lname` FROM `material` WHERE `id` = '$materialId'");
            $m = $mq->fetch();
            $sm = strtoupper($m["lname"][0]);
        } else {
            $materialSname = $mpc["mtype"];
            $mq = $db->query("SELECT `lname` FROM `material` WHERE `name` = '$materialSname'");
            $m = $mq->fetch();
            $sm = strtoupper($m["lname"][0]);
        }


        $dpath .= "/" . $m["lname"];
        $dpath .= "/" . str_replace(".", "P", $dim);

        //Name
        $esrc = explode(".", $d["src"]);
        $ext = end($esrc);

        $j_atr = json_decode($mpw["atribute"], true);
        $a = "";
        if (count($j_atr) > 0) {
            foreach ($j_atr as $atr) {
                $a .= _getChecboxText($atr);
            }
        }
        $atribute = "-" . $a;
        /* $ecode = explode("-", $mpw["code"]);
          $atribute = "";
          if (count($ecode) > 3) {
          $atribute = "-" . end($ecode);
          } */

        $newName = $cid . "-" . $mpw["pieces"] . "X" . $thickness . "-$sm-$item" . $atribute . "." . $ext;

        //Original path
        if ($d["type"] == 2) {
            if ($mpw["radius"] > 0) {
                $_src = $proj["src"] . "/V" . $mpw["version"] . "/R" . $mpw["radius"] . "/shd/" . $d["src"];
            } else {
                $_src = $proj["src"] . "/V" . $mpw["version"] . "/shd/" . $d["src"];
            }
        } else {
            $_src = $proj["src"] . "/V" . $mpw["version"] . "/dxf/" . $d["src"];
        }

        if (file_exists($dpath) == false) {
            make_dir($dpath);
        }

        $db->query("INSERT INTO `oitems` (`oid`, `mpw`, `code`, `src`, `path`) VALUES ('$oid', '$item', '$newName', '$_src', '$dpath')");
        $db->query("UPDATE `mpw` SET `type` = '$new_type' WHERE `id` = '$item'");
        $db->query("UPDATE `order` SET `status` = '2' WHERE `id` = '$oid'");
    }

    die("1");
}

//Get items 
if ($action == 6) {
    $oid = intval($_GET["oid"]);

    $oquery = $db->query("SELECT `status` FROM `order` WHERE `id` = '$oid'");
    $foquery = $oquery->fetch();
    if ($foquery["status"] > 2) {
        die("2");
    }

    $iquery = $db->query("SELECT `id`, `mpw`, `code` FROM `oitems` WHERE `oid` = '$oid'");
    $content = "";

    foreach ($iquery as $oitem) {
        $wid = $oitem["mpw"];

        $mpwq = $db->query("SELECT `did`, `material`, `pieces`, `atribute`, `mcp`, `type` FROM `mpw` WHERE `id` = '$wid'");
        $mpw = $mpwq->fetch();

        $atribute_s = "";
        $atribute = json_decode($mpw["atribute"]);
        if (count($atribute) > 0) {
            foreach ($atribute as $a) {
                $atribute_s .= " <b>" . _getChecboxText($a) . "</b> ";
            }
        }

        $did = $mpw["did"];
        $nameq = $db->query("SELECT `src` FROM `details` WHERE `id` = '$did'");
        $namef = $nameq->fetch();
        $dname = $namef["src"];

        $mid = $mpw["material"];
        $materialq = $db->query("SELECT `name` FROM `material` WHERE `id` = '$mid'");
        $materialf = $materialq->fetch();
        $material = $materialf["name"];

        if ($mpw["type"] == 2 || $mpw["type"] == 7) {
            $mpcq = $db->query("SELECT `last_price_all_netto` FROM `mpc` WHERE `wid` = '$wid'");
            $mpc = $mpcq->fetch();
            $cost = $mpc["last_price_all_netto"];
        }
        if ($mpw["type"] == 4 || $mpw["type"] == 8) {
            $qpc = $db->query("SELECT `priceset` FROM `profile_costing` WHERE `id` = '" . $mpw["mcp"] . "'");
            $pc = $qpc->fetch();
            $cost = $pc["priceset"];
        }

        $content .= '<tr id="' . $oitem["mpw"] . '_mpwoi"><td>' . $oitem["mpw"] . '</td>'
                . '<td>' . $dname . '</td>'
                . '<td>' . $oitem["code"] . '</td>'
                . '<td>' . $material . '</td>'
                . '<td>' . $mpw["pieces"] . ' <i class="fa fa-pencil pediti" id="' . $wid . '_mpeidd" style="cursor: pointer"></i></td>'
                . '<td>' . $atribute_s . '</td>'
                . '<td>' . $cost . '</td>'
                . '<td style="text-align: center;"><i class="fa fa-trash difo" style="cursor: pointer;"></i></td></tr>';
    }

    die($content);
}

//Delete item
if ($action == 7) {
    $mpw = intval($_GET["mpw"]);

    $qpmpw = $db->query("SELECT `id` FROM `mpw` WHERE `id` = '$mpw' AND `program` = ''");
    if ($pmpw = $qpmpw->fetch()) {
        $uquery = $db->query("UPDATE `mpw` SET `type` = `type` - 1 WHERE `id` = '$mpw' ");
        $dquery = $db->query("DELETE FROM `oitems` WHERE `mpw` = '$mpw'");
    }
    die("1");
}

//To production
if ($action == 8) {
    $oid = $_GET["oid"];
    $db->query("UPDATE `order` SET `status` = '3' WHERE `id` = '$oid'");

    $oitems = $db->query("SELECT * FROM `oitems` WHERE `oid` = '$oid'");
    foreach ($oitems as $row) {
        //MPW STATUS UPDATE
        $mpw = $row["mpw"];
        $qtype = $db->query("SELECT `type` FROM `mpw` WHERE `id` = '$mpw'");
        $dtype = $qtype->fetch();
        $type = $dtype["type"];

        $ntype = 0;
        switch ($type) {
            case 2:
                $ntype = 7;
                break;
            case 4:
                $ntype = 8;
                break;
            case 6:
                $ntype = 9;
                break;
        }

        if ($ntype != 0) {
            $db->query("UPDATE `mpw` SET `type` = '$ntype' WHERE `id` = '$mpw'");
        }

        if (@file_exists($row["src"])) {
            copy($row["src"], $row["path"] . "/" . $row["code"]);
        }

        header("Location: $site_path/order/$oid/");
    }
}

//Change pieces html
if ($action == 9) {
    $pval = @$_GET["pval"];
    $content = "<div class=\"input-inline input-small\"><div class=\"input-group\"><input type=\"text\" class=\"form-control\" value=\"$pval\" id=\"pnv\"/>";
    $content .= "<span class=\"input-group-addon\" id=\"changep\" style=\"cursor: pointer;\"><i class=\"fa fa-check\"></i></span></div></div>";
    die($content);
}

//Change pieces ajax
if ($action == 10) {
    $pval = $_GET["pval"]; // New value
    $mpw = $_GET["mpw"]; // Item id

    $qoitem = $db->query("SELECT `pcr` FROM `oitems` WHERE `mpw` = '$mpw'");
    $oitem = $qoitem->fetch();
    $pcr = $oitem["pcr"];

    $message = array();

    if ($pcr > $pval) {
        $message["type"] = "error";
        $message["header"] = "Za mała ilość detalu!";
        $message["content"] = "Zaprogramowane jest już $pcr sztuk tego detalu.";
        $jmessage = json_encode($message);
        die($jmessage);
    }

    $db->query("UPDATE `mpw` SET `pieces` = '$pval' WHERE `id` = '$mpw'");

    $message["type"] = "success";
    $message["header"] = "Zmieniono!";
    $message["content"] = "Nowa ilość detalu została zapisana.";
    $jmessage = json_encode($message);
    die($jmessage);
}
//Delete order
if ($action == 11) {
    $oid = $_GET["oid"];

    $oitems = $db->query("SELECT `mpw` FROM `oitems` WHERE `oid` = '$oid'");
    foreach ($oitems as $oitem) {
        $mpw = $oitem["mpw"];
        $qpmpw = $db->query("SELECT `id` FROM `mpw` WHERE `id` = '$mpw' AND `program` != ''");
        if ($pmpw = $qpmpw->fetch()) {
            die("Najpierw usuń programy!");
            break;
        } else {
            continue;
        }
    }

    $db->query("DELETE FROM `oitems` WHERE `oid` = '$oid'");
    $db->query("DELETE FROM `order` WHERE `id` = '$oid'");
    die("1");
}

ob_end_flush();
