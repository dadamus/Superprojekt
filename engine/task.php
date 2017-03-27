<?php
require_once dirname(__FILE__) . '/../config.php';
require_once dirname(__FILE__) . '/class/calendar.php';

$calendar = new calendar($site_path);

function getTask($filter = null) {
    global $db;

    $addtoq = "";
    if ($filter != null) {
        $addtoq = $filter;
    } else {
        $addtoq = "SELECT * FROM `calendar`";
    }

    $tasks = $db->query($addtoq);
    $return = "";

    $task_ids = [];
    
    foreach ($tasks as $task) {
        if(array_search($task["id"], $task_ids) === false) {
            array_push($task_ids, $task["id"]);
        } else {
            continue;
        }
        
        $icon = "";
        switch ($task["type"]) {
            case 1:
                $icon = '<a href="javascript:;" class="tooltips" data-placement="top" data-container=”body” data-toggle="tooltip" title="Pilność: Normalna"><i class="fa fa-circle-o"></i></a>';
                break;
            case 2:
                $icon = '<a href="javascript:;" class="tooltips" data-placement="top" data-container=”body” data-toggle="tooltip" title="Pilność: Wysoka"><i class="fa fa-exclamation"></i></a>';
                break;
            case 3:
                $icon = '<a href="javascript:;" class="tooltips" data-placement="top" data-container=”body” data-toggle="tooltip" title="Pilność: Bardzo duża"><i class="fa fa-exclamation-triangle"></i></a>';
                break;
        }

        $return .= '<li class="mt-list-item"><div class="list-icon-container">' . $icon . '</div><div class="list-datetime" style="width: 150px !important">' . $task["startdate"] . '</div><div class="list-item-content"><p>' . $task["title"] . '</p></div></li>';
    }
    return $return;
}

$action = @$_GET["a"];
if ($action != "") { //Ajax content
    require_once 'protect.php';
}

function joinText($tableName, $prefix, $calendarId, $searchId, $data) {

    $joinText = " INNER JOIN `$tableName` $prefix ON (calendar.id = $prefix.$calendarId AND (";
    $values = null;
    foreach ($data as $value) {
        if ($values != null) {
            $values .= " OR ";
        }

        $values .= "$prefix.$searchId = '$value'";
    }
    $joinText .= $values . '))';
    return $joinText;
}

if ($action == 1) { //Filter
    $where_filter = null;
    $pf = null;

    $filter = null;

    $qstr = "SELECT calendar.* FROM `calendar`";

    //Inner filter
    if (isset($_POST["userFilter"])) {
        $qstr .= joinText("calendar_user", "cu", "cid", "uid", $_POST["userFilter"]);
    }
    if (isset($_POST["programFilter"])) {
        $qstr .= joinText("calendar_programs", "cp", "cid", "pid", $_POST["programFilter"]);
    }
    if (isset($_POST["detailFilter"])) {
        $qstr .= joinText("calendar_details", "cd", "cid", "did", $_POST["detailFilter"]);
    }

    //WHERE filter
    if (isset($_POST["priorFilter"])) {
        foreach (@$_POST["priorFilter"] as $priorFilter) {
            if ($pf != null) {
                $pf .= " OR ";
            }
            $pf .= "calendar.type = '$priorFilter'";
        }
    }

    if ($pf != null) {
        $where_filter = "($pf)";
    }

    //Projekt filter
    $prf = null;
    if (isset($_POST["projectFilter"])) {
        foreach (@$_POST["projectFilter"] as $project) {
            if ($prf != null) {
                $prf .= " OR ";
            } else {
                $prf = "(";
            }
            $prf .= "calendar.project = '$project'";
        }
        $prf .= ")";

        if ($where_filter != null) {
            $where_filter .= " AND ";
        }
        $where_filter .= $prf;
    }

    //Date filter
    if (strlen(@$_POST["dateFilter"]) > 1) {
        $dvalue = explode(":", $_POST["dateFilter"]);
        $dateStart = $dvalue[0];
        $dateEnd = $dvalue[1];

        if ($where_filter != null) {
            $where_filter .= " AND ";
        }

        $where_filter .= "calendar.startdate` >= '$dateStart' AND calendar.startdate <= '$dateEnd'";
    }

    $filter = $qstr;
    if ($where_filter != null) {
        $filter .= " WHERE " . $where_filter;
    }
    
    //die($filter);
    
    die(getTask($filter));
}
?>
<div class="row">
    <div class="col-lg-12">
        <h2 class="page-title">Zadania</h2>
    </div>
</div>
<div class="row">
    <div class="col-lg-3 col-md-12">
        <div class="portlet box blue-hoki">
            <div class="portlet-title">
                <div class="caption">
                    Filtry
                </div>
            </div>
            <div class="portlet-body">
                <div class="col-md-12">
                    <form id="filterForm" action="?">
                        <div class="row">
                            <select class="bs-select form-control" name="calendarFilter">
                                <option value="-1">Wszystkie</option>
                                <?php
                                foreach ($calendar->menu as $key => $value) {
                                    echo '<option value="' . $key . '">' . $value["text"] . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="row" style="margin-top: 10px;">
                            <select class="bs-select form-control" multiple="multiple" title="Użytkownicy" data-live-search="true" name="userFilter[]">
                                <?php
                                $uquery = $db->query("SELECT `name`, `id` FROM `accounts`");
                                foreach ($uquery as $value) {
                                    echo '<option value="' . $value["id"] . '">' . $value["name"] . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="row" style="margin-top: 10px;">
                            <select class="bs-select form-control" multiple="multiple" title="Projekty" data-live-search="true" name="projectFilter[]">
                                <?php
                                $uquery = $db->query("SELECT `name`, `id` FROM `projects`");
                                foreach ($uquery as $value) {
                                    echo '<option value="' . $value["id"] . '">' . $value["id"] . ' - ' . $value["name"] . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="row" style="margin-top: 10px;">
                            <select class="bs-select form-control" data-live-search="true" title="Detale" multiple="multiple" name="detailFilter[]">
                                <?php
                                $uquery = $db->query("SELECT `src`, `id` FROM `details`");
                                foreach ($uquery as $value) {
                                    echo '<option value="' . $value["id"] . '">' . $value["id"] . ' - ' . $value["src"] . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="row" style="margin-top: 10px;">
                            <select class="bs-select form-control" data-live-search="true" title="Programy" multiple="multiple" name="programFilter[]">
                                <?php
                                $uquery = $db->query("SELECT `name`, `id` FROM `programs`");
                                foreach ($uquery as $value) {
                                    echo '<option value="' . $value["id"] . '">' . $value["name"] . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="row" style="margin-top: 10px;">
                            <select class="bs-select form-control" multiple title="Pilność" data-actions-box="true" name="priorFilter[]">
                                <option value="0">Niska</option>
                                <option value="1">Normalna</option>
                                <option value="2">Wysoka</option>
                                <option value="3">Bardzo duża</option>
                            </select>
                        </div>
                        <div class="row">
                            <div id="defaultrange" style="margin-bottom: 10px; margin-top: 10px;">
                                <input type="text" name="dateFilter" class="form-control" placeholder="Data">
                            </div>
                        </div>
                        <div class="row" style="text-align: right;">
                            <button type="submit" class="btn green"><i class="fa fa-filter"></i> Filtruj</button>
                            <button type="button" class="btn white" id="b_reset"><i class="fa fa-eraser"></i> Reset</button>
                        </div>
                    </form>
                </div>
                <div style="clear: both;"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-9 col-md-12">
        <div class="portlet light portlet-fit bordered">
            <div class="portlet-body">
                <div class="mt-element-list">
                    <div class="mt-list-head list-simple ext-1 font-white bg-green-sharp">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="list-head-title-container">
                                    <h3 class="list-title uppercase sbold">Lista zadań</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-list-container list-default ext-1">
                        <ul id="listContent">
                            <?php
                            echo getTask();
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function reloadTask(filter) {
        App.blockUI({boxed: !0});
        $.ajax({
            method: "POST",
            data: filter,
            url: "<?php echo $site_path; ?>/engine/task.php?a=1"
        }).done(function (msg) {
            $("#listContent").html(msg);
            App.unblockUI();
        });
    }

    function resetFilter() {
        $("#filterForm select > option").removeAttr("selected");
        $("#filterForm select").each(function () {
            if (!$(this).prop("multiple")) {
                $(this).val($(this).children("option:first").val());
            }
        });
        $("#filterForm select").selectpicker('render');

        $("#filterForm input").val('');
        $.uniform.update();

        reloadTask("");
    }

    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();
        $(".bs-select").selectpicker({iconBase: "fa", tickIcon: "fa-check"});

        $("#defaultrange").daterangepicker({
            opens: App.isRTL() ? "left" : "right",
            format: "MM/DD/YYYY",
            separator: " do ",
            startDate: moment().subtract("days", 29),
            endDate: moment(),
            ranges: {
                "Dziś": [moment(), moment()],
                "Wczoraj": [moment().subtract("days", 1), moment().subtract("days", 1)],
                "Ostatnie 7 dni": [moment().subtract("days", 6), moment()],
                "Ostatnie 30 dni": [moment().subtract("days", 29), moment()],
                "Ten miesiąc": [moment().startOf("month"), moment().endOf("month")],
                "Ostatni miesiąc": [moment().subtract("month", 1).startOf("month"), moment().subtract("month", 1).endOf("month")]},
            minDate: "01/01/2012",
            maxDate: "12/31/2018"},
                function (t, e) {
                    $("#defaultrange input").val(t.format("YYYY-MM-DD") + " : " + e.format("YYYY-MM-DD"))
                });
    });

    $("#b_reset").on("click", function () {
        resetFilter();
    });

    $("#filterForm").on("submit", function (e) {
        e.preventDefault();
        var form = $("#filterForm").serialize();
        reloadTask(form);
    });
</script>