<?php

class Input {

    public $type, $name, $dtype, $val;

    public function __construct($type, $name, $val = null, $dtype) {
        $this->type = $type;
        $this->name = $name;
        $this->val = $val;
        $this->dtype = $dtype;
    }

}

class Inputs {

    public $inputs = array();
    public $input = 0;
    public $list = 1;

    public function Add($type, $name, $dtype = null) {
        if ($dtype == null) {
            $dtype = PDO::PARAM_STR;
        }
        $val = @$GLOBALS[$name];

        switch ($type) {
            case $this->input: // Input
                array_push($this->inputs, new Input($type, $name, $val, $dtype));
                break;
            case $this->list:
                array_push($this->inputs, new Input($type, $name, $val, $dtype));
                break;
        }
        //echo $name . ': ' . $val . '<br/>';
    }

    public function BindInputs($query) {
        foreach ($this->inputs as $i) {
            $val = null;
            if (is_array($i->val) == true) {
                foreach ($i->val as $v) {
                    $val .= "x" . $v;
                }
            } else {
                $val = $i->val;
            }
            $query->bindValue(":" . $i->name, $val, $i->dtype);
        }
    }

}

//INIT
$_INPUTS = new Inputs();

function INPUT_INIT() {
    global $_INPUTS;
    $_INPUTS->Add($_INPUTS->input, "name");
    $_INPUTS->Add($_INPUTS->input, "remnant_calc");
    $_INPUTS->Add($_INPUTS->input, "clean_cut");
    $_INPUTS->Add($_INPUTS->input, "przeladunek");
    $_INPUTS->Add($_INPUTS->input, "cut_all");
    $_INPUTS->Add($_INPUTS->input, "cut_all_netto");
    $_INPUTS->Add($_INPUTS->input, "d_cut_all");
    $_INPUTS->Add($_INPUTS->input, "d_cut_all_netto");
    $_INPUTS->Add($_INPUTS->input, "rm_odpad");
    $_INPUTS->Add($_INPUTS->input, "tcost");
    $_INPUTS->Add($_INPUTS->input, "tps");
    $_INPUTS->Add($_INPUTS->input, "tpl");
    $_INPUTS->Add($_INPUTS->input, "waga_1m");
    $_INPUTS->Add($_INPUTS->input, "cena_mat_ciet");
    $_INPUTS->Add($_INPUTS->input, "cost_all_price");
    $_INPUTS->Add($_INPUTS->input, "rm_value");
    $_INPUTS->Add($_INPUTS->input, "cost_mat_kg");
    $_INPUTS->Add($_INPUTS->input, "waga_rm");
    $_INPUTS->Add($_INPUTS->input, "d_mat");
    $_INPUTS->Add($_INPUTS->input, "d_rmn");
    $_INPUTS->Add($_INPUTS->input, "d_weight");
    $_INPUTS->Add($_INPUTS->input, "mprice");
    $_INPUTS->Add($_INPUTS->input, "mwaste");
    $_INPUTS->Add($_INPUTS->input, "scut");
    $_INPUTS->Add($_INPUTS->input, "sotime");
    $_INPUTS->Add($_INPUTS->input, "socost");
    $_INPUTS->Add($_INPUTS->input, "sp_factor");
    $_INPUTS->Add($_INPUTS->input, "s_remnant_factor");
    $_INPUTS->Add($_INPUTS->input, "oq");
    $_INPUTS->Add($_INPUTS->input, "dlugosc_mat");
    $_INPUTS->Add($_INPUTS->input, "zajetosc_mat");
    $_INPUTS->Add($_INPUTS->input, "cut_all_time");
    $_INPUTS->Add($_INPUTS->input, "last_price_all_netto");
    $_INPUTS->Add($_INPUTS->input, "d_last_price_n");
    $_INPUTS->Add($_INPUTS->input, "d_qty");
    $_INPUTS->Add($_INPUTS->input, "wh");
    $_INPUTS->Add($_INPUTS->input, "diameter");
    $_INPUTS->Add($_INPUTS->input, "type");
    $_INPUTS->Add($_INPUTS->input, "mtype");
    $_INPUTS->Add($_INPUTS->input, "thickness");
    $_INPUTS->Add($_INPUTS->input, "mname");
    $_INPUTS->Add($_INPUTS->input, "c_mb");
    $_INPUTS->Add($_INPUTS->input, "mat_discount");
    $_INPUTS->Add($_INPUTS->input, "wasteMode");
    $_INPUTS->Add($_INPUTS->input, "paramI");
    $_INPUTS->Add($_INPUTS->input, "AreaWithoutHoles");
    $_INPUTS->Add($_INPUTS->input, "AreaWithHoles");
    $_INPUTS->Add($_INPUTS->input, "AreaWithoutHolesSHD");
}
