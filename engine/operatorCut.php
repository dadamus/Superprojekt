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
      cq.parent_synced,
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

        // ------ Tutaj parenty
        $parentQuery = $db->prepare("
            SELECT COUNT(*) as ile FROM cutting_queue_list WHERE state <> 2 AND cutting_queue_id = :cqid
        ");
        $parentQuery->bindParam(":cqid", $program["new_cutting_queue_id"], PDO::PARAM_INT);
        $parentQuery->execute();
        $count = $parentQuery->fetch();

        if ($count['ile'] == 0) {
            continue;
        }

        // ------ Tutaj update parentu odpadu

        if ($program['parent_synced'] === 0) {
            //Najpierw detale bo to do nich jest blacha przypisana
            $detailWasteQuery = $db->prepare("SELECT id, plate_warehouse_id FROM cutting_queue_details WHERE cutting_queue_list_id = :listId");
            $detailWasteQuery->bindValue(':listId', $program['new_cutting_queue_id'], PDO::PARAM_INT);
            $detailWasteQuery->execute();

            $detailsCount = 0;
            $syncedCount = 0;
            while($row = $detailWasteQuery->fetch())
            {
                $details++;

            }

            if ($detailsCount === $syncedCount) {
                $queueUpdateQuery = $db->prepare("UPDATE cutting_queue SET parent_synced = 1 WHERE id = :queueId");
                $queueUpdateQuery->bindValue(":queueId", $program['new_cutting_queue_id'], PDO::PARAM_INT);
                $queueUpdateQuery->execute();
            }
        }

        if ($program["quantity"] > 0) {
            $pieces += $program["quantity"];
        }

        if ($program["cut"] < 1) {
            $program["cut"] = 0;
        }

        $programName = str_replace('.', '+', $program['name']);

        $position = intval($program["position"]);
        $str2 = '<li class="dd-item dd3-item" data-id="' . $program["id"] . '"><div class="dd-handle dd3-handle"></div><div class="dd3-content">' . $programName . ' <div style="float: right; cursor: pointer;" class="bPinfo">' . $program["cut"] . '/' . $pieces . ' <i class="fa fa-info-circle"></i></div></div></li>';

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

    $("#pcontent").on('click', '.ajax-modal', function (e) {
        //Modal do zmiany statusu
        e.preventDefault();

        App.blockUI();
        var url = $(this).attr('href');
        $.ajax({
            method: 'GET',
            url: url
        }).done(function (response) {
            $('#modal-container').html(response).find('#status-modal').modal('show');
        }).always(function () {
            App.unblockUI();
        });
    });
    $(document).on('change', 'select[name="list-state"]', function () {
        //Tu przy statusie 2,3 otwieramy pole do wpisanie sztuk detali
        var $statusSelect = $('select[name="list-state"]');
        var state = $statusSelect.val();

        var $detailsRow = $('.list-details');

        if (state == 2 || state == 3) {
            $detailsRow.show();
        } else {
            $detailsRow.hide();
            return true;
        }

    });
    $(document).on('click', '.submit-status-change', function () {
        //A tu juz zmiana statusu
        var listId = $(this).data('list-id');
        var $statusSelect = $('select[name="list-state"]');
        var state = $statusSelect.val();
        var optionText = $statusSelect.find('option[value="' + state + '"]').text();

        var postData = "state=" + state + "&list-id=" + listId;

        //Liczmy detale
        var details = "";
        var $detailsRow = $('.list-details');

        $detailsRow.find('.detail-count').each(function () {
            var detailId = $(this).data('detail-id');

            if (details !== "") {
                details += ",";
            }

            details += detailId;
            postData += "&detail_" + detailId + "=" + $(this).val();
        });

        postData += "&details=" + details;

        $('#status-modal').modal('hide');
        App.blockUI();

        $.ajax({
            method: 'POST',
            url: '/engine/chart/program.php?action=3',
            data: postData
        }).done(function () {
            toastr.success('Status zmieniony!');
            //Zmiamy status zeby widzieli ladne
            $('.list-item-state[data-item-id="' + listId + '"]').html(optionText);
        }).error(function () {
            toastr.error('Wystąpił błąd!');
            $('#status-modal').modal('hide');
        }).always(function () {
            App.unblockUI();
        });
    });
</script>