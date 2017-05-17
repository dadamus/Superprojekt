<?php

function getValue($name) {
    global $inputs;
    if (count($inputs) == 0) {
        return false;
    }

    foreach ($inputs as $key => $value) {
        if ($key == $name) {
            return floatval($value);
        } else if ($key == $name . "_brutto") {
            return floatval($value) / 1.25;
        }
    }

    return false;
}

function mpc_count() {
    global $db, $settings, $mtype, $edit;
    $qmaterial_data = $db->query("SELECT `price`, `waste`, `cubic` FROM `material` WHERE `name` = '$mtype'");
    $material_data = $qmaterial_data->fetch();

    if (@$edit == true) {
        if (getValue("c_mb") > 0) {
            $GLOBALS["c_mb"] = floatval($_POST["c_mb"]);
        }
        $material_data["waste"] = floatval($_POST["mwaste"]);

        if (getValue("cut_all_time") !== false) {
            $GLOBALS["cut_all_time"] = _timeToSec($_POST["cut_all_time"]);
        }
        if (getValue("dlugosc_mat") > 0) {
            $GLOBALS["dlugosc_mat"] = floatval($_POST["dlugosc_mat"]);
        }
        if (getValue("zajetosc_mat") > 0) {
            $GLOBALS["zajetosc_mat"] = floatval($_POST["zajetosc_mat"]);
        }
        if (getValue("mat_discount") !== false) {
            $GLOBALS["mat_discount"] = floatval($_POST["mat_discount"]);
        }
        if (getValue("sp_factor") > 0 || getValue("p_factor") > 0) {
            $v = getValue("sp_factor");
            if ($v == false) {
                $v = getValue("p_factor");
            }
            $settings["p_factor"] = floatval($v);
        }
        if (getValue("scut") > 0) {
            $settings["cut"] = floatval($_POST["scut"]);
        }
        if (getValue("sotime") !== false) {
            $settings["otime"] = _timeToSec($_POST["sotime"]);
        } else {
            $settings["otime"] = $GLOBALS["sotime"];
        }
        if (getValue("socost") > 0) {
            $settings["ocost"] = floatval($_POST["socost"]);
        } else {
            $settings["ocost"] = $GLOBALS["socost"];
        }
    } else {
        $GLOBALS["mat_discount"] = 0;
    }

    if (getValue("remnant_factor") > 0) {
        $settings["remnant_factor"] = getValue("remnant_factor");
    }

    //$GLOBALS["remnant_calc"] = ceil($material_data["price"] / $material_data["waste"]);
    $GLOBALS["remnant_calc"] = 0;

    if (getValue("d_clean_cut") > 0) {
        $GLOBALS["d_clean_cut"] = getValue("d_clean_cut");
        $GLOBALS["clean_cut"] = round($GLOBALS["d_clean_cut"] * $GLOBALS["d_qty"], 2);
    } else {
        if (getValue("clean_cut") > 0) {
            $GLOBALS["clean_cut"] = getValue("clean_cut");
        } else {
            $GLOBALS["clean_cut"] = round($GLOBALS["cut_all_time"] / 60 * $settings["cut"], 2);
        }

        $GLOBALS["d_clean_cut"] = round($GLOBALS["clean_cut"] / $GLOBALS["d_qty"], 2);
    }


    if (getValue("d_przeladunek") > 0) {
        $GLOBALS["przeladunek"] = getValue("d_przeladunek");
    } else {
        if (getValue("przeladunek") > 0) {
            $GLOBALS["przeladunek"] = getValue("przeladunek");
        } else {
            $GLOBALS["przeladunek"] = round($settings["otime"] / 60 * $settings["ocost"] * $GLOBALS["oq"], 2);
        }
        $GLOBALS["d_przeladunek"] = round($GLOBALS["przeladunek"] / $GLOBALS["d_qty"], 2);
    }

    if (getValue("d_cut_all") > 0) {
        $GLOBALS["d_cut_all"] = getValue("d_cut_all");
        $GLOBALS["cut_all"] = round($GLOBALS["cut_all"] * $GLOBALS["d_qty"], 2);
    } else {
        if (getValue("cut_all") > 0) {
            $GLOBALS["cut_all"] = getValue("cut_all");
        } else {
            $GLOBALS["cut_all"] = round($GLOBALS["clean_cut"] + $GLOBALS["przeladunek"], 2);
        }

        $GLOBALS["d_cut_all"] = round($GLOBALS["cut_all"] / $GLOBALS["d_qty"], 2);
    }

    if (getValue("d_cut_all_netto") > 0) {
        $GLOBALS["d_cut_all_netto"] = getValue("d_cut_all_netto");
        $GLOBALS["cut_all_netto"] = round($GLOBALS["d_cut_all_netto"] * $GLOBALS["d_qty"], 2);
    } else {
        if (getValue("cut_all_netto") > 0) {
            $GLOBALS["cut_all_netto"] = getValue("cut_all_netto");
        } else {
            $GLOBALS["cut_all_netto"] = round($GLOBALS["cut_all"] * $settings["p_factor"], 2);
        }

        $GLOBALS["d_cut_all_netto"] = round($GLOBALS["cut_all_netto"] / $GLOBALS["d_qty"], 2);
    }

    if (getValue("d_weight") > 0) {
        $GLOBALS["d_weight"] = getValue("d_weight");
        $GLOBALS["tweight"] = $GLOBALS["d_weight"] * $GLOBALS["d_qty"];
    } else {
        if (getValue("tweight") > 0) {
            $GLOBALS["tweight"] = getValue("tweight");
            $GLOBALS["d_weight"] = $GLOBALS["tweight"] / $GLOBALS["d_qty"];
        } else {
            $GLOBALS["d_weight"] = floatval($GLOBALS["d_weight"]);
            $GLOBALS["tweight"] = $GLOBALS["d_weight"] * $GLOBALS["d_qty"];
        }
    }

    $GLOBALS["cut_all_brutto"] = $GLOBALS["cut_all_netto"] * 1.23;
    $GLOBALS["d_cut_all_brutto"] = round($GLOBALS["cut_all_brutto"] / $GLOBALS["d_qty"], 2);

    //$GLOBALS["waga_1m"] = round($GLOBALS["tps"] / $GLOBALS["tpl"], 2); OLD VERSION
    //NEW DATA 04.11.2016
    $GLOBALS["waga_kawalka_xml"] = $GLOBALS["AreaWithoutHoles"] * $GLOBALS["thickness"] * $material_data["cubic"];
    $GLOBALS["waga_detalu_xml"] = $GLOBALS["AreaWithHoles"] * $GLOBALS["thickness"] * $material_data["cubic"];
    if ($GLOBALS["waga_detalu_xml"] > 0) {
    $GLOBALS["waga_1m"] = $GLOBALS["waga_kawalka_xml"] / $GLOBALS["waga_detalu_xml"];
    } else {
        $GLOBALS["waga_1m"] = 0;
    }
    $GLOBALS["roznica_pow_xml"] = $GLOBALS["AreaWithoutHolesSHD"] - $GLOBALS["AreaWithHoles"];
    $GLOBALS["waga_odp_xml"] = $GLOBALS["roznica_pow_xml"] * $GLOBALS["thickness"] * $material_data["cubic"];
    $GLOBALS["waga_odp_all_xml"] = $GLOBALS["waga_odp_xml"] * $GLOBALS["d_qty"];

    if (getValue("rm_odpad") > 0) {
        $GLOBALS["rm_odpad"] = getValue("rm_odpad");
    } else {
        $GLOBALS["rm_odpad"] = round($GLOBALS["dlugosc_mat"] - $GLOBALS["zajetosc_mat"], 2);
    }
    if (getValue("tcost") > 0) {
        $GLOBALS["tcost"] = getValue("tcost");
    } else {
        $GLOBALS["tcost"] = round($GLOBALS["c_mb"] / 1000 * $GLOBALS["dlugosc_mat"], 2);
    }
    $GLOBALS["cena_mat_ciet"] = round($GLOBALS["c_mb"] / 1000 * $GLOBALS["zajetosc_mat"], 2);
    if ($GLOBALS["waga_1m"] > 0) {
    $GLOBALS["cost_mat_kg"] = round($GLOBALS["c_mb"] / $GLOBALS["waga_1m"], 2);
    } else {
        $GLOBALS["cost_mat_kg"] = 0;
    }

    if (getValue("waga_rm") > 0) {
        $GLOBALS["waga_rm"] = getValue("waga_rm");
    } else {
        if ($GLOBALS["wasteMode"] == 0) { //Default
            $add = 0;
            if ($GLOBALS["paramI"] == 1) {
                $add = $GLOBALS["waga_odp_all_xml"];
            }
            $GLOBALS["waga_rm"] = round($GLOBALS["rm_odpad"] * $GLOBALS["waga_1m"] + $add, 2);
        } else {
            $GLOBALS["waga_prof_xml"] = $GLOBALS["waga_1m"] / 1000 * $GLOBALS["zajetosc_mat"];
            $GLOBALS["waga_detali_xml"] = $GLOBALS["waga_detalu_xml"] * $GLOBALS["d_qty"] / 1000;
            $GLOBALS["waga_rm"] = round($GLOBALS["waga_prof_xml"] - $GLOBALS["waga_detali_xml"], 2);
        }
    }

    if (getValue("rm_value") > 0) {
        $GLOBALS["rm_value"] = getValue("rm_value");
        $GLOBALS["d_rmn"] = $GLOBALS["rm_value"] / $GLOBALS["d_qty"];
    } else {
        if (getValue("d_rmn") > 0) {
            $GLOBALS["d_rmn"] = getValue("d_rmn");
            $GLOBALS["rm_value"] = $GLOBALS["d_rmn"] * $GLOBALS["d_qty"];
        } else {
            $GLOBALS["rm_value"] = round($GLOBALS["waga_rm"] * $material_data["waste"] * $settings["remnant_factor"], 2);
            $GLOBALS["d_rmn"] = round($GLOBALS["rm_value"] / $GLOBALS["d_qty"], 2);
        }
    }

    if (getValue("cost_all_price") > 0) {
        $GLOBALS["cost_all_price"] = getValue("cost_all_price");
        $GLOBALS["d_mat"] = round($GLOBALS["cost_all_price"] / $GLOBALS["d_qty"], 2);
    } else {
        if (getValue("d_mat") > 0) {
            $GLOBALS["d_mat"] = getValue("d_mat");
            $GLOBALS["cost_all_price"] = round($GLOBALS["d_mat"] * $GLOBALS["d_qty"], 2);
        } else {
            $GLOBALS["cost_all_price"] = round(($GLOBALS["tcost"] - $GLOBALS["rm_value"]) + (($GLOBALS["tcost"] - $GLOBALS["rm_value"]) * $GLOBALS["mat_discount"] / 100), 2);
            //die($GLOBALS["cost_all_price"] . " | ".($GLOBALS["tcost"] - $GLOBALS["rm_value"]). " | ".(($GLOBALS["tcost"] - $GLOBALS["rm_value"]) * $GLOBALS["mat_discount"] / 100). " | ".$GLOBALS["mat_discount"]);
            $GLOBALS["d_mat"] = round($GLOBALS["cost_all_price"] / $GLOBALS["d_qty"], 2);
        }
    }

    $GLOBALS["d_rmn"] = round($GLOBALS["rm_value"] / $GLOBALS["d_qty"], 2);
    $GLOBALS["last_price_all_netto"] = 0;
    //-----------------------Atribute checkbox
    if (@$edit == true) {
        for ($i = 1; $i <= 6; $i++) {
            if (getValue("a" . $i . "i1") > 0) {
                $GLOBALS["a" . $i . "i2"] = round(getValue("a" . $i . "i1") * $GLOBALS["d_qty"], 2);
                $GLOBALS["a" . $i . "i1"] = round(getValue("a" . $i . "i1"), 2);
            } else if (getValue("a" . $i . "i2") > 0) {
                $GLOBALS["a" . $i . "i2"] = round(getValue("a" . $i . "i2"), 2);
                $GLOBALS["a" . $i . "i1"] = round(getValue("a" . $i . "i2") / $GLOBALS["d_qty"], 2);
            } else {
                continue;
            }
            if (!empty($_POST["atribute"])) {
                foreach ($_POST["atribute"] as $atribute) {
                    if ($atribute == $i) {
                        $GLOBALS["last_price_all_netto"] += $GLOBALS["a" . $i . "i2"];
                    }
                }
            }
        }
    }

    if (getValue("last_price_all_netto") > 0) {
        $GLOBALS["last_price_all_netto"] += getValue("last_price_all_netto");
    } else {
        $GLOBALS["last_price_all_netto"] += round($GLOBALS["cut_all_netto"] + $GLOBALS["cost_all_price"], 2);
    }


    if (getValue("d_last_price_n_brutto") > 0) {
        $GLOBALS["d_last_price_n"] = round(getValue("d_last_price_n_brutto") / 1.23, 2);
        $GLOBALS["d_last_price_n_brutto"] = getValue("d_last_price_n_brutto");
    } else {

        if (getValue("d_last_price_n") > 0) {
            $GLOBALS["d_last_price_n"] = getValue("d_last_price_n");
            $GLOBALS["d_last_price_n_brutto"] = round($GLOBALS["d_last_price_n"] * 1.23, 2);
        } else {
            $GLOBALS["d_last_price_n"] = round($GLOBALS["last_price_all_netto"] / $GLOBALS["d_qty"], 2);
            $GLOBALS["d_last_price_n_brutto"] = round($GLOBALS["d_last_price_n"] * 1.23, 2);
        }
    }

    $GLOBALS["mprice"] = 0;
    $GLOBALS["mwaste"] = $material_data["waste"];
    $GLOBALS["scut"] = $settings["cut"];
    $GLOBALS["sotime"] = $settings["otime"];
    $GLOBALS["socost"] = $settings["ocost"];
    $GLOBALS["sp_factor"] = $settings["p_factor"];
    $GLOBALS["s_remnant_factor"] = $settings["remnant_factor"];
}
