<?php
$action = @$_GET["action"];
if ($action == 1) { // Save new queue
    require_once dirname(__FILE__) . '/../config.php';
    require_once dirname(__FILE__) . '/protect.php';

    $p = json_decode(str_replace('\\', '', $_POST["plis"]), true);

    $queue = array();
    for ($i = 0; $i < count($p); $i++) {
        $item = $p[$i]["id"];
        if (array_key_exists($item, $queue) == false) {
            $queue[$item] = $i;
        } else {
            $queue[$item] .= "|" . $i;
        }
    }
    foreach ($queue as $key => $value) {
        $db->query("UPDATE `programs` SET `position` = '$value' WHERE `id` = '$key'");
    }
    die("1");
}

function getPrograms()
{
    global $db;
    $programs = $db->query("
      SELECT 
      p.`name`,
      p.`id`,
      p.`mpw`,
      p.`cut`,
      p.`position`,
      cq.sheet_count as quantity,
      p.new_cutting_queue_id
      FROM `programs` p
      LEFT JOIN cutting_queue cq ON cq.id = p.new_cutting_queue_id
      WHERE p.`status` < 1 
      ORDER BY p.`id` DESC
    ");

    $queue = array();
    $ooq = array();

    foreach ($programs as $program) {
        $mpwa = json_decode($program["mpw"], true);
        $pieces = 0;
        if (is_array($mpwa)) {
            if (count($mpwa) > 0) {
                foreach ($mpwa as $name => $value) {
                    $pieces += $value;
                }
            }
        }

        if ($program["quantity"] > 0) {
            $pieces += $program["quantity"];
        }

        if ($program["cut"] < 1) {
            $program["cut"] = 0;
        }

        $position = intval($program["position"]);
        $str2 = '<li class="dd-item dd3-item" data-id="' . $program["id"] . '"><div class="dd-handle dd3-handle"></div><div class="dd3-content">' . $program["name"] . ' <div style="float: right; cursor: pointer;" class="bPinfo">' . $program["cut"] . '/' . $pieces . ' <i class="fa fa-info-circle"></i></div></div></li>';

        if (array_key_exists($position, $queue) == false) {
            $queue[$position] = $str2;
        } else {
            array_push($ooq, $str2);
        }
    }

    ksort($queue);
    foreach ($queue as $row) {
        echo $row;
    }
    foreach ($ooq as $row) {
        echo $row;
    }
}

?>

<div class="row">
    <div class="col-lg-12">
        <h2 class="page-title">Panel operatora</h2>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">Lista programów</div>
            </div>
            <div class="portlet-body">
                <div class="alert alert-danger" style="display: none;" id="amd">
                    <strong>Uwaga! Lista nie jest aktualna</strong>
                    <p>Jeśli jesteś w trakcje zmiany kolejki zapisz swój aktualny postęp, lub odświez.</p>
                    <div style="text-align: right;"><a href="<?php echo $site_path; ?>/site/15/operator"
                                                       class="btn btn-danger">Odświez</a></div>
                </div>
                <div id="slbuttons" style="text-align: right; display: none;">
                    <button type="button" class="btn btn-success">Zapisz</button>
                </div>
                <div class="dd" id="nestable">
                    <ol class="dd-list">
                        <?php
                        getPrograms();
                        ?>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">Podgląd</div>
            </div>
            <div class="portlet-body" id="pcontent">
                <div style="text-align: center;">
                    <small>Najpierw wybierz program...</small>
                </div>
                <div style="clear: both;"></div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var _plist = "";
    $(document).ready(function () {
        chlp = false;

        $('#nestable').nestable().on('change', function () {
            $("#slbuttons").slideDown();
            _plist = JSON.stringify($("#nestable").nestable('serialize'));
        });
    });
    $(".bPinfo").on("click", function () {
        var prId = $(this).parent().parent().attr("data-id");
        App.blockUI({target: "#pcontent"});
        $.ajax({
            url: "<?php echo $site_path; ?>/engine/chart/program.php?prId=" + prId
        }).done(function (content) {
            $("#pcontent").html(content);
            App.unblockUI();
        });
    });
    $("#slbuttons").on("click", function () {
        App.blockUI();
        $.ajax({
            method: "POST",
            data: "plis=" + _plist,
            url: "<?php echo $site_path; ?>/engine/operatorCut.php?action=1"
        }).done(function (msg) {
            App.unblockUI();
            window.location.reload();
        });
    });

</script>