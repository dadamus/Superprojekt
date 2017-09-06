<?php
include dirname(__FILE__) . '/../class/material.php';
$material = new Material();


$qdata = $db->query("SELECT * FROM `mpc` WHERE `id` = '$nr'");
$data = $qdata->fetch();

$wid = $data["wid"];
$qmpw = $db->query("SELECT `did` FROM `mpw` WHERE `id` = '$wid'");
$mpw = $qmpw->fetch();
$did = $mpw["did"];

$dq = $db->query("SELECT `src`, `pid` FROM `details` WHERE `id` = '$did'");
$dqf = $dq->fetch();
$src = $dqf["src"];
$pid = $dqf["pid"];

$pq = $db->query("SELECT `name` FROM `projects` WHERE `id` = '$pid'");
$fpq = $pq->fetch();
$pname = $fpq["name"];
?>
<div id="flyingWindowComments">
    <div id="fwcIcon" class="font-dark"><i class="fa fa-comments"></i></div>
    <div id="fwcContent">
        <div style="float: right; margin-right: 10px;"><i id="shoutrefreshb" class="fa fa-refresh" style="cursor: pointer;"></i></div>
        <div style="clear: both"></div>
        <div id="shouts" style="height: 275px; padding-top: 10px;">
            <?php
            $qshout = $db->query("SELECT * FROM `comments` WHERE `type` = 'costing' AND `eid` = '$nr' ORDER BY `id` DESC");
            foreach ($qshout as $shout) {
                $uid = $shout["uid"];
                $uq = $db->query("SELECT `name` FROM `accounts` WHERE `id` = '$uid'");
                $uf = $uq->fetch();
                $user = $uf["name"];

                echo '<div class="shout"><div class="shout-header"><b>' . $user . '</b>  <div style="float: right">' . $shout["date"] . '</div></div>' . $shout["content"] . '</div>';
            }
            ?>
        </div>
        <div id="stextarea" style="margin-top: 5px;">
            <form id="addshout" action="?">
                <textarea style="width: 100%;" id="shoutbox"></textarea>
                <button type="submit" class="btn blue btn-block" id="addcoment">Dodaj</button>
            </form>
        </div>
    </div>
</div>
<div id="loading-div" style="position: fixed; left: 48%; top: 30%; display: none; z-index: 100;">
    <img src="<?php echo $site_path; ?>/images/loading.gif" alt="Ładowanie..."/>
</div>
<div class="row">
    <div class="col-lg-12">
        <h2 class="page-title">Auto wycena profilu <small><a href="<?php echo $site_path; ?>/project/4/<?php echo $pid; ?>/"><?php echo $pname ?></a>/<?php echo $src; ?></small></h2>
    </div>
</div>
<form id="ceForm" method="POST">
    <div class="row" style="position: relative; z-index: 0;">
        <div class="col-md-2">
            <div class="stats-heading">Materiał</div>
            <div class="stats-body-alt"> 
                <div class="text-center">
                    <select class="form-control required" name="material" id="materialSelect" disabled>
                        <?php
                        for ($i = 1; $i <= count($material->name); $i++) {
                            if ($data["mtype"] == $material->name[$i]) {
                                echo '<option value="' . $i . '" selected="selected">' . $material->name[$i] . '</option>';
                            } else {
                                echo '<option value="' . $i . '">' . $material->name[$i] . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="stats-footer"></div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stats-heading">Typ</div>
            <div class="stats-body-alt"> 
                <div class="text-center">
                    <select name="type" id="type" class="form-control" disabled>
                        <?php
                        $stype[$data["type"]] = 'selected="selected"';
                        ?>
                        <option value="0" <?php echo @$stype[0]; ?>>Profil</option>
                        <option value="1" <?php echo @$stype[1]; ?>>Rura</option>
                        <option value="2" <?php echo @$stype[2]; ?>>Ceownik</option>
                        <option value="3" <?php echo @$stype[3]; ?>>Kątownik</option>
                    </select>
                </div>
                <div class="stats-footer"></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-heading">Wymiar</div>
            <div class="stats-body-alt"> 
                <div class="text-center">
                    <?php
                    $whr = explode("R", $data["wh"]);
                    $wh = explode("X", $whr[0]);
                    ?>
                    <input type="text" name="dimension1" value="<?php echo floatval($wh[0]); ?>" id="diemension1" class="form-control" style="text-align: center; width: 33%; float: left;" readonly>
                    <input type="text" name="dimension2" value="<?php echo floatval($wh[1]); ?>"id="diemension2" class="form-control" style="text-align: center; width: 33%; float: left;" readonly>
                    <input type="text" name="dimension3" value="<?php echo floatval($data["thickness"]); ?>"id="diemension3" class="form-control" style="text-align: center; width: 33%; float: left;" readonly>
                    <div style="clear: both"></div>
                </div>
                <div class="stats-footer"></div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stats-heading">Liczba sztuk</div>
            <div class="stats-body-alt"> 
                <div class="text-center">
                    <input type="number" value="<?php echo $data["d_qty"]; ?>" name="d_qty" id="d_qty" class="form-control" readonly>
                </div>
                <div class="stats-footer"></div>
            </div> 
        </div>
    </div>
    <div class="row" style="margin-top: 20px; position: relative; z-index: 0;">
        <div class="col-md-6">
            <div class="cold-md-12">
                <div class="portlet box green-jungle">
                    <div class="portlet-title">
                        <div class="caption">Koszt cięcia</div>
                    </div>
                    <div class="portlet-body flip-scroll">
                        <table class="table table-striped table-condensed flip-content">
                            <thead class="flip-content">
                                <tr>
                                    <td style="width: 30%;">Nazwa</td>
                                    <td style="width: 35%;">Komplet</td>
                                    <td style="width: 35%;">Detal</td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td>Czas cięcia</td><td><div class="input-group"><input type="text" value="<?php echo _secToTime($data["cut_all_time"]); ?>" name="cut_all_time" id="cut_all_time" class="form-control"><span class="input-group-btn" style="display: none;"><button class="btn red resetInput" type="button"><i class="fa fa-reply"></i></button></span></div></td><td></td></tr>
                                <tr><td>Czyste cięcie</td><td><div class="input-group"><input type="text" value="<?php echo round($data["clean_cut"], 2); ?>" name="clean_cut" id="clean_cut" class="form-control"><span class="input-group-btn" style="display: none;"><button class="btn red resetInput" type="button"><i class="fa fa-reply"></i></button></span></div></td><td><div class="input-group"><input type="text" value="<?php echo round($data["clean_cut"] / $data["d_qty"], 2); ?>" name="d_clean_cut" id="d_clean_cut" class="form-control"><span class="input-group-btn" style="display: none;"><button class="btn red resetInput" type="button"><i class="fa fa-reply"></i></button></span></div></td></tr>
                                <tr><td>Cięcie netto:</td><td><div class="input-group"><input type="text" value="<?php echo round($data["cut_all_netto"], 2); ?>" name="cut_all_netto" id="cut_all_netto" class="form-control"><span class="input-group-btn" style="display: none;"><button class="btn red resetInput" type="button"><i class="fa fa-reply"></i></button></span></div></td><td><div class="input-group"><input type="text" value="<?php echo round($data["d_cut_all_netto"], 2); ?>" name="d_cut_all_netto" id="d_cut_all_netto" class="form-control"><span class="input-group-btn" style="display: none;"><button class="btn red resetInput" type="button"><i class="fa fa-reply"></i></button></span></div></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet box red-flamingo">
                        <div class="portlet-title">
                            <div class="caption">Koszty materiału</div>
                        </div>
                        <div class="portlet-body flip-scroll">
                            <table class="table table-striped table-condensed flip-content">
                                <thead class="flip-content">
                                    <tr>
                                        <td style="width: 30%;">Nazwa</td>
                                        <td style="width: 35%;">Komplet</td>
                                        <td style="width: 35%;">Detal</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr><td>Cena m/b</td><td><div class="input-group"><input type="text" value="<?php echo $data["c_mb"]; ?>" name="c_mb" id="c_mb" class="form-control"><span class="input-group-btn" style="display: none;"><button class="btn red resetInput" type="button"><i class="fa fa-reply"></i></button></span></div></td><td></td></tr>
                                    <tr><td>Długość materiału:</td><td><div class="input-group "><input type="text" value="<?php echo $data["dlugosc_mat"]; ?>" name="dlugosc_mat" id="dlugosc_mat" class="form-control"><span class="input-group-btn" style="display: none;"><button class="btn red resetInput" type="button"><i class="fa fa-reply"></i></button></span></div></td><td></td></tr>
                                    <tr><td>Zajętość materiału:</td><td><div class="input-group "><input type="text" value="<?php echo $data["zajetosc_mat"]; ?>" name="zajetosc_mat" id="zajetosc_mat" class="form-control"><span class="input-group-btn" style="display: none;"><button class="btn red resetInput" type="button"><i class="fa fa-reply"></i></button></span></div></td><td></td></tr>
                                    <tr><td>Zajętość odpadu:</td><td><div class="input-group "><input type="text" value="<?php echo $data["rm_odpad"]; ?>" name="rm_odpad" id="rm_odpad" class="form-control"><span class="input-group-btn" style="display: none;"><button class="btn red resetInput" type="button"><i class="fa fa-reply"></i></button></span></div></td><td></td></tr>
                                    <tr><td>Koszt materiału:</td><td><div class="input-group "><input type="text" value="<?php echo $data["tcost"]; ?>" name="tcost" id="tcost" class="form-control"><span class="input-group-btn" style="display: none;"><button class="btn red resetInput" type="button"><i class="fa fa-reply"></i></button></span></div></td><td></td></tr>
                                    <tr><td>Waga odpad:</td><td><div class="input-group "><input type="text" value="<?php echo $data["waga_rm"]; ?>" name="waga_rm" id="waga_rm" class="form-control"><span class="input-group-btn" style="display: none;"><button class="btn red resetInput" type="button"><i class="fa fa-reply"></i></button></span></div></td><td></td></tr>
                                    <tr><td>Wartość odpadu:</td><td><div class="input-group "><input type="text" value="<?php echo $data["rm_value"]; ?>" name="rm_value" id="rm_value" class="form-control"><span class="input-group-btn" style="display: none;"><button class="btn red resetInput" type="button"><i class="fa fa-reply"></i></button></span></div></td><td><div class="input-group "><input type="text" value="<?php echo $data["d_rmn"]; ?>" name="d_rmn" id="d_rmn" class="form-control"><span class="input-group-btn" style="display: none;"><button class="btn red resetInput" type="button"><i class="fa fa-reply"></i></button></span></div></td></tr>
                                    <tr><td>Cena za materiał:</td><td><div class="input-group "><input type="text" value="<?php echo $data["cost_all_price"]; ?>" name="cost_all_price" id="cost_all_price" class="form-control"><span class="input-group-btn" style="display: none;"><button class="btn red resetInput" type="button"><i class="fa fa-reply"></i></button></span></div></td><td><div class="input-group "><input type="text" value="<?php echo $data["d_mat"]; ?>" name="d_mat" id="d_mat" class="form-control"><span class="input-group-btn" style="display: none;"><button class="btn red resetInput" type="button"><i class="fa fa-reply"></i></button></span></div></td></tr>
                                    <tr><td>Discount:</td><td><div class="input-group "><input type="text" value="<?php echo $data["mat_discount"]; ?>" name="mat_discount" id="mat_discount" class="form-control"><span class="input-group-btn" style="display: none;"><button class="btn red resetInput" type="button"><i class="fa fa-reply"></i></button></span></div></td><td style="text-align: center; vertical-align: center;"><a type="button" class="btn btn btn-default" data-toggle="modal" href="#discount_modal">Edytuj</a></td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet box dark">
                        <div class="portlet-title">
                            <div class="caption">Koszty dodatkowe</div>
                        </div>
                        <div class="portlet-body">
                            <table class="table table-striped" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <td style="width: 5%"></td>
                                        <td style="width: 20%;">Nazwa</td>
                                        <td style="width: 37%;">zł/szt</td>
                                        <td style="width: 37%;">zł/komplet</td>
                                    </tr>
                                </thead>
                                <?php
                                //Atributes
                                $atributes = explode("|", $data["atributes"]);
                                if (count(@$atributes) > 0) {
                                    foreach ($atributes as $key) {
                                        if ($key == null) {
                                            continue;
                                        } else {
                                            $t_key = explode(":", $key);
                                            $name = $t_key[0];
                                            $val = $t_key[1];
                                            $GLOBALS["a" . $name . "i2"] = $val;
                                        }
                                    }
                                }

                                function getAtribute($id, $type) {
                                    global $data;
                                    if (@$GLOBALS["a" . $id . "i2"] != null) {
                                        if ($type == 1) {
                                            echo round($GLOBALS["a" . $id . "i2"] / $data["d_qty"], 2);
                                        } else {
                                            echo $GLOBALS["a" . $id . "i2"];
                                        }
                                    }
                                }
                                ?>
                                <tbody>
                                    <tr>
                                        <td><input type="checkbox" name="atribute[]" value="1" style="width: 20px; height: 20px;"/></td>
                                        <td>Gięcie</td>
                                        <td><div class="input-group"><input type="text" name="a1i1" id="a1i1" value="<?php getAtribute(1, 1); ?>" class="form-control ai"/><span class="input-group-btn" style="display: none;"><button class="btn red resetInput" type="button"><i class="fa fa-reply"></i></button></span></div></td>
                                        <td><div class="input-group"><input type="text" name="a1i2" id="a1i2" value="<?php getAtribute(1, 2); ?>" class="form-control aik"/><span class="input-group-btn" style="display: none;"><button class="btn red resetInput" type="button"><i class="fa fa-reply"></i></button></span></div></td>
                                    </tr>
                                    <tr>
                                        <td><input type="checkbox" name="atribute[]" value="2" class="form-control" style="width: 20px; height: 20px;"/></td>
                                        <td>Projekt</td>
                                        <td><div class="input-group"><input type="text" name="a2i1" id="a2i1" value="<?php getAtribute(2, 1); ?>" class="form-control ai"/><span class="input-group-btn" style="display: none;"><button class="btn red resetInput" type="button"><i class="fa fa-reply"></i></button></span></div></td>
                                        <td><div class="input-group"><input type="text" name="a2i2" id="a2i2" value="<?php getAtribute(2, 2); ?>" class="form-control aik"/><span class="input-group-btn" style="display: none;"><button class="btn red resetInput" type="button"><i class="fa fa-reply"></i></button></span></div></td>
                                    </tr>
                                    <tr>
                                        <td><input type="checkbox" name="atribute[]" value="3" class="form-control" style="width: 20px; height: 20px;"/></td>
                                        <td>Spawanie</td>
                                        <td><div class="input-group "><input type="text" name="a3i1" id="a3i1" value="<?php getAtribute(3, 1); ?>" class="form-control ai"/><span class="input-group-btn" style="display: none;"><button class="btn red resetInput" type="button"><i class="fa fa-reply"></i></button></span></div></td>
                                        <td><div class="input-group "><input type="text" name="a3i2" id="a3i2" value="<?php getAtribute(3, 2); ?>" class="form-control aik"/><span class="input-group-btn" style="display: none;"><button class="btn red resetInput" type="button"><i class="fa fa-reply"></i></button></span></div></td>
                                    </tr>
                                    <tr>
                                        <td><input type="checkbox" name="atribute[]" value="4" class="form-control" style="width: 20px; height: 20px;"/></td>
                                        <td>Malowanie</td>
                                        <td><div class="input-group"><input type="text" name="a4i1" id="a4i1" value="<?php getAtribute(4, 1); ?>" class="form-control ai"/><span class="input-group-btn" style="display: none;"><button class="btn red resetInput" type="button"><i class="fa fa-reply"></i></button></span></div></td>
                                        <td><div class="input-group"><input type="text" name="a4i2" id="a4i2" value="<?php getAtribute(4, 2); ?>" class="form-control aik"/><span class="input-group-btn" style="display: none;"><button class="btn red resetInput" type="button"><i class="fa fa-reply"></i></button></span></div></td>
                                    </tr>
                                    <tr>
                                        <td><input type="checkbox" name="atribute[]" value="5" class="form-control" style="width: 20px; height: 20px;"/></td>
                                        <td>Ocynkowanie</td>
                                        <td><div class="input-group"><input type="text" name="a5i1" id="a5i1" value="<?php getAtribute(5, 1); ?>" class="form-control ai"/><span class="input-group-btn" style="display: none;"><button class="btn red resetInput" type="button"><i class="fa fa-reply"></i></button></span></div></td>
                                        <td><div class="input-group"><input type="text" name="a5i2" id="a5i2" value="<?php getAtribute(5, 2); ?>" class="form-control aik"/><span class="input-group-btn" style="display: none;"><button class="btn red resetInput" type="button"><i class="fa fa-reply"></i></button></span></div></td>
                                    </tr>
                                    <tr>
                                        <td><input type="checkbox" name="atribute[]" value="6" class="form-control" style="width: 20px; height: 20px;"/></td>
                                        <td>Gwintowanie</td>
                                        <td><div class="input-group"><input type="text" name="a6i1" id="a6i1" value="<?php getAtribute(6, 1); ?>" class="form-control ai"/><span class="input-group-btn" style="display: none;"><button class="btn red resetInput" type="button"><i class="fa fa-reply"></i></button></span></div></td>
                                        <td><div class="input-group"><input type="text" name="a6i2" id="a6i2" value="<?php getAtribute(6, 2); ?>" class="form-control aik"/><span class="input-group-btn" style="display: none;"><button class="btn red resetInput" type="button"><i class="fa fa-reply"></i></button></span></div></td>
                                    </tr>
                                    <tr>
                                        <td><input type="checkbox" name="atribute[]" value="7" class="form-control" style="width: 20px; height: 20px;"/></td>
                                        <td>Common Cut</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-8">
                    <div class="portlet box yellow-mint">
                        <div class="portlet-title">
                            <div class="caption">Dane inicjacyjne</div>
                        </div>
                        <div class="portlet-body flip-scroll">
                            <table class="table table-striped table-condensed flip-content">
                                <tbody>
                                    <tr><td style="width: 40%;">Cięcie:</td><td style="width: 60%;"><div class="input-group "><input type="text" value="<?php echo $data["scut"]; ?>" name="scut" id="scut" class="form-control"><span class="input-group-btn" style="display: none;"><button class="btn red resetInput" type="button"><i class="fa fa-reply"></i></button></span></div></td></tr>
                                    <tr><td>Współczynnik p:</td><td><div class="input-group "><input type="text" value="<?php echo $data["sp_factor"]; ?>" name="sp_factor" id="sp_factor" class="form-control"><span class="input-group-btn" style="display: none;"><button class="btn red resetInput" type="button"><i class="fa fa-reply"></i></button></span></div></td></tr>
                                    <tr><td>Cena odpadu:</td><td><div class="input-group "><input type="text" value="<?php echo $data["mwaste"]; ?>" name="mwaste" id="mwaste" class="form-control"><span class="input-group-btn" style="display: none;"><button class="btn red resetInput" type="button"><i class="fa fa-reply"></i></button></span></div></td></tr>
                                    <tr><td>Czas przeładunku:</td><td><div class="input-group "><input type="text" value="<?php echo _secToTime($data["sotime"]); ?>" name="sotime" id="sotime" class="form-control"><span class="input-group-btn" style="display: none;"><button class="btn red resetInput" type="button"><i class="fa fa-reply"></i></button></span></div></td></tr>
                                    <tr><td>Koszt przeładunku:</td><td><div class="input-group "><input type="text" value="<?php echo $data["socost"]; ?>" name="socost" id="socost" class="form-control"><span class="input-group-btn" style="display: none;"><button class="btn red resetInput" type="button"><i class="fa fa-reply"></i></button></span></div></td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="portlet box yellow-lemon">
                    <div class="portlet-title">
                        <div class="caption">Dane ostateczne</div>
                    </div>
                    <div class="portlet-body flip-scroll">
                        <table class="table table-striped table-condensed flip-content">
                            <thead class="flip-content">
                                <tr>
                                    <td style="width: 30%;">Nazwa</td>
                                    <td style="width: 35%;">Netto</td>
                                    <td style="width: 35%;">Brutto</td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td>Koszt detalu:</td><td><div class="input-group "><input type="text" value="<?php echo round($data["d_last_price_n"], 2); ?>" name="d_last_price_n" id="d_last_price_n" class="form-control"><span class="input-group-btn" style="display: none;"><button class="btn red resetInput" type="button"><i class="fa fa-reply"></i></button></span></div></td><td><div class="input-group "><input type="text" value="<?php echo round($data["d_last_price_n"] * 1.23, 2); ?>" name="d_last_price_n_brutto" id="d_last_price_n_brutto" class="form-control"><span class="input-group-btn" style="display: none;"><button class="btn red resetInput" type="button"><i class="fa fa-reply"></i></button></span></div></td></tr>
                                <tr><td>Koszt kompletu:</td><td><div class="input-group "><input type="text" value="<?php echo round($data["last_price_all_netto"], 2); ?>" name="last_price_all_netto" id="last_price_all_netto" class="form-control"><span class="input-group-btn" style="display: none;"><button class="btn red resetInput" type="button"><i class="fa fa-reply"></i></button></span></div></td><td><div class="input-group "><input type="text" value="<?php echo round($data["last_price_all_netto"] * 1.23, 2); ?>" name="last_price_all_netto_brutto" id="last_price_all_netto_brutto" class="form-control"><span class="input-group-btn" style="display: none;"><button class="btn red resetInput" type="button"><i class="fa fa-reply"></i></button></span></div></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="portlet box blue-sharp">
                    <div class="portlet-title">
                        <div class="caption">Dane dodatkowe</div>
                    </div>
                    <div class="portlet-body flip-scroll">
                        <table class="table table-striped table-condensed flip-content">
                            <thead class="flip-content">
                                <tr>
                                    <td style="width: 30%;">Nazwa</td>
                                    <td style="width: 35%;">Komplet</td>
                                    <td style="width: 35%;">Detal</td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td>Waga:</td><td><div class="input-group "><input type="text" value="<?php echo round($data["d_weight"] * $data["d_qty"], 2); ?>" name="tweight" id="tweight" class="form-control" readonly="readonly"><span class="input-group-btn" style="display: none;"><button class="btn red resetInput" type="button"><i class="fa fa-reply"></i></button></span></div></td><td><div class="input-group "><input type="text" value="<?php echo round($data["d_weight"], 2); ?>" name="d_weight" id="d_weight" class="form-control" readonly="readonly"><span class="input-group-btn" style="display: none;"><button class="btn red resetInput" type="button"><i class="fa fa-reply"></i></button></span></div></td></tr>
                                <tr><td>Waga 1m:</td><td><div class="input-group "><input type="text" value="<?php echo round($data["waga_1m"], 2); ?>" name="waga_1m" id="waga_1m" class="form-control" readonly="readonly"><span class="input-group-btn" style="display: none;"><button class="btn red resetInput" type="button"><i class="fa fa-reply"></i></button></span></div></td><td></td></tr>
                                <tr><td>Cena mat/kg:</td><td><div class="input-group "><input type="text" value="<?php echo round($data["cost_mat_kg"], 2); ?>" name="cost_mat_kg" id="cost_mat_kg" class="form-control" readonly="readonly"><span class="input-group-btn" style="display: none;"><button class="btn red resetInput" type="button"><i class="fa fa-reply"></i></button></span></div></td><td></td></tr>
                                <tr><td>Wartość przeładunku:</td><td><div class="input-group "><input type="text" value="<?php echo round($data["przeladunek"], 2); ?>" name="przeladunek" id="przeladunek" class="form-control" readonly="readonly"><span class="input-group-btn" style="display: none;"><button class="btn red resetInput" type="button"><i class="fa fa-reply"></i></button></span></div></td><td><div class="input-group "><input type="text" value="<?php echo round($data["przeladunek"] / $data["d_qty"], 2); ?>" name="d_przeladunek" id="d_przeladunek" class="form-control" readonly="readonly"><span class="input-group-btn" style="display: none;"><button class="btn red resetInput" type="button"><i class="fa fa-reply"></i></button></span></div></td></tr>
                                <tr><td>Detali:</td><td><div class="input-group "><input type="text" value="<?php echo $data["d_qty"]; ?>" name="d_qty" id="d_qty" class="form-control" readonly="readonly"><span class="input-group-btn" style="display: none;"><button class="btn red resetInput" type="button" readonly="readonly"><i class="fa fa-reply"></i></button></span></div></td><td></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="portlet box grey-mint">
                    <div class="portlet-title">
                        <div class="caption">
                            Historia
                        </div>
                        <div class="tools">
                            <a href="javascript:;" class="expand"></a>
                        </div>
                    </div>
                    <div class="portlet-body flip-scroll" style="display: none;">
                        <div class="timeline">
                            <?php
                            $q_timeline = $db->query("SELECT * FROM `backup` WHERE `type` = '1' AND `item` = '$nr'");
                            foreach ($q_timeline as $item) {
                                $uid = $item["user"];
                                $q_user = $db->query("SELECT `name` FROM `accounts` WHERE `id` = '$uid'");
                                $f_user = $q_user->fetch();
                                $user_name = $f_user["name"];

                                echo '<div class="timeline-item"><div class="timeline-badge"><div class="timeline-icon"><i class="icon-drawer font-green-haze"></i></div></div>';
                                echo '<div class="timeline-body">';
                                echo '<div class="timeline-body-arrow"></div>';
                                echo '<div class="timeline-body-head"><div class="timeline-body-head-caption"><span class="timeline-body-alerttitle font-green-haze">' . $user_name . '</span><span class="timeline-body-time font-grey-cascade">' . $item["date"] . '</span></div>';
                                echo '<div class="timeline-body-head-actions"><div class="btn-group dropup"><button class="btn btn-circle red btn-sm dropdown-toggle" type="button" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" aria-expanded="true">Akcje <i class="fa fa-angle-down"></i></button>';
                                echo '<ul class="dropdown-menu pull-right" role="menu" style="position: absolute;"><li><a href="javascript:;" class="bHView" id="' . $item["id"] . '_bh"><i class="fa fa-search"></i> Podgląd</a></li><li><a href="javascript:;" class="bHRestore" id="' . $item["id"] . '_bh"><i class="fa fa-history"></i> Przywróć</a></li></ul></div></div>';
                                echo '</div>';
                                //Content create
                                $inputs = json_decode($item["comments"], true);
                                $snapshot = json_decode(stripslashes($item["snapshot"]), true);
                                $content = "<ul>";
                                foreach ($inputs as $input => $val) {
                                    if (isset($snapshot[$input])) {
                                        $bc = $snapshot[$input];
                                    } else {
                                        $bc = "no-data";
                                    }
                                    $content .= "<li><b>$input</b> ($bc) <i class=\"fa fa-angle-double-right\"></i> <i>$val</i></li>";
                                }
                                $content .= "</ul>";
                                echo '<div class="timeline-body-content"><span class="font-grey-cascade">' . $content . '</span></div>';
                                echo '</div></div>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-9"></div>
        <div class="col-lg-3">
            <div class="widget">
                <div class="btn-group">
                    <button class="btn btn-success" type="submit" id="bSave">Zapisz</button>
                    <button class="btn btn-default" type="button" id="cancel">Anuluj</button>
                </div>
            </div>
        </div>
    </div>
</form>

<div class="modal fade bs-modal-sm" id="discount_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            <h4 class="modal-title">Discount</h4>
        </div>
        <div class="modal-body" style="text-align: center;">
            <input id="knob" data-angleoffset="-125" data-anglearc="250" data-min="-100" data-max="100" value="<?php echo $data["mat_discount"]; ?>"/>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn dark btn-outline" data-dismiss="modal">Anuluj</button>
            <button type="button" class="btn green" id="discount_button">Zapisz</button>
        </div>
    </div>
</div>

<script type="text/javascript">
    Math.decimal = function (n, k)
    {
        var factor = Math.pow(10, k + 1);
        n = Math.round(Math.round(n * factor) / 10);
        return n / (factor / 10);
    };

    var inputUnits = {};

    function inputCreateUnits(key, tw) {
        inputUnits[key] = tw;
        var ntw;

        if (tw >= 1000000) {
            ntw = Math.decimal(tw / 1000000, 2) + " t";
        } else if (tw >= 1000) {
            ntw = Math.decimal(tw / 1000, 2) + " kg";
        } else {
            ntw = tw + " g";
        }
        return ntw;
    }

    var mpc = <?php echo $nr; ?>;
    var ajax, timeout, reload_start;
    var reload_id = 0;
    var input_name = new Object();

    var reload_shout = setTimeout(function () {
        refreshShout();
    }, 5000);
    function refreshShout() {
        $.ajax({
            url: "<?php echo $site_path; ?>/engine/addcomment.php?action=1&eid=<?php echo $nr; ?>&type=2"
        }).done(function (msg) {
            $("#shouts").html(msg);
        });
        reload_shout = setTimeout(function () {
            refreshShout();
        }, 5000);
    }
    ;


    function blockSite() {
        App.blockUI({boxed: !0});
    }
    function unblockSite() {
        App.unblockUI();
    }

    function reload(add, cb) {
        if (ajax !== null && typeof (ajax) !== 'undefined') {
            alert(ajax);
            ajax.abort();
        }

        blockSite();

        reload_id += 1;
        clearTimeout(timeout);
        timeout = setTimeout(function () {
            ajax_timeout(reload_id);
        }, 5000);

        if (typeof (add) === 'undefined') {
            add = "";
        }

        var reload_next = false;
        if (typeof (cb) !== 'undefined') {
            reload_next = true;
        }

        var json_inputs = JSON.stringify(input_name);
        console.log(input_name);
        console.log("json_inputs => " + json_inputs);
        var material = $("#materialSelect").val();

        $("#tweight").val(inputUnits["tweight"]);
        $("#d_weight").val(inputUnits["d_weight"]);

        ajax = $.ajax({
            type: "POST",
            url: "<?php echo $site_path; ?>/aengine/modules/mpc_edit.php?mpc=" + mpc + "&reload_id=" + reload_id + add,
            data: $("#ceForm").serialize() + "&json_inputs=" + json_inputs + "&material=" + material,
            success: function (jResponse) {
                var response = jQuery.parseJSON(jResponse);

                if (reload_id == parseInt(response.reload_id)) {
                    ajax = null;

                    console.log("response.c_input_name => " + response.c_input_name_backup);

                    var inputs = jQuery.parseJSON(response.inputs)
                    for (var key in inputs) {

                        var obj = inputs[key];
                        if (typeof (input_name[obj.name]) !== 'undefined')
                        {
                            console.log("| continue: " + obj.name);
                            continue;
                        }
                        if (obj.name == "tweight") {
                            $("#" + obj.name).val(inputCreateUnits("tweight", obj.val));
                        } else if (obj.name == "d_weight") {
                            $("#" + obj.name).val(inputCreateUnits("d_weight", obj.val));
                        }
                        else {
                            $("#" + obj.name).val(obj.val);
                        }
                        if ($("#" + obj.name + "_brutto").length) {
                            $("#" + obj.name + "_brutto").val(Math.decimal(obj.val * 1.23, 2));
                        }
                    }

                    if (typeof (response.c_input_name_backup) !== 'undefined') {
                        var j_inputs_name = jQuery.parseJSON(response.c_input_name_backup);
                        console.log("j_inputs_name" + j_inputs_name);
                        if (typeof (j_inputs_name) === 'object') {
                            input_name = new Object();
                            for (var key in j_inputs_name) {
                                console.log(key + "=>" + j_inputs_name[key]);
                                input_name[key] = j_inputs_name[key];
                                var ti = $("#" + key);
                                $(ti).parent().find("span").fadeIn();
                            }
                        }
                    }

                    console.log(input_name);

                    if (reload_next == true) {
                        setTimeout(function () {
                            reload();
                        }, 200);
                    } else {
                        unblockSite();
                    }
                } else {
                    alert(ri);
                }
                clearTimeout(timeout);
            }
        });
    }

    function ajax_timeout(rid) {
        if (reload_id == rid) {
            alert("Limit czasu liczenia przekroczony! Ponawiam...");
            if (ajax !== null && typeof (ajax) !== 'undefined') {
                ajax.abort();
            }
            reload();
        }
    }

    function input_save(input, delay) {
        if (typeof (input) !== 'undefined' || input !== null) {
            var name = $(input).attr("id");
            clearTimeout(reload_start);
            if ($(input).val() != "") {
                input_name[name] = $(input).val();
                $(input).parent().find("span").fadeIn();
            } else {
                $(input).parent().find("span").fadeOut();
            }
        }
        if (typeof (delay) === 'undefined')
        {
            delay = 3000;
        }

        reload_start = setTimeout(function () {
            reload();
        }, delay);
    }

    $(document).ready(function () {
<?php
$backups = $db->query("SELECT `comments` FROM `backup` WHERE `item` = '$nr' ORDER BY `id` DESC LIMIT 1");
$t_input_name = array();
foreach ($backups as $backup) {
    $comments = json_decode($backup["comments"]);
    foreach ($comments as $key => $val) {
        $t_input_name[$key] = $val;
    }
}

foreach ($t_input_name as $key => $val) {
    echo "\t\t" . 'input_name["' . $key . '"] = "' . $val . '";' . "\n";
    echo "\t\t" . '$("#' . $key . '").parent().find("span").fadeIn();' . "\n";

    //Atribute checkbox
    for ($i = 1; $i <= 7; $i++) {
        if ($key == "a" . $i . "i1" || $key == "a" . $i . "i2") {
            echo "\t\t" . '$(\'input:checkbox[value="' . $i . '"]\').prop("checked", true);' . "\n";
        }
    }
}
?>
        $.uniform.update();
        $("#cut_all_time").inputmask("99:99:99");
        $("#sotime").inputmask("99:99:99");
        $("#knob").knob();
        $("#knob").trigger('configure', {
            'release': function (v) {
                input_save("#remnant_factor");
            }
        });

        //Tweight units
        var tt = $("#tweight").val();
        $("#tweight").val(inputCreateUnits("tweight", tt));
        var dw = $("#d_weight").val();
        $("#d_weight").val(inputCreateUnits("#d_weight", dw));

        $("input").keyup(function () {
            if ($(this).attr("class") != "knob") {
                input_save(this);
            }
        });

        $("input").blur(function () {
            if ($(this).attr("class") != "knob" && $(this).attr("type") != "checkbox") {
                input_save(null, 1);
            }
        });

        $(":checkbox").change(function () { // Atribute change
            if (!$(this).is(':checked')) {
                var aid = $(this).attr("value");
                if (input_name["a" + aid + "i1"] > 0) {
                    delete input_name["a" + aid + "i1"];
                    $("#a" + aid + "i1").parent().find("span").fadeOut();
                }
                if (input_name["a" + aid + "i2"] > 0) {
                    delete input_name["a" + aid + "i2"];
                    $("#a" + aid + "i2").parent().find("span").fadeOut();
                }
                reload();
            } else {
                input_save(null, 1);
            }
        });

        $("form").submit(function (event) {
            event.preventDefault();
        });

        $(".resetInput").click(function () {
            blockSite();
            var iName = $(this).parent().parent().find("input").attr("id");
            delete input_name[iName];
            $(this).parent().fadeOut("fast", function () {
                $(this).css("display", "none");
            });
            reload();
        });

        $("#bSave").click(function () {
            blockSite();
            $("input").prop('readonly', true);
            reload();
            var json_inputs = JSON.stringify(input_name);
            var material = $("#materialSelect").val();
            $.ajax({
                url: "<?php echo $site_path; ?>/aengine/modules/mpc_edit.php?mpc=" + mpc + "&action=1",
                method: "POST",
                data: $("#ceForm").serialize() + "&json_inputs=" + json_inputs + "&material=" + material,
                success: function (msg) {
                    location.reload();
                }
            });
        });
        $("#cancel").on("click", function () { //Cancel button
            $("body").css("opacity", "0.5");
            if (confirm("Na pewno chcesz wyjść?")) {
                edit = false;
                $("body").fadeOut("fast", function () {
                    window.location.href = "<?php echo $site_path; ?>/project/4/<?php echo $pid; ?>/";
                });
            } else {
                $("body").css("opacity", "1");
            }
        });

        $("#discount_button").on("click", function () {
            var discount = $("#knob").val();
            $("#mat_discount").val(discount);
            input_save($("#mat_discount"), 1);
            $("#discount_modal").modal('hide');
        });

        //-------------------HISTORY
        $(".bHView").on("click", function () {
            blockSite();

            for (var key in input_name) {
                var ti = $("#" + key);
                $(ti).parent().find("span").fadeOut();
            }

            input_name = new Object();
            var _id = parseInt($(this).attr("id"));
            reload("&action=2&h_id=" + _id, true);
        });

        //--------------------Coments box 
        $("#fwcIcon").on("click", function () {
            if ($(this).hasClass("font-dark") == true) {
                $(this).removeClass("font-dark");
                $("#flyingWindowComments").stop().animate({
                    width: "300px",
                    height: "400px",
                    bottom: "20%"
                }, 500, function () {
                    $("#fwcContent").stop().fadeIn();
                    $("#fwcIcon i").removeClass("fa-comments").addClass("fa-remove");
                });
            } else {
                $(this).addClass("font-dark");
                $("#fwcContent").stop().fadeOut();
                $("#flyingWindowComments").stop().animate({
                    width: "50px",
                    height: "50px",
                    bottom: "30%"
                }, 500, function () {
                    $("#fwcIcon i").addClass("fa-comments").removeClass("fa-remove");
                });
            }
        });

        $("#addshout").submit(function (e) {
            e.preventDefault();
            var sval = $("#shoutbox").val();
            if (sval == "" || sval == null) {
                return;
            }
            $("#shoutbox").val("");
            $.ajax({
                url: "<?php echo $site_path; ?>/engine/addcomment.php?type=2&eid=<?php echo $nr; ?>&content=" + sval
            }).done(function () {
                refreshShout();
            });
        });

        $("#shoutrefreshb").on("click", function () {
            refreshShout();
        });
    });
</script>