<?php
$action = @$_GET["a"];
if (!empty($action)) {
    require_once '../config.php';
    require_once 'protect.php';
}
if ($action == 1) { //AJAX client list
    $content = $_POST['scontent'];
    if (is_numeric($content)) {
        $query = $db->prepare("SELECT `id`, `name`, `type` FROM `clients` WHERE `id` LIKE '%$content%'");
    } else {
        $query = $db->prepare("SELECT `id`, `name`, `type` FROM `clients` WHERE `name` LIKE '%$content%'");
    }
    die(getClientsShort($query));
} else if ($action == 2) { // AJAX project list
    $client = explode("_", $_GET["name"]);
    $_id = $client[0];
    $name = $client[1];

    $query = $db->prepare("SELECT * FROM `projects` WHERE `cid` = '$_id'");
    $query->execute();

    $data = "";
    foreach ($query as $row) {
        $add = null;
        if (@$_GET["tb"] == 1) {
            $add = '<i class="fa fa-pencil"></i>';
        }
        $data .= '<tr class="gradeA" style="cursor: pointer;" id="' . $row["id"] . '_' . $row["name"] . '"><td class="clickabl">' . $row["nr"] . '</td><td class="clickabl">' . $row["date"] . '</td><td class="_pname clickabl">' . $row['name'] . '</td><td>' . $add . '</td></tr>';
    }
    die($data);
} else if ($action == 3) { // AJAX Add project
    $client = explode("_", $_COOKIE["plClientName"]);
    $_id = $client[0];
    $name = preg_replace('/[^[:print:]]/', '', $client[1]);

    $directory = "";
    for ($i = -1; $i >= -1; $i += 50) {
        if ($_id >= $i && $_id <= $i + 50) {
            if ($i == -1) {
                $min = 1;
            } else {
                $min = $i + 1;
            }
            $max = $i + 50;
            $directory = $min . '-' . $max;
            break;
        } else {
            continue;
        }
        if ($i > 5000) {
            break;
        }
    }
    $src = $data_src . $directory . "/" . $_id . "/PROJEKTY/";
    $number = count(glob($src . "*", GLOB_ONLYDIR)) + 1;

    $pname = $_POST["name"];
    $dd = str_pad($_POST["dd"], 2, 0, STR_PAD_LEFT);
    $dm = str_pad($_POST["dm"], 2, 0, STR_PAD_LEFT);
    $dy = $_POST["dy"];
    $date = $dy . "-" . $dm . "-" . $dd;

    $p_src = $src . $number;
    mkdir($p_src, 0777, true);
    chown($p_src, $user_name);
    mkdir($p_src . "/V1");
    chown($p_src . "/V1", $user_name);

    $addProject = $db->prepare("INSERT INTO `projects` (`nr`, `cid`, `date`, `name`, `src`) VALUES ('$number', '$_id', '$date', '$pname', '$p_src')");
    $addProject->execute();

    //Status
    $pid = $db->lastInsertId();
    $date = date("Y-m-d H:i:s");
    $db->query("INSERT INTO `pstatus` (`pid`, `status`, `date`) VALUES ('$pid', '1', '$date')");

    die("1");
} else if ($action == 4) { // AJAX Scan
    $client = explode("_", $_GET["name"]);
    $_id = $client[0];
    $name = $client[1];

    $directory = "";
    for ($i = -1; $i >= -1; $i += 50) {
        if ($_id >= $i && $_id <= $i + 50) {
            if ($i == -1) {
                $min = 1;
            } else {
                $min = $i + 1;
            }
            $max = $i + 50;
            $directory = $min . '-' . $max;
            break;
        } else {
            continue;
        }
        if ($i > 5000) {
            break;
        }
    }
    $src = $data_src . $directory . "/" . $_id . "/PROJEKTY/";

    $aProjects = 0;
    foreach (glob($src . "*", GLOB_ONLYDIR) as $pdir) {
        $project = explode(" ", end((explode("/", $pdir))));

        $nr = $project[0];
        $tDate = explode("-", $project[1]);
        $date = $tDate[2] . "-" . $tDate[1] . "-" . $tDate[0];
        $name = $project[2];

        $query = $db->prepare("SELECT `id` FROM `projects` WHERE `nr` = '$nr' AND `cid` = '$_id'");
        $query->execute();
        if ($query->rowCount() == 0) {
            $aProjects++;
            $addProject = $db->prepare("INSERT INTO `projects` (`nr`, `cid`, `date`, `name`, `src`) VALUES ('$nr', '$_id', '$date', '$name', '$pdir')");
            $addProject->execute();
        }
    }
    if ($aProjects == 0) {
        $aProjects = "0";
    } else {
        $aProjects = " " . $aProjects;
    }
    die($aProjects);
} else if ($action == 5) {
    $pid = $_GET["pid"];

    $project_q = $db->prepare("SELECT `date`, `name` FROM `projects` WHERE `id` = :pid");
    $project_q->bindValue(":pid", $pid, PDO::PARAM_INT);
    $project_q->execute();
    $project = $project_q->fetch();
}
?>

<div class="row">
    <div class="col-lg-12">
        <h2 class="page-title">Baza projektów</h2>
    </div>
</div>
<div class="row">
    <div class="col-lg-3">
        <div class="widget">
            <div class="widget-header"> <i class="icon-search"></i>
                <h3>Wyszukaj firmę</h3>
            </div>
            <div class="widget-content">
                <form id="sform">
                    <input type="search" placeholder="ID lub nazwa" id="search-input" class="form-control" name="scontent">
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-9">
        <div class="widget">
            <div class="widget-header"> <i class="icon-book"></i>
                <h3>Lista firmy</h3>
            </div>
            <div class="widget-content">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <td>ID</td>
                            <td>Nazwa</td>
                            <td>Typ</td>
                            <td></td>
                        </tr>
                    </thead>
                    <tbody id="clist">
                        <tr><td>Brak wyników</td><td></td><td></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="row" id="messageBox"></div>
<div class="row" style="display: none" id="pdiv">
    <div class="col-lg-3">
        <div class="widget">
            <div class="widget-content" style="text-align: center;">
                <a href="#projectForm" data-toggle="modal" class="btn btn-info">Dodaj nowy</a>
                <a href="#" id="aScan" data-toggle="modal" class="btn btn-info">Skanuj</a>
                <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="projectForm" class="modal fade" style="display: none;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
                            <h4 class="modal-title">Dodaj nowy projekt</h4>
                        </div>
                        <form id="_projectForm" class="form-inline" method="POST" action="?">
                            <div class="modal-body">
                                <div id="error" style="display: none;">
                                    <div class="alert alert-block alert-danger fade in">
                                        <strong>Błąd!</strong> Uzupełnij pole nazwy!
                                    </div>
                                </div>
                                <div id="doneMessage" style="display: none;">
                                    <div class="alert alert-success alert-block fade in">
                                        <h4> <i class="icon-ok-sign"></i> Gotowe! </h4>
                                        <p>Projekt zapisany</p>
                                    </div>
                                </div>
                                <table style="margin: 0 auto; border-collapse: separate; border-spacing: 2px;">
                                    <tr><td style="text-align: right;">Nazwa:</td><td><input type="text" name="name" class="form-control" id="name"/></td></tr>
                                    <tr><td style="text-align: right;">Data:</td><td><?php echo '<input type="number" class="form-control" name="dd" value="' . date('d') . '" style="width: 55px"/><input type="number" name="dm" class="form-control" value="' . date('m') . '" style="width: 55px"/><input type="number" class="form-control" name="dy" value="' . date('Y') . '" style="width: 86px"/>'; ?></td></tr>
                                </table>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>
                                <button type="submit" class="btn btn-success">Zapisz</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-9">
        <div class="widget">
            <div class="widget-header"> <i class="icon-copy"></i>
                <h3>Projekty</h3>
            </div>
            <div class="widget-content" id="pcontent"></div>
        </div>
    </div>
</div>
<div style="display: none;" aria-hidden="true" aria-labelledby="editModal" role="dialog" tabindex="-1" class="modal fade modal-scroll modal-overflow" id="addToCostingModal">
    <div class="modal-content">
        <form action="?" id="editform" method="POST">
            <div class="modal-header">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
                <h4 id="myModalLabel" class="modal-title">Edytuj dane</h4>
            </div>
            <div class="modal-body" id="costing-wrapper">
                <table>
                    <tr><td>Nazwa projektu</td><td><input name="p_name_input" type="text" class="form-control" placeholder=""/></td></tr>
                    <tr><td>Data utworzenia</td><td><input name="p_date_input" class="form-control date-picker" data-date-format="yyyy-mm-dd" size="16" type="text" value=""/></td></tr>
                    <tr><td>Data zakończenia</td><td><input name="p_date_input2" class="form-control date-picker" data-date-format="yyyy-mm-dd" size="16" type="text" value=""/></td></tr>
                </table>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">Zamknij</button>
                <button class="btn btn-primary" type="submit">Zapisz</button>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
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
        $("#pdiv").fadeIn();
        $.ajax({
            url: "<?php echo $site_path; ?>/engine/projectbase.php?a=2&tb=1&name=" + _id
        }).done(function (msg) {
            $('html,body').animate({scrollTop: $("#pdiv").offset().top}, 1000);
            Cookies.set("plClientName", _id);
            $("#pcontent").html('<table cellpadding="0" cellspacing="0" border="0" id="plist" class="display"><thead><tr><td>Nr</td><td>Data</td><td>Nazwa</td><td>Akcje</td></tr></thead><tbody id="plistc"></tbody></table>');
            $("#plistc").html(msg);
            table = $('#plist').dataTable({
                "sPaginationType": "full_numbers"
            });
        });
    }

    var edit_id;

    $(document).ready(function () {
        $('.datepicker').datepicker();

        //Edit
        $("#pcontent").on("click", "table tr td", function () {
            if (!$(this).hasClass("clickabl")) {
                edit_id = parseInt($(this).parent().attr("id"));
                App.blockUI({boxed: !0});
                $.ajax({
                    url: "<?php echo $site_path; ?>/engine/projectbase.php?a=5&pid=" + edit_id
                }).done(function (msg) {
                    var returns;
                    if (msg != "") {
                        returns = JSON.parse(msg);
                        $("input[name='p_name_input']").val(returns.name);
                        $("input[name=p_date_input]").val(returns.startdate);
                        $("input[name=p_date_input2]").val(returns.endtdate);
                    } else {
                        $("input").val("");
                    }
                    App.unblockUI();
                    $("#editModal").modal("show")
                });
            }
        });

        //Galery
        $("#pcontent").on("click", "table tr .clickabl", function () {
            var _id = parseInt($(this).parent().attr("id"));
            history.replaceState({}, 'ABL', "/manager/site/7/projekty");
            location.replace("<?php echo $site_path; ?>/galery/12/" + _id + "/");
        });
        var table = null
        $("#clist").on("click", "tr", function () {
            var _id = $(this).attr("id");
            pList(_id);
        });
        $("#aScan").on("click", function () {
            $.ajax({
                url: "<?php echo $site_path; ?>/engine/projectbase.php?a=4&name=" + Cookies.get("plClientName")
            }).done(function (msg) {
                $("#messageBox").append('<div class="alert alert-success alert-block fade in"><button type="button" class="close close-sm" data-dismiss="alert"> <i class="icon-remove"></i> </button><h4> <i class="icon-ok-sign"></i> Gotowe! </h4><p>Do bazy dodano: ' + msg + ' projektów</p></div>');
                pList(Cookies.get("plClientName"));
            });
        });
        $("#search-input").keyup(function () {
            search();
        });
    });
    $("#sform").submit(function (event) {
        search();
        event.preventDefault();
    });
    $("#_projectForm").submit(function (event) {
        $("#doneMessage").fadeOut();
        if ($("#name").val() == "") {
            $("#error").fadeIn();
        } else {
            $("#error").fadeOut();
            $.ajax({
                method: "POST",
                url: "<?php echo $site_path; ?>/engine/projectbase.php?a=3",
                data: $("#_projectForm").serialize()
            }).done(function (msg) {
                $("#doneMessage").fadeIn();
                $("#name").val("");
                pList(Cookies.get("plClientName"));
            });
        }
        event.preventDefault();
    });
</script>
