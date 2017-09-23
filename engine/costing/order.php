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
            $oitems = $db->query("SELECT `mpw`, `did` FROM `oitems` WHERE `oid` = '$oid'");
            foreach ($oitems as $item) {
                $mpwq = $db->query("SELECT `mcp`, `type` FROM `mpw` WHERE `id` = '" . $item["mpw"] . "'");
                $mpw = $mpwq->fetch();
                if ($mpw["type"] == OT::AUTO_WYCENA_DODANE_DO_ZAMOWIENIA || $mpw["type"] == OT::AUTO_WYCENA_ZABLOKOWANA_EDYCJA) {
                    $wid = $item["mpw"];
                    $mpcq = $db->query("SELECT `last_price_all_netto` FROM `mpc` WHERE `wid` = '$wid'");
                    $mpc = $mpcq->fetch();
                    $cost += floatval($mpc["last_price_all_netto"]);
                } else if ($mpw["type"] == OT::RECZNA_WYCENA_PROFULU_DODANE_DO_ZAMOWIENIA || $mpw["type"] == OT::RECZNA_WYCENA_PROFILU_ZABLOKOWANA_EDYCJA) {
                    $qpc = $db->query("SELECT `priceset` FROM `profile_costing` WHERE `id` = '" . $mpw["mcp"] . "'");
                    $pc = $qpc->fetch();
                    $cost += floatval($pc["priceset"]);
                } else if ($mpw["type"] >= OT::AUTO_WYCENA_BLACH_MULTI_DODANE_DO_ZAMOWIENIA) {
                    $qMultiPartPlate = $db->prepare("
                        SELECT
                        ds.price,
                        pp.PartCount
                        FROM plate_multiPartDetails pd
                        LEFT JOIN plate_multiPartCostingDetailsSettings ds ON ds.directory_id = pd.dirId AND ds.detaild_id = pd.did
                        LEFT JOIN plate_multiPartProgramsPart pp ON pp.PartName = pd.name
                        WHERE 
                        pd.mpw = :mpw
                        AND pd.did = :did
                    ");
                    $qMultiPartPlate->bindValue(':mpw', $item["mpw"], PDO::PARAM_INT);
                    $qMultiPartPlate->bindValue(':did', $item["did"], PDO::PARAM_INT);
                    $qMultiPartPlate->execute();

                    $multiPartData = $qMultiPartPlate->fetch();

                    if ($multiPartData !== false) {
                        $cost += floatval($multiPartData['price']) * floatval($multiPartData['PartCount']);
                    }
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
    $mpwId = 0;
    $didId = 0;
    $queryType = 0;

    $oid = intval($_GET["oid"]);
    $items = explode("|", $_GET["items"]);

    foreach ($items as $item) {
        if (strlen($item) > 3) {
            if ($item[0] . $item[1] . $item[2] == "MPL") {
                $itemSetup = explode("D", $item);
                $mpwId = intval(str_replace("MPL", "", $itemSetup[0]));
                $didId = intval($itemSetup[1]);
                $queryType = 1;
            }
        }

        if ($queryType == 0) {
            $dataQuery = $db->prepare("
            SELECT
            mpw.src,
            mpw.did,
            mpw.pid,
            mpw.atribute,
            mpw.pieces,
            mpw.material,
            mpw.type,
            mpw.code,
            mpw.version,
            mpw.radius,
            mpw.mcp,
            p.cid,
            p.src as project_name,
            mpc.type as mpc_type,
            mpc.mtype,
            mpc.thickness as mpc_thickness,
            mpc.wh,
            d.type as detail_type,
            d.src as detail_name
            FROM
            mpw mpw
            LEFT JOIN projects p ON p.id = mpw.pid
            LEFT JOIN mpc mpc ON mpc.wid = mpw.id
            LEFT JOIN details d ON d.id = mpw.did
            WHERE
            mpw.id = :mpw
        ");
            $dataQuery->bindValue(":mpw", $item, PDO::PARAM_INT);
            $dataQuery->execute();
        } else if ($queryType == 1) { //Query dal multipartu
            $dataQuery = $db->prepare("
                SELECT
                mpw.src,
                mpw.did,
                mpw.pid,
                mpw.atribute,
                mpw.pieces,
                mpw.type,
                mpw.code,
                mpw.version,
                mpw.radius,
                mpw.thickness as mpc_thickness,
                mpw.mcp,
                p.cid,
                p.src as project_name,
                mpc.type as mpc_type,
                mpc.wh,
                d.type as detail_type,
                d.src as detail_name,
                m.name as material_name
                FROM
                plate_multiPartDetails mpd
                LEFT JOIN plate_multiPartCostingDetailsSettings s ON s.directory_id = mpd.dirId AND s.detaild_id = mpd.did
                LEFT JOIN mpw mpw ON mpw.id = mpd.mpw
                LEFT JOIN projects p ON p.id = mpw.pid
                LEFT JOIN mpc mpc ON mpc.wid = mpw.id
                LEFT JOIN details d ON d.id = mpd.did
                LEFT JOIN material m ON m.id = mpw.material
                WHERE
                mpd.mpw = :mpw
                AND mpd.did = :did
            ");
            $dataQuery->bindValue(":mpw", $mpwId, PDO::PARAM_INT);
            $dataQuery->bindValue(":did", $didId, PDO::PARAM_INT);
            $dataQuery->execute();

            $item = $mpwId;
        }

        $data = $dataQuery->fetch(PDO::FETCH_ASSOC);

        $main = "";
        $dim = "";

        $new_type = 2;
        if ($data["type"] == 1) { //Profil
            $main = "roto";
            $thickness = floatval($data["mpc_thickness"]);

            //DIR
            if ($data["mpc_type"] == 0) { //Profil
                $wh = explode("X", $data["wh"]);
                $dim = floatval($wh[0]) . "x" . floatval($wh[1]) . "x" . floatval($data["mpc_thickness"]);
            } else if ($data["mpc_type"] == 1) { //Rura
                $dim = "fi" . floatval($data["wh"]) . "x" . floatval($data["mpc_thickness"]);
            } else { //Inne
                $dim = "k" . floatval($data["mpc_thickness"]);
            }
        } else if ($data["type"] == 3) {//Profil manual
            $main = "roto";
            $new_type = 4;
            $qpc = $db->query("SELECT `dimension`, `type` FROM `profile_costing` WHERE `id` = '" . $data["mcp"] . "'");
            $pc = $qpc->fetch();

            $exdim = explode("x", $pc["dimension"]);
            $thickness = floatval(end($exdim));

            if ($pc["type"] == 1) {
                $dim = "fi" . $pc["dimension"];
            } else {
                $dim = $pc["dimension"];
            }
        } else if ($data["type"] == 5) { // Blacha
            $main = "sheet";

            $dim = floatval($data["mpc_thickness"]);
        } else if ($data["type"] == OT::AUTO_WYCENA_BLACH_MULTI_ZATWIERDZONA || OT::AUTO_WYCENA_BLACH_MULTI_DODANE_DO_ZAMOWIENIA) { //Multi
            $main = "sheet";
            $new_type = OT::AUTO_WYCENA_BLACH_MULTI_DODANE_DO_ZAMOWIENIA;
            $dim = floatval($data["mpc_thickness"]);
        } else {
            continue;
        }

        $dpath = $data_src . "cutting/" . $main;

        //Get material folder
        if ($data["type"] == 3) {
            $materialId = $data["material"];
            $mq = $db->query("SELECT `name` FROM `material` WHERE `id` = '$materialId'");
            $m = $mq->fetch();
            $sm = strtoupper($m["lname"][0]);
        } else if ($data["type"] == OT::AUTO_WYCENA_BLACH_MULTI_ZATWIERDZONA || OT::AUTO_WYCENA_BLACH_MULTI_DODANE_DO_ZAMOWIENIA) { //Multi blachy
            $sm = $data["material_name"];
            $m["lname"] = $sm;
        } else {
            $materialSname = $data["mtype"];
            $mq = $db->query("SELECT `name` FROM `material` WHERE `name` = '$materialSname'");
            $m = $mq->fetch();
            $sm = strtoupper($m["lname"][0]);
        }


        $dpath .= "/" . $m["lname"];
        $dpath .= "/" . str_replace(".", "P", $dim);

        //Name
        $esrc = explode(".", $data["detail_name"]);
        $ext = end($esrc);

        $j_atr = json_decode($data["atribute"], true);
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

        $cid = $data["cid"];
        $thickness = $data["mpc_thickness"];

        $hash = md5(time());
        $newName = $cid . "-" . $data["pieces"] . "X" . $thickness . "-$sm-$item" . $atribute . '-' . $hash . "." . $ext;

        //Original path
        if ($data["type"] == 2) {
            if ($data["radius"] > 0) {
                $_src = $data["project_name"] . "/V" . $data["version"] . "/R" . $data["radius"] . "/shd/" . $data["detail_name"];
            } else {
                $_src = $data["project_name"] . "/V" . $data["version"] . "/shd/" . $data["detail_name"];
            }
        } else {
            $_src = $data["project_name"] . "/V" . $data["version"] . "/dxf/" . $data["detail_name"];
        }

        if (file_exists($dpath) == false) {
            make_dir($dpath);
        }

        $db->query("INSERT INTO `oitems` (`oid`, `mpw`, `code`, `src`, `path`, `did`) VALUES ('$oid', '$item', '$newName', '$_src', '$dpath', $didId)");
        $db->query("UPDATE `mpw` SET `type` = '$new_type' WHERE `id` = '$item'");
        $db->query("UPDATE `order` SET `status` = '2' WHERE `id` = '$oid'");
    }

    die("1");
}

//Get items 
if ($action == 6) {
    $oid = intval($_GET["oid"]);

    $orderDataQuery = $db->prepare("
        SELECT
        o.status,
        oi.id,
        oi.code,
        mpw.id as mpw,
        mpw.did,
        mpw.material,
        m.name as material_name,
        mpw.pieces,
        mpw.atribute,
        mpw.mcp,
        mpw.type,
        oi.did as order_detail
        FROM `order` o
        LEFT JOIN oitems oi ON oi.oid = o.id
        LEFT JOIN mpw mpw ON mpw.id = oi.mpw
        LEFT JOIN material m ON m.id = mpw.material
        WHERE 
        o.id = :oid
    ");
    $orderDataQuery->bindValue(":oid", $oid, PDO::PARAM_INT);
    $orderDataQuery->execute();

    $orderData = $orderDataQuery->fetchAll(PDO::FETCH_ASSOC);
    if ($orderData[0]["status"] > 2) {
        die("2");
    }

    $content = "";

    foreach ($orderData as $oitem) {
        $wid = $oitem["mpw"];

        $atribute_s = "";
        $atribute = json_decode($oitem["atribute"]);
        if (count($atribute) > 0) {
            foreach ($atribute as $a) {
                $atribute_s .= " <b>" . _getChecboxText($a) . "</b> ";
            }
        }

        $did = $oitem["did"];
        $nameq = $db->query("SELECT `src` FROM `details` WHERE `id` = '$did'");
        $namef = $nameq->fetch();
        $dname = $namef["src"];

        if ($oitem["type"] == 2 || $oitem["type"] == 7) {
            $mpcq = $db->query("SELECT `last_price_all_netto` FROM `mpc` WHERE `wid` = '$wid'");
            $mpc = $mpcq->fetch();
            $cost = $mpc["last_price_all_netto"];
        }
        if ($oitem["type"] == 4 || $oitem["type"] == 8) {
            $qpc = $db->query("SELECT `priceset` FROM `profile_costing` WHERE `id` = '" . $mpw["mcp"] . "'");
            $pc = $qpc->fetch();
            $cost = $pc["priceset"];
        } else if ($oitem["type"] == OT::AUTO_WYCENA_BLACH_MULTI_DODANE_DO_ZAMOWIENIA) {
            $detailDataQuery = $db->prepare("
              SELECT 
              d.src,
              ds.price
              FROM 
              details d
              LEFT JOIN plate_multiPartDetails pd ON pd.did = d.id
              LEFT JOIN plate_multiPartCostingDetailsSettings ds ON ds.detaild_id = d.id AND ds.directory_id = pd.dirId
              WHERE 
              d.id = :did
              AND pd.mpw = :mpw
            ");
            $detailDataQuery->bindValue(":did", $oitem["order_detail"], PDO::PARAM_INT);
            $detailDataQuery->bindValue(":mpw", $oitem["mpw"], PDO::PARAM_INT);
            $detailDataQuery->execute();
            $detailData = $detailDataQuery->fetch();

            $cost = $detailData["price"];
            $dname = $detailData["src"];
        } else {
            $cost = 0;
        }

        $content .= '<tr id="' . $oitem["mpw"] . '_mpwoi"><td>' . $oitem["mpw"] . '</td>'
            . '<td>' . $dname . '</td>'
            . '<td>' . $oitem["code"] . '</td>'
            . '<td>' . $oitem["material_name"] . '</td>'
            . '<td>' . $oitem["pieces"] . ' <i class="fa fa-pencil pediti" id="' . $wid . '_mpeidd" style="cursor: pointer"></i></td>'
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
    $db->query("UPDATE tickets SET state = 'do zaprogramowania' WHERE order_id = " . $oid);

    $oitemsQuery = $db->prepare("
      SELECT 
      oi.*,
      mpw.type
      FROM `oitems` oi
      LEFT JOIN mpw mpw ON mpw.id = oi.mpw
      WHERE 
      oi.`oid` = :oid
    ");
    $oitemsQuery->bindValue(":oid", $oid, PDO::PARAM_INT);
    $oitemsQuery->execute();
    foreach ($oitemsQuery->fetchAll(PDO::FETCH_ASSOC) as $row) {
        //MPW STATUS UPDATE
        $mpw = $row["mpw"];
        $type = $row["type"];

        $ntype = 0;
        switch ($type) {
            case OT::AUTO_WYCENA_DODANE_DO_ZAMOWIENIA:
                $ntype = OT::AUTO_WYCENA_ZABLOKOWANA_EDYCJA;
                break;
            case OT::RECZNA_WYCENA_PROFULU_DODANE_DO_ZAMOWIENIA:
                $ntype = OT::RECZNA_WYCENA_PROFILU_ZABLOKOWANA_EDYCJA;
                break;
            case OT::RECZNA_WYCENA_BLACH_DODANE_DO_ZAMOWIENIA:
                $ntype = OT::RECZNA_WYCENA_BLACHY_ZABLOKOWANA_EDYCJA;
                break;
        }

        if ($ntype != 0) {
            $mpwUpdateQuery = $db->prepare("UPDATE `mpw` SET `type` = :ntype WHERE `id` = :mpw");
            $mpwUpdateQuery->bindValue(':ntype', $ntype, PDO::PARAM_INT);
            $mpwUpdateQuery->bindValue(':mpw', $mpw, PDO::PARAM_INT);
            $mpwUpdateQuery->execute();
        }

        if (@file_exists($row["src"])) {
            copy($row["src"], $row["path"] . '/' . $row["code"]);
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
