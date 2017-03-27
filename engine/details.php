<?php
require_once dirname(__FILE__) . '/../config.php';

if (@$_GET["a"] == 1) {
    require_once 'protect.php';
    $pid = $_COOKIE["plProjectId"];
    $type = $_GET["t"];
    $query = $db->prepare("SELECT * FROM `details` WHERE `pid` = '$pid' AND (`type` = '$type' OR `type` = '0')");

    die(getDetails($query));
}
if (@$_GET["a"] == 2) { //Reload details
    $filter = array();

    $page = @$_GET["page"];
    if ($page == null) {
        $page = 1;
    }

    //Roto blacha
    if (!empty($_POST["type"])) {
        $ntype = 0;
        foreach ($_POST["type"] as $check) {
            $filter["type"][$ntype] = $check;
            $ntype++;
        }
    }

    //Klienci
    if ($_POST["client"] != 0) {
        $filter["client"] = $_POST["client"];
    }

    //Projekt
    if ($_POST["project"] != 0) {
        $filter["project"] = $_POST["project"];
    }

    //Material
    if (!empty($_POST["material"])) {
        $mtype = 0;
        foreach ($_POST["material"] as $material) {
            if ($material != 0) {
                $filter["material"][$mtype] = $material;
                $mtype++;
            }
        }
    }

    if ($_POST["date"] != "") {
        $rdate = str_replace(' ', '', $_POST["date"]);
        $edate = explode(':', $rdate);
        if (count($edate) == 2) {
            $filter["date"]["from"] = $edate[0] . " 00:00:00";
            $filter["date"]["to"] = $edate[1] . " 24:60:60";
        }
    }

    die(json_encode(selectDetail($page, $filter)));
}

function getDetails($query, $type = null) { //OLD only for some old script
    $query->execute();
    $data = "";

    global $db;

    foreach ($query as $row) {
        $projects = $db->prepare("SELECT `name`, `cid` FROM `projects` WHERE `id` = '" . $row["pid"] . "'");
        $projects->execute();

        $projectName = "";
        $cid = 0;
        foreach ($projects as $_project) {
            $projectName = $_project["name"];
            $cid = $_project["cid"];
        }

        $clients = $db->prepare("SELECT `name` FROM `clients` WHERE `id` = '$cid'");
        $clients->execute();

        $clientName = "";
        foreach ($clients as $_client) {
            $clientName = $_client["name"];
        }

        $did = $row["id"];
        $default_price = 0;
        $default = $db->prepare("SELECT `dprice` FROM `plate_costing` WHERE `did` = '$did' AND `default` = '1'");
        $default->execute();
        if ($default->rowCount() > 0) {
            foreach ($default as $drow) {
                $default_price = $drow["dprice"];
            }
        } else {
            $default2 = $db->prepare("SELECT `priceset` FROM `profile_costing` WHERE `did` = '$did' AND `default` = '1'");
            $default2->execute();
            if ($default2->rowCount() > 0) {
                foreach ($default2 as $drow) {
                    $default_price = $drow["priceset"];
                }
            }
        }
        $did = $row["id"];
        $status = statusGetAll($did);

        $query = $db->prepare("SELECT `id` FROM `status` WHERE `did` = '$did' AND `type` = '5'");
        $query->execute();
        $count = $query->rowCount();

        $detail = new Detail($row["type"]);

        if ($type == null) {
            if ($count > 0) {
                $data .= '<strike>';
            }
            $data .= '<tr class="gradeA" style="cursor: pointer" id="' . $row["id"] . '_did"><td><input type="checkbox" class="form-control" name="selected[]" value="' . $row["id"] . '" style="width: 20px; height: 20px;"/></td><td class="ca">' . $row["id"] . '</td><td class="ca">' . $clientName . '</td><td class="ca">' . $projectName . '</td><td class="_dname ca">' . $row["src"] . '</td><td class="ca">' . $status . '</td><td class="ca">' . $default_price . 'zł</td><td><a href="abl:\\"><i class="fa fa-folder-open-o"></i></a></td></tr>';
            if ($count > 0) {
                $data .= '</strike>';
            }
        } else {
            $data .= '<tr class="gradeA" style="cursor: pointer" id="' . $row["id"] . '_did"><td><input type="checkbox" class="form-control" style="width: 20px; height: 20px;"/></td><td>' . $row["id"] . '</td><td class="_dname">' . $row["src"] . '</td><td>' . $projectName . '</td><td>' . $detail->s_type . '</td><td>' . $row["code"] . '</td><td>' . $row["status"] . '</td><td>' . $row["dxf"] . '</td><td>' . $row["sldprt"] . '</td><td>' . $row["date"] . '</td><td></td><td></td><td>I</td><td>C</td></tr>';
        }
    }

    return $data;
}

$in_row = 0;

function addPattern($p_id, $p_name, $p_date, $p_img) {
    global $in_row, $site_path;

    $return = "";
    if ($in_row == 0) {
        $return .= "<div class='row'>";
    }
    $return .= '<div class="col-lg-3 col-md-3 col-sm-3 col-xs-6"> <div class="mt-card-item"><div class="mt-card-avatar mt-overlay-1" style="height: 200px;">' . $p_img . '<div class="mt-overlay"><ul class="mt-info"><li><a class="btn default btn-outline" href="' . $site_path . '/detail/' . $p_id . '/"><i class="icon-magnifier"></i></a></li></ul></div></div><div class="mt-card-content"><h3 class="mt-card-name" style="word-wrap:break-word;">' . $p_name . '</h3><p class="mt-card-desc font-grey-mint">' . $p_date . '</p></div></div></div>';

    if ($in_row == 3) {
        $return .= "</div>";
        $in_row = -1;
    }
    $in_row++;
    return $return;
}

function selectDetail($page = 1, $filter = null) {
    global $db, $site_path, $data_src;

    $add_to_query = array();
    if (is_array($filter)) {
        foreach ($filter as $key => $value) {
            switch ($key) {
                case "type":
                    if (count($value) == 3) {
                        array_push($add_to_query, "(`type` = '" . $value[0] . "' OR `type` = '" . $value[1] . "' OR `type` = '" . $value[2] . "')");
                    } else if (count($value) == 2) {
                        array_push($add_to_query, "(`type` = '" . $value[0] . "' OR `type` = '" . $value[1] . "')");
                    } else {
                        array_push($add_to_query, "`type` = '" . $value[0] . "'");
                    }
                    break;

                case "client":
                    $qcp = $db->query("SELECT `id` FROM `projects` WHERE `cid` = '$value'");

                    $pids = null;
                    foreach ($qcp as $cp) {
                        if ($pids == null) {
                            $pids = "(`pid` = '" . $cp["id"] . "'";
                        } else {
                            $pids .= " OR `pid` = '" . $cp["id"] . "'";
                        }
                    }
                    $pids .= ")";
                    array_push($add_to_query, $pids);
                    break;

                case "project":
                    array_push($add_to_query, "`pid` = '$value'");
                    break;

                case "material":
                    $qmt = null;
                    if (is_array($value)) {
                        if (count($value) == 1) {
                            $qmt = "`material` = '" . $value[0] . "'";
                        } else {
                            foreach ($value as $m) {
                                if ($qmt == null) {
                                    $qmt = "(`material` = '$m'";
                                } else {
                                    $qmt .= " OR `material` = '$m'";
                                }
                            }
                            $qmt .= ")";
                        }

                        $did = array();
                        $qfs = null;
                        $qm = $db->query("SELECT `did` FROM `mpw` WHERE $qmt");
                        foreach ($qm as $mpw) {
                            if (!array_search($mpw["did"], $did)) {
                                if ($qfs == null) {
                                    $qfs = "(`id` = '" . $mpw["did"] . "'";
                                } else {
                                    $qfs .= " OR `id` = '" . $mpw["did"] . "'";
                                }
                                array_push($did, $mpw["did"]);
                            }
                        }
                        $qfs .= ")";
                        array_push($add_to_query, $qfs);
                    }
                    break;

                case "date":
                    array_push($add_to_query, "(`date` >= '" . $value["from"] . "' AND `date` <= '" . $value["to"] . "')");
                    break;
            }
        }
    }

    $max = $page * 20;
    $offset = $max - ($page * 20);

    $d_img = '<img src="holder.js/100%x200" class="holder" alt="" style="width: 100%;">';

    $return = "";

    $sadq = null;
    if (count($add_to_query) > 0) {
        foreach ($add_to_query as $wh) {
            if ($sadq == null) {
                $sadq = "WHERE " . $wh;
            } else {
                $sadq .= " AND " . $wh;
            }
        }
    }

    //die("SELECT * FROM `details` $sadq ORDER BY `id` DESC LIMIT $offset, $max");
    $details = $db->query("SELECT * FROM `details` $sadq ORDER BY `id` DESC LIMIT $offset, $max");
    $qpages = $db->query("SELECT count(*) FROM `details` $sadq");
    $detail_number = $qpages->fetchColumn();

    foreach ($details as $detail) {
        if ($detail["img"] == '') {
            $p_img = $d_img;
        } else {
            $p_img = '<div style="width: 100%; height: 100%; background: url(\'/data/detale/img/min/' . $detail["img"] . '\'); background-repeat: no-repeat; background-position: center; background-size: 100% auto; ></div>';
        }

        $p_id = $detail["id"];
        $p_name = $detail["src"];
        $p_date = $detail["date"];

        $return .= addPattern($p_id, $p_name, $p_date, $p_img);
    }
    return array("return" => $return, "dp" => $detail_number);
}
?>
<div class="row">
    <div class="col-lg-12">
        <h2 class="page-title">Spis detali</h2>
    </div>
</div>
<div class="row">
    <div class="col-lg-3 col-md-12 col-sm-12">
        <div class="portlet box blue-hoki">
            <div class="portlet-title">
                <div class="caption">Filtry</div>
            </div>
            <div class="portlet-body">
                <div class="col-md-12">
                    <form id="filter" action="?">
                        <div class="row" style="margin-bottom: 10px;">
                            <div class="clearfix">
                                <div class="btn-group" data-toggle="buttons">
                                    <label class="btn default active">
                                        <input value="0" name="type[]" type="checkbox" class="toggle" checked/>
                                        No data
                                    </label>
                                    <label class="btn default active">
                                        <input value="1" name="type[]" type="checkbox" class="toggle" checked/>
                                        Blacha
                                    </label>
                                    <label class="btn default active">
                                        <input value="2" name="type[]" type="checkbox" class="toggle" checked/>
                                        Roto
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-bottom: 10px;">
                            <select name="client" class="bs-select form-control" data-live-search="true" data-size="8">
                                <option value="0">- Firmy -</option>
                                <?php
                                $qclient = $db->query("SELECT `id`, `name` FROM `clients`");
                                foreach ($qclient as $client) {
                                    echo '<option value="' . $client["id"] . '">' . $client["id"] . ' | ' . $client["name"] . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="row" style="margin-bottom: 10px;">
                            <select name="project" class="bs-select form-control" data-live-search="true" data-size="8">
                                <option value="0">- Projekty -</option>
                                <?php
                                $qproject = $db->query("SELECT `id`, `name` FROM `projects`");
                                foreach ($qproject as $project) {
                                    echo '<option value="' . $project["id"] . '">' . $project["id"] . ' | ' . $project["name"] . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="row" style="margin-bottom: 10px;">
                            <select name="material[]" class="bs-select form-control" multiple data-actions-box="true">
                                <option value="0">Brak</option>
                                <?php
                                $qmaterial = $db->query("SELECT `id`, `lname` FROM `material`");
                                foreach ($qmaterial as $material) {
                                    echo '<option value="' . $material["id"] . '">' . $material["lname"] . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="row" style="margin-bottom: 10px;">
                            <div id="defaultrange">
                                <input type="text" name="date" class="form-control" placeholder="Data">
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
    <div class="col-lg-9 col-md-12 col-sm-12">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">Detale</div>
            </div>
            <div class="portlet-body">
                <div class="mt-element-card mt-element-overlay" id="detail_contener">
                    <div class="row">
                        <?php
                        $det = selectDetail();
                        echo $det["return"];
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-12 col-md-12">
        <div class="portlet light bordered">
            <div class="portlet-body" style="text-align: center;">
                <div>
                    <ul class="pagination pagination-lg" id="page_list">
                        <?php
                        $qpages = $db->query("SELECT COUNT(*) FROM `details`");
                        $detail_number = $qpages->fetchColumn();

                        $pages = ceil($detail_number / 20);
                        for ($i = 1; $i <= $pages; $i++) {
                            $class = '';
                            if ($i == 1) {
                                $class = ' active';
                            }
                            echo '<li class="page_button ' . $class . '" id="' . $i . '_page"><a href="javascript:;">' . $i . '</a></li>';
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    function reloadDetails(page) {
        var page = (typeof page !== 'undefined') ? page : 1;

        App.blockUI({boxed: !0});
        $(".mt-card-item").animate({
            opacity: 0.1
        }, "normal", function () {
            var serialize = $("#filter").serialize();
            $.ajax({
                method: "POST",
                data: serialize,
                url: site_path + "/engine/details.php?a=2&page=" + page
            }).done(function (msg) {
                var data = JSON.parse(msg);
                $("#detail_contener").html(data.return);
                $(".holder").each(function () {
                    Holder.run({images: this});
                });

                var details = parseInt(data.dp);
                var pages = Math.ceil(details / 20);
                var pages_html = "";
                for (i = 1; i <= pages; i++) {
                    var _class = "page_button";
                    if (i == page) {
                        _class += " active";
                    }

                    pages_html += '<li class="' + _class + '" id="' + i + '_page"><a href="javascript:;">' + i + '</a></li>';
                }
                $("#page_list").html(pages_html);
                App.unblockUI();

            });
        });
    }

    function resetFilter() {
        $("#filter :checkbox").each(function () {
            $(this).prop('checked', true);
            if (!$(this).parent().hasClass("active")) {
                $(this).parent().addClass("active");
            }
        });

        $("#filter select").val(0);
        $("#filter select").selectpicker('render');

        $("#filter input[name='date']").val('');

        reloadDetails();
        $.uniform.update();
    }

    $(document).ready(function () {
        $(".bs-select").selectpicker({iconBase: "fa", tickIcon: "fa-check"});

        //Reset filter button
        $("#b_reset").on("click", function () {
            resetFilter();
        });

        //Filter set
        $("#filter").on("submit", function (e) {
            reloadDetails();
            e.preventDefault();
        });

        //Page set
        $("#page_list").on("click", ".page_button", function () {
            var _page = parseInt($(this).attr("id"));
            $("#page_list .active").removeClass("active");
            $(this).addClass("active");
            reloadDetails(_page);
        });

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
</script>