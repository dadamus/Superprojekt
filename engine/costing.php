<?php
//VERSION 1.0
require_once dirname(__FILE__) . '/class/material.php';
$material = new Material();

class Costing
{

    private $_name = array(1 => "Blachy", 2 => "Profile"); // STRING Name
    private $_url = array(1 => "plate", 2 => "profile"); // URL
    public $type;
    public $name;
    public $url;

    public function __construct($int)
    { // GET TYPE
        $this->type = $int;
        $this->name = $this->_name[$int];
        $this->url = $this->_url[$int];
    }

}

$costing = new Costing($ct);
$_SESSION["costingID"] = $costing->type;
?>
<div class="row">
    <div class="col-lg-12">
        <h2 class="page-title">Costing - <?php echo $costing->name; ?></h2>
    </div>
</div>
<div id="cClients">
    <div class="row">
        <div class="col-lg-8" style="margin-left: 1%;">
            <div class="portlet">
                <div class="portlet-title"><i class="icon-search"></i>
                    <h3>Wyszukaj firmę</h3>
                </div>
                <div class="portlet-body">
                    <form id="sform">
                        <input type="search" class="form-control" placeholder="ID lub nazwa" id="search-input"
                               name="scontent">
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="portlet box">
                <div class="portlet-body flip-scroll">
                    <table class="table table-bordered table-striped table-condensed flip-content">
                        <thead>
                        <tr>
                            <td>ID</td>
                            <td>Nazwa</td>
                            <td>Typ</td>
                        </tr>
                        </thead>
                        <tbody id="clist">
                        <tr>
                            <td>Brak wyników</td>
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
<div id="cProjects" style="display: none;">
    <div class="row">
        <div class="col-lg-12">
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <span class="caption-subject font-dark bold uppercase">Projekty</span>
                    </div>
                    <div class="actions">
                        <a href="#" id="aClientsList" data-toggle="modal" class="btn btn-info"><i
                                    class="fa fa-mail-reply"></i> Lista klientów</a>
                    </div>
                </div>
                <div class="portlet-body" id="pcontent"></div>
            </div>
        </div>
    </div>
</div>
<div class="row" id="cDetails" style="display: none;">
    <div class="portlet">
        <div class="portlet-title">
            <div class="caption">
                <div style="float: left">Detele</div>
            </div>
            <div class="actions">
                <div class="tree" style="float: right; margin: 8px; padding-right: 40px;"></div>
            </div>
        </div>
        <div class="portlet-body">
            <div class="row">
                <div class="col-lg-2" style="text-align: center;">
                    <div class="portlet box blue-hoki">
                        <div class="portlet-title">
                            <div class="caption">Menu</div>
                        </div>
                        <div class="portlet-body">
                            <p><a href="#" id="aProjectsList" data-toggle="modal" class="btn btn-info"><i
                                            class="fa fa-mail-reply"></i> Lista projektow</a></p>
                            <a href="#" id="addToCosting" data-backdrop="false" data-target="#addToCostingModal"
                               data-toggle="modal" class="btn btn-info">Auto wycena</a>
                            <?php if ($costing->type != 2): ?>
                                <a href="#" style="margin-top: 10px;" id="addToMultiCosting" data-backdrop="false"
                                   data-target="#addToMultiCostingModal" data-toggle="modal" class="btn btn-success">MultiPart</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-10">
                    <div class="portlet">
                        <form action="#">
                            <div class="portlet-body" id="dcontent"></div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="portlet">
                        <div class="portlet-body">
                            <div class="widget">
                                <div id="accordion1" class="panel-group">
                                    <div class="panel">
                                        <div class="panel-heading">
                                            <a href="#collapseOneTwo" data-toggle="collapse"
                                               class="accordion-toggle collapsed">Opcje statusów</a>
                                        </div>
                                        <div class="panel-collapse collapse" id="collapseOneTwo" style="height: 0px;">
                                            <div class="panel-body">
                                                <table class="table">
                                                    <thead>
                                                    <tr>
                                                        <?php
                                                        $style = 'style="width: 25%; text-align: center;"';
                                                        for ($i = 0; $i < count($STATUS_ALLOWED); $i++) {
                                                            echo '<td ' . $style . '><a href = "javascript:;" id="' . $STATUS_ALLOWED[$i] . '_sid"><i class = "' . $STATUS_ICONS[$STATUS_ALLOWED[$i]] . '"></i></a></td>';
                                                        }
                                                        ?>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr>
                                                        <?php
                                                        for ($i = 0; $i < count($STATUS_ALLOWED); $i++) {
                                                            echo '<td ' . $style . '><a class="btn btn-small btn-success sa" href="javascript:;" id="' . $STATUS_ALLOWED[$i] . '_asid"><i class="fa fa-check"></i></a></td>';
                                                        }
                                                        ?>
                                                    </tr>
                                                    <tr>
                                                        <?php
                                                        for ($i = 0; $i < count($STATUS_ALLOWED); $i++) {
                                                            echo '<td ' . $style . '><a class="btn btn-danger btn-small sd" href="javascript:;" id="' . $STATUS_ALLOWED[$i] . '_rsid"><i class="fa fa-remove"></i></a></td>';
                                                        }
                                                        ?>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="panel">
                                        <div class="panel-heading" style="border: yellowgreen solid 1px;">
                                            <a href="#collapseTwo" data-toggle="collapse"
                                               class="accordion-toggle collapsed">Kolejka do wyceny</a>
                                        </div>
                                        <div class="panel-collapse collapse" id="collapseTwo">
                                            <div class="panel-body">
                                                <table class="display" id="CTD_TABLE"
                                                       style="overflow-y: auto; display:block;">
                                                    <thead>
                                                    <tr>
                                                        <th>Nr</th>
                                                        <th>Nazwa</th>
                                                        <th>Kod</th>
                                                        <th>Blacha</th>
                                                        <th>Sztuk</th>
                                                        <th>Atrybut</th>
                                                        <th></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody id="CTD_CONTENT">

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="panel">
                                        <div class="panel-heading" style="border: orange solid 1px;">
                                            <a href="#collapseThree" data-toggle="collapse"
                                               class="accordion-toggle collapsed">Wycenione</a>
                                        </div>
                                        <div class="panel-collapse collapse" id="collapseThree">
                                            <div class="panel-body">
                                                <table class="display" id="PTD_TABLE" style="overflow-y: auto;">
                                                    <thead>
                                                    <tr>
                                                        <th style="width: 120px;"><a class="btn btn-primary"
                                                                                     data-toggle="modal" id="BATO"
                                                                                     href="#addToOrder"> Dodaj
                                                                zamówienie</a>
                                                        </td>
                                                        <th>Nr</th>
                                                        <th>Nazwa</th>
                                                        <th>Kod</th>
                                                        <th>Blacha</th>
                                                        <th>Sztuk</th>
                                                        <th>Atrybut</th>
                                                        <th>Cena</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody id="PTD_CONTENT">

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="panel">
                                        <div class="panel-heading" style="border: silver solid 1px;">
                                            <a href="#collapseFour" data-toggle="collapse"
                                               class="accordion-toggle collapsed">Historia</a>
                                        </div>
                                        <div class="panel-collapse collapse" id="collapseFour">
                                            <div class="panel-body">
                                                <table class="display" style="overflow-y: auto;" id="HT_TABLE">
                                                    <thead>
                                                    <tr>
                                                        <th>Nr</th>
                                                        <th>Nazwa</th>
                                                        <th>Kod</th>
                                                        <th>Blacha</th>
                                                        <th>Sztuk</th>
                                                        <th>Atrybut</th>
                                                        <th>Cena</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody id="HT_CONTENT">

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div style="display: none;" aria-hidden="true" aria-labelledby="myModalLabel2" role="dialog"
                                 tabindex="-1" class="modal fade modal-scroll modal-overflow"
                                 id="addToMultiCostingModal">
                                <div class="modal-content">
                                    <form action="?" id="multiGetNumberForm" method="POST">
                                        <div class="modal-header">
                                            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">
                                                x
                                            </button>
                                            <h4>Multipart</h4>
                                        </div>
                                        <div class="modal-body" id="multi-wrapper">
                                            <div style="text-align: center"><p>Wybierz detale...</p></div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div style="display: none;" aria-hidden="true" aria-labelledby="myModalLabel3" role="dialog"
                                 tabindex="-1" class="modal fade modal-scroll" id="createMWPMultipartModal">
                                <div class="modal-content">
                                    <form action="?" id="multiMPWCreate" method="POST">
                                        <div class="modal-header">
                                            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">
                                                x
                                            </button>
                                            <h4>Multipart</h4>
                                        </div>
                                        <div class="modal-body" id="multi-mpw-wrapper">

                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div style="display: none;" aria-hidden="true" aria-labelledby="myModalLabel" role="dialog"
                                 tabindex="-1" class="modal fade modal-scroll modal-overflow" id="addToCostingModal">
                                <div class="modal-content">
                                    <form action="?" id="ATCFORM" method="POST">
                                        <div class="modal-header">
                                            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">
                                                x
                                            </button>
                                            <h4 id="myModalLabel" class="modal-title">Wycena</h4>
                                        </div>
                                        <div class="modal-body" id="costing-wrapper">
                                            <div style="text-align: center"><p>Wybierz detale...</p></div>
                                        </div>
                                        <div class="modal-footer">
                                            <button data-dismiss="modal" class="btn btn-default" type="button">Zamknij
                                            </button>
                                            <button class="btn btn-primary" type="submit">Zapisz</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="portlet">
                        <div class="portlet-title">
                            <div class="caption">
                                Zamówienia
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="portlet box blue-hoki">
                                        <div class="portlet-title">
                                            <div class="caption">
                                                <i class="fa fa-shopping-cart"></i> Lista
                                            </div>
                                            <div class="actions">
                                                <!--<a href="javascript:;" data-target="#mAddOrder" data-toggle="modal" class="btn btn-default btn-sm">
                                                    <i class="fa fa-plus"></i> Nowe
                                                </a>-->
                                            </div>
                                        </div>
                                        <div class="portlet-body">
                                            <table class="display" id="OTD_TABLE"
                                                   style="overflow-y: auto; text-align: center; width: 100%;">
                                                <thead>
                                                <tr>
                                                    <td>Nr</td>
                                                    <td>Nazwa</td>
                                                    <td>Detali</td>
                                                    <td>Suma</td>
                                                    <td>Status</td>
                                                    <td>Data utworzenia</td>
                                                    <td></td>
                                                </tr>
                                                </thead>
                                                <tbody id="OTD_CONTENT">

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="mAddOrder" class="modal fade" tabindex="-1" data-focus-on="input:first" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4 class="modal-title">Nowe zamówienie</h4>
    </div>
    <div class="modal-body">
        <div class="row">
            <form id="fAddOrder" action="#" class="form-horizontal form-bordered">
                <div class="form-body">
                    <div class="form-group">
                        <label class="control-label col-md-3">Data</label>
                        <div class="col-md-8">
                            <input class="form-control date-picker" name="odate" size="16" type="text"
                                   value="<?php echo date("Y-m-d"); ?>" data-date-format="yyyy-mm-dd"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Opis</label>
                        <div class="col-md-8">
                            <textarea class="form-control" name="odes"></textarea>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" data-dismiss="modal" class="btn btn-outline dark">Zamknij</button>
        <button type="button" class="btn green" id="fAddOrderB">Dodaj</button>
    </div>
</div>
<div id="addToOrder" class="modal fade" tabindex="-1" data-focus-on="input:first" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4 class="modal-title">Dodaj do zamówienia</h4>
    </div>
    <div class="modal-body" id="toc">
        Najpierw wybierz detale!
    </div>
    <div class="modal-footer">
        <button type="button" data-dismiss="modal" class="btn btn-outline dark">Zamknij</button>
    </div>
</div>

<div id="orderInfo" class="modal container modal-scroll modal-overflow fade" tabindex="-1" data-focus-on="input:first"
     aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4 class="modal-title">Zamówienie</h4>
    </div>
    <div class="row">
        <div class="col-lg-10"></div>
        <div class="col-lg-2">
            <a class="btn btn-info bAddToProduction" href="javascript:;">Do produkcji</a>
        </div>
    </div>
    <div class="modal-body" id="orderInfoContent">
        <table class="display" id="OTDM_TABLE" style="overflow-y: auto;">
            <thead>
            <tr>
                <th>Nr</th>
                <th>Nazwa</th>
                <th>Kod</th>
                <th>Blacha</th>
                <th>Sztuk</th>
                <th>Atrybut</th>
                <th>Cena</th>
                <th></th>
            </tr>
            </thead>
            <tbody id="OTDM_CONTENT">

            </tbody>
        </table>
    </div>
    <div class="modal-footer">
        <button type="button" data-dismiss="modal" class="btn btn-outline dark">Zamknij</button>
    </div>
</div>

<script type="text/javascript">
    var selected = new Array();
    var costEdit = false;
    var oid_view;
    var ajax_gif = '<div style="text-align: center"><img src="<?php echo $site_path; ?>/img/lg.GIF" alt="Loading"/></div>';
    var server_name = "<?php echo $_SERVER["SERVER_NAME"]; ?>";
    var ctype = <?php echo $costing->type; ?>;

    function search() {
        if ($("#sform input").val() !== "") {
            $.ajax({
                method: "POST",
                url: "<?php echo $site_path; ?>/engine/projectbase.php?a=1",
                data: $("#sform").serialize()
            }).done(function (msg) {
                $("#clist").html(msg);
            });
        }
    }

    function pList(_id) {
        var _name = _id.split("_")[1];
        $("#cClients").fadeOut("fast");
        $(".tree").html(_name + "/");
        $.ajax({
            url: "<?php echo $site_path; ?>/engine/projectbase.php?a=2&name=" + _id
        }).done(function (msg) {
            Cookies.set("plClientName", _id);
            $("#pcontent").html('<table cellpadding="0" cellspacing="0" border="0" id="plist" class="display" style="width: 100%;"><thead><tr><td>Nr</td><td>Data</td><td>Nazwa</td><td>Zawartość</td></tr></thead><tbody id="plistc"></tbody></table>');
            $("#plistc").html(msg);
            table = $('#plist').dataTable({
                "sPaginationType": "full_numbers"
            });
        });
    }

    var first = true;

    function dList(id) {
        var _id = parseInt(id);
        var _name = id;
        _name = _name.split("_")[1];
        //$("input:checkbox").attr('checked', false);
        var cname = Cookies.get("plClientName").split("_")[1];
        $(".tree").html(cname + "/" + _name + "/");
        Cookies.set("plProjectName", id);
        Cookies.set("plProjectId", _id);
        if (first == false) {
            $('#dlist').DataTable().destroy();
            $("#CTD_TABLE").DataTable().destroy();
            $("#PTD_TABLE").DataTable().destroy();
            $("#OTD_TABLE").DataTable().destroy();
            $("#PT_TABLE").DataTable().destroy();
            $("#HT_TABLE").DataTable().destroy();
        }

        $("#cProjects").stop().fadeOut("fast", function () {
            $("#cDetails").fadeIn();
            $.ajax({
                url: "<?php echo $site_path; ?>/engine/details.php?t=<?php echo $costing->type; ?>&a=1" //Typ profilu
            }).done(function (msg) {
                $("#dcontent").html('<table cellpadding="0" cellspacing="0" border="0" id="dlist" class="display"><thead><tr><td style="width: 20px;"><input type="checkbox" id="select-all" style="width: 20px; height: 20px;" value="-1"/></td><td>ID</td><td>Firma</td><td>Projekt</td><td>Nazwa</td><td>Status</td><td>Wycena</td><td></td></tr></thead><tbody id="dlistc"></tbody></table>');
                $("#dlistc").html(msg);
                table = $('#dlist').dataTable({
                    "sPaginationType": "full_numbers"
                });
                //Select selected
                if (selected.length > 0) {
                    var s;
                    for (s in selected) {
                        $('input[name="selected[]"][value="' + selected[s] + '"]').prop('checked', true);
                    }
                }
            });
        });
        //COSTING LOAD
        $.ajax({
            url: "<?php echo $site_path; ?>/engine/costing/listmanager.php?id=3&ctype=" + ctype
        }).done(function (msg) {
            $("#CTD_CONTENT").html(msg);
            $("#CTD_TABLE").dataTable({
                "sPaginationType": "full_numbers"
            });
        });
        //Priced load
        $.ajax({
            url: "<?php echo $site_path; ?>/engine/costing/listmanager.php?id=7&ctype=" + ctype
        }).done(function (msg) {
            $("#PTD_CONTENT").html(msg);
            $("#PTD_TABLE").dataTable({
                "sPaginationType": "full_numbers"
            });
        });
        //History load
        $.ajax({
            url: "<?php echo $site_path; ?>/engine/costing/listmanager.php?id=8&ctype=" + ctype
        }).done(function (msg) {
            $("#HT_CONTENT").html(msg);
            $("#HT_TABLE").dataTable({
                "sPaginationType": "full_numbers"
            });
        });

        $.ajax({
            url: "<?php echo $site_path; ?>/engine/costing/order.php?id=1"
        }).done(function (msg) {
            $("#OTD_CONTENT").html(msg);
            $("#OTD_TABLE").dataTable({
                "sPaginationType": "full_numbers"
            });
        });
        var site = 3;
        if (ctype == 2) { // if profile
            site = 4;
        }
        window.history.replaceState(null, null, "<?php echo $site_path; ?>/project/" + site + "/" + _id + "/");

        if (first == true) {
            first = false;
        }
    }

    //Add to costing list
    function addToCosting() {
        if (selected.length > 0 || costEdit == true) {
            var clMaterial = new Array();
            <?php
            for ($i = 1; $i <= count($material->name); $i++) {
                if (!isset($material->name[$i])) {
                    continue;
                }
                echo 'clMaterial[' . $i . '] = "' . $material->name[$i] . '";';
            }
            ?>
            var actionID = "2";
            if (costEdit == true) {
                actionID = "4";
            }
            $("#costing-wrapper").html(ajax_gif);
            $.ajax({
                method: "POST",
                data: "did=" + selected,
                url: "<?php echo $site_path; ?>/engine/costing/listmanager.php?id=" + actionID + "&t=<?php echo $costing->type; ?>"
            }).done(function (Data) {
                var text = '<div style="text-align: center"><p>Wybranych detali: ' + selected.length;
                text = text + '</p><p><table style="margin: 0 auto; border-spacing: 2px; border-collapse: separate;"><tbody>';
                text = text + '<tr><td>Blacha</td><td><select class="form-control" name="material" id="cmaterial"></select></td></tr>';
                text = text + '<tr><td>Grubość</td><td><input type="text" class="form-control" name="thickness" id="cthickness"/></td></tr>';
                text = text + '<tr><td>Sztuk</td><td><input type="number" class="form-control" name="pieces" id="cpieces"/></td></tr>';
                text = text + '<tr><td>Wersja</td><td><select class="form-control" name="version" id="pversioni"></select></td></tr>';
                <?php
                if ($costing->type == 2) {
                    echo 'text = text + \'<tr><td>Promień</td><td><select class="form-control" name="radius" id="radiuslist"></select></td></tr>\';';
                }
                ?>
                text = text + '<tr><td>B</td><td><input type="checkbox" name="cba[]" value="1" id="c1" class="form-control"></td></tr>\n\
<tr><td>P</td><td><input type="checkbox" name="cba[]" value="4" class="form-control" id="c4"></td></tr>\n\
<tr><td>Z</td><td><input type="checkbox" name="cba[]" value="5" class="form-control" id="c5"></td></tr>\n\
<tr><td>R</td><td><input type="checkbox" name="cba[]" value="6" class="form-control" id="c6"></td></tr>\n\
<tr><td>W</td><td><input type="checkbox" name="cba[]" value="3" class="form-control" id="c3"></td></tr>\n\
<tr><td>Opis</td><td><textarea class="form-control" name="des" id="cdes"></textarea></td></tr></tbody></table></p></div>';
                $("#costing-wrapper").html(text);
                var data;
                if (Data !== "") {
                    data = jQuery.parseJSON(Data);
                    var version = jQuery.parseJSON(data.version);
                    for (key in clMaterial) {
                        var ads = "";
                        if (parseInt(data.dmaterial) == key) {
                            ads = "selected=\"selected\"";
                        }
                        $("#cmaterial").append("<option value=\"" + key + "\" " + ads + ">" + clMaterial[key] + "</option>");
                    }
                    for (key in version) {
                        var ads = "";
                        if (parseInt(data.dversion) == parseInt(version[key])) {
                            ads = "selected=\"selected\"";
                        }
                        $("#pversioni").append("<option " + ads + ">" + version[key] + "</option>");
                    }
                    if (data.radius !== "") {
                        var radius = jQuery.parseJSON(data.radius);
                        for (key in radius) {
                            var ads = "";
                            if (parseFloat(data.dradius) == parseFloat(radius[key])) {
                                ads = "selected=\"selected\"";
                            }
                            $("#radiuslist").append("<option " + ads + ">" + radius[key] + "</option>");
                        }
                    }
                    if (costEdit !== undefined) {
                        $("#cthickness").val(data.dthickness);
                        $("#cpieces").val(data.dpieces);
                        $("#cdes").val(data.ddes);

                        var datribute = jQuery.parseJSON(data.datribute);
                        for (key in datribute) {
                            $("#c" + datribute[key]).prop('checked', true);
                        }
                    }
                }
            });
        } else {
            $("#costing-wrapper").html('<div style="text-align: center"><p>Wybierz detale...</p></div>');
        }
    }

    function orderView(_oid) {
        App.blockUI({boxed: !0});
        if ($.fn.DataTable.isDataTable("#OTDM_TABLE")) {
            $("#OTDM_TABLE").DataTable().destroy();
        }
        $.ajax({
            url: "<?php echo $site_path; ?>/engine/costing/order.php?id=6&oid=" + _oid
        }).done(function (msg) {
            if (msg != "2") {
                $("#OTDM_CONTENT").html(msg);
                $("#OTDM_TABLE").dataTable({
                    "sPaginationType": "full_numbers"
                });
                $("#orderInfo").stop().modal("show");
                App.unblockUI();
            } else {
                window.location.href = "<?php echo $site_path; ?>/order/" + _oid + "/";
            }
        });
    }

    $(document).ready(function () {
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "positionClass": "toast-bottom-right",
            "onclick": null,
            "showDuration": "1000",
            "hideDuration": "1000",
            "timeOut": "10000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }
        <?php
        if (@$_GET["plist"] != null) {
            echo 'pList(Cookies.get("plClientName"));dList(Cookies.get("plProjectName"));';
        }
        ?>
        $("#clist").on("click", "tr", function () {
            var _id = $(this).attr("id");
            Cookies.set("cname", $(this).children("._fname").html());
            $("#cClients").fadeOut("fast", function () {
                $("#cProjects").fadeIn();
            });
            pList(_id);
        });
        $("#pcontent").on("click", "#plist tbody tr", function () {
            Cookies.set("pname", $(this).children("._pname").html());
            var _id = $(this).attr("id");
            dList(_id);
        });
        $("#dcontent").on("click", "#dlist .ca", function () {
            var _id = parseInt($(this).parent().attr("id"));
            Cookies.set("dname", $(this).parent().children("._dname").html());
            Cookies.set("plDetailId", _id);
            window.location.href = "<?php echo $site_path; ?>/costing/<?php echo $costing->url; ?>/" + _id + "/";
        });
        //STATUS SET
        $("#dcontent").on("click", 'input[name="selected[]"]', function () {
            var _id = parseInt($(this).val());
            if ($(this).is(':checked') && selected.indexOf(_id) < 0 && parseInt(_id) >= 0) {
                selected.push(_id);
            }
            if (!$(this).is(':checked') && selected.indexOf(_id) >= 0) {
                var index = selected.indexOf(_id);
                selected.splice(index, 1);
            }
        });
        //ADD STATUS
        $(".panel-body").on("click", ".sa", function () {
            var _id = parseInt($(this).attr("id"));
            if (selected.length > 0) {
                $.ajax({
                    method: "POST",
                    url: "<?php echo $site_path; ?>/engine/status.php?sa=1",
                    data: {selected: selected, status: _id}
                }).done(function () {
                    $("#" + _id + "_sid").addClass("btn btn-small btn-success");
                    dList(Cookies.get("plProjectId"));
                });
            } else {
                alert("Zaznacz detale do zmiany!");
            }
        });
        //DELETE STATUS
        $(".panel-body").on("click", ".sd", function () {
            var _id = parseInt($(this).attr("id"));
            if (selected.length > 0) {
                $.ajax({
                    method: "POST",
                    url: "<?php echo $site_path; ?>/engine/status.php?sa=2",
                    data: {selected: selected, status: _id}
                }).done(function () {
                    $("#" + _id + "_sid").addClass("btn btn-danger btn-small sd");
                    dList(Cookies.get("plProjectId"));
                });
            } else {
                alert("Zaznacz detale do zmiany!");
            }
        });
        //SELECT ALL CHECKBOX
        $("#dcontent").on("click", "#select-all", function () {
            if (this.checked) {
                // Iterate each checkbox
                $('input[name="selected[]"]').each(function () {
                    if (!$(this).is(':checked') && parseInt($(this).val()) >= 0 && selected.indexOf($(this).val()) < 0) {
                        selected.push(parseInt($(this).val()));
                    }
                    this.checked = true;
                });
            } else {
                $('input[name="selected[]"]').each(function () {
                    var index = selected.indexOf(parseInt($(this).val()));
                    selected.splice(index, 1);
                    this.checked = false;
                });
            }
        });
        //Add to costing button
        $("#cDetails").on("click", "#addToCosting", function () {
            costEdit = false;
            addToCosting();
        });
        $("#search-input").keyup(function () {
            search();
        });
        $("#aClientsList").on("click", function () {
            $("#cProjects").fadeOut("fast", function () {
                $("#cClients").fadeIn();
            });
        });
        $("#aProjectsList").on("click", function () {
            $("#cDetails").fadeOut("fast", function () {
                $("#cProjects").fadeIn();
            });
        });
        $("#aDatailList").on("click", function () {
            $("#cValuation").fadeOut("fast", function () {
                $("#cDetails").fadeIn();
            });
        });
        $("#sform").submit(function (event) {
            search();
            event.preventDefault();
        });
        //AUTO COSTING EDIT BUTTON
        $("#CTD_CONTENT").on("click", ".bEditC", function () {
            var _d = $(this).parent().parent().parent().parent().parent();
            var did = parseInt($(_d).children(".did").attr("id"));
            var Cid = parseInt($(_d).children(".Cid").html());
            costEdit = true;
            Cookies.set("cfeDID", did);
            Cookies.set("cfeCID", Cid);

            addToCosting();
        });
        $("#CTD_CONTENT").on("click", ".bDeleteC", function () {
            var _d = $(this).parent().parent().parent().parent().parent();
            var did = parseInt($(_d).children(".did").attr("id"));
            var Cid = parseInt($(_d).children(".Cid").html());
            Cookies.set("cfeDID", did);
            Cookies.set("cfeCID", Cid);

            $.ajax({
                url: "<?php echo $site_path; ?>/engine/costing/listmanager.php?id=6"
            }).done(function () {
                location.reload();
            });
        });
        $("#mAddOrder").on("click", "#fAddOrderB", function () {
            $("#fAddOrder").submit();
        });
        $('form[id="fAddOrder"]').on("submit", function (event) {
            var serialize = $('form[id="fAddOrder"]').serialize();
            $.ajax({
                method: "POST",
                data: serialize,
                url: "<?php echo $site_path; ?>/engine/costing/order.php?id=2"
            }).done(function (msg) {
                if (msg == "1") {
                    $("#OTD_TABLE").DataTable().destroy();
                    $.ajax({
                        url: "<?php echo $site_path; ?>/engine/costing/order.php?id=1"
                    }).done(function (msg) {
                        $("#OTD_CONTENT").html(msg);
                        $("#OTD_TABLE").dataTable({
                            "sPaginationType": "full_numbers"
                        });
                    });
                } else {
                    alert("Wystąpił błąd!");
                }
            });
            event.preventDefault();
        });
        //AUTO COSTING FORM
        $('form[id="ATCFORM"]').on("submit", function (event) {
            var action = 1;
            if (costEdit == true) {
                action = 5;
            }
            var serialize = $('form[id="ATCFORM"]').serialize();
            $("#costing-wrapper").html(ajax_gif);
            $.ajax({
                method: "POST",
                data: serialize + "&did=" + selected,
                url: "<?php echo $site_path; ?>/engine/costing/listmanager.php?id=" + action + "&t=<?php echo $costing->type; ?>"
            }).done(function (msg) {
                if (msg == "1") {
                    location.reload();
                } else {
                    alert(msg);
                }
            });
            event.preventDefault();
        });
        //Add item to order
        $('#BATO').on("click", function () {
            var items = null;

            $(".sorder:checked").each(function () {
                items = items + "|" + $(this).val();
            });

            if (items === null) {
                $("#toc").html("Najpierw wybierz detale!");
            } else {
                $.ajax({
                    url: "<?php echo $site_path; ?>/engine/costing/order.php?id=3"
                }).done(function (form) {
                    $("#toc").html(form);
                });
            }
        });
        //Order search
        $("#toc").on('keyup', '#osinput', function () {
            $.ajax({
                url: "<?php echo $site_path; ?>/engine/costing/order.php?id=4&key=" + $("#osinput").val()
            }).done(function (msg) {
                $("#oscontent").html(msg);
            });
        });
        //Order search click
        $("#toc").on("click", ".osc", function () {
            var items = "";
            var iteml = 0;

            $(".sorder:checked").each(function () {
                if ($(this).val() != null) {
                    if (items == "") {
                        items = $(this).val();
                    } else {
                        items = items + "|" + $(this).val();
                    }
                    iteml += 1;
                }
            });

            var oid = parseInt($(this).attr("id"));
            var on = $(this).children(".oname").html();

            if (confirm("Dodaj " + iteml + " detali do zamówienia: " + on + "?")) {
                $.ajax({
                    url: "<?php echo $site_path; ?>/engine/costing/order.php?id=5&oid=" + oid + "&items=" + items
                }).done(function () {
                    $("#addToOrder").modal('hide');
                    toastr.success('Dodałem!');
                });
            }
        });

        //Order item click
        $("#OTD_CONTENT").on("click", ".o_click td", function () {
            oid_view = parseInt($(this).parent().attr("id"));
            if ($(this).hasClass("dob")) {
                $.ajax({
                    url: "<?php echo $site_path; ?>/engine/costing/order.php?id=11&oid=" + oid_view
                }).done(function (msg) {
                    if (msg == "1") {
                        location.reload();
                    } else {
                        toastr.error(msg, "Błąd!");
                    }
                });
            } else {
                orderView(oid_view);
            }
        });
        $("#orderInfoContent").on("click", ".difo", function () {
            App.blockUI({boxed: !0});
            var trp = $(this).parent().parent();
            var mi = parseInt($(trp).attr("id"));
            $.ajax({
                url: "<?php echo $site_path; ?>/engine/costing/order.php?id=7&mpw=" + mi
            }).done(function () {
                $(trp).fadeOut();
                App.unblockUI();
            });
        });

        //Edit pieces form
        var _pediti;
        $("#orderInfoContent").on("click", ".pediti", function () {
            _pediti = parseInt($(this).attr("id"));

            App.blockUI({boxed: !0});
            var par = $(this).parent();
            var pval = parseInt($(this).parent().html());
            var nd = '';
            $.ajax({
                url: "<?php echo $site_path; ?>/engine/costing/order.php?id=9&pval=" + pval
            }).done(function (content) {
                $(par).html(content);
                App.unblockUI();
            });
        });
        //Change pieces do
        $("#orderInfoContent").on("click", "#changep", function () {
            var pval = $("#pnv").val();

            $.ajax({
                url: "<?= $site_path ?>/engine/costing/order.php?id=10&pval=" + pval + "&mpw=" + _pediti
            }).done(function (msg) {
                App.unblockUI();
                orderView(oid_view);
                var jmessage = jQuery.parseJSON(msg);
                toastr[jmessage.type](jmessage.content, jmessage.header);
            });
        });

        //Priced item click
        $("#PTD_CONTENT").on("click", ".pitr", function () {
            var nr = parseInt($(this).parent().attr("id"));
            window.location.href = "<?php echo $site_path; ?>/view/601/" + nr + "/auto_costing";
        });

        //Priced auto plate item click
        $("#PTD_CONTENT").on("click", ".pspc", function () {
            var nr = parseInt($(this).parent().attr("id"));
            window.location.href = "<?php echo $site_path; ?>/view/602/" + nr + "/auto_costing";
        });

        //To production button
        $(".bAddToProduction").on("click", function () {
            window.location.href = "<?php echo $site_path; ?>/engine/costing/order.php?id=8&oid=" + oid_view;
        });

        //Multi part load modal button
        $("#addToMultiCosting").on("click", function () {
            if ($("input[name='selected[]']:checked").size() === 0) {
                $("#multi-wrapper").html('<div style="text-align: center"><p>Wybierz detale...</p></div>');
            } else {
                $.ajax({
                    method: "GET",
                    url: "<?= $site_path ?>/engine/costing/plateMultiPart.php?action=getDirectoryForm"
                }).done(function (response) {
                    $("#multi-wrapper").html(response);
                });
            }
        });
    });
</script>
<script type="text/javascript" src="/js/plateMultiPartForm/directoryView.js"></script>
<script type="text/javascript" src="/js/plateMultiPartForm/mpwView.js"></script>