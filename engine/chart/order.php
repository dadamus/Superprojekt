<?php
$oid = $_GET["oid"];
$orderQuery = $db->prepare("
  SELECT 
  o.*,
  c.name as client_name,
  c.nip as client_nip,
  c.address as client_address,
  c.email as client_email,
  c.phone as client_phone
  FROM
  `order` o 
  LEFT JOIN clients c ON c.id = o.cid
  WHERE 
  o.id = :oid
");
$orderQuery->bindValue(":oid", $oid, PDO::PARAM_INT);
$orderQuery->execute();

$order = $orderQuery->fetch();

$status = "Brak danych";

$status = getOrderStatus($order["status"])
?>

<div class="row">
    <div class="col-lg-12">
        <h2 class="page-title">Podgląd zamówienia</h2>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="portlet light portlet-fit portlet-datatable bordered">
            <div class="portlet-title">
                <div class="caption">
                    <span class="caption-subject font-dark sbold uppercase">Zamówienie numer: #<?= $order["id"]; ?></span>
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <div class="portlet blue-hoki box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-info"></i>
                                    Szczegóły zamówienia
                                </div>
                            </div>
                            <div class="portlet-body" id="order-details">
                                <div class="row static-info">
                                    <div class="col-md-5 name">
                                        Kod:
                                    </div>
                                    <div class="col-md-7 value">
                                        <?= $order["on"]; ?>
                                    </div>
                                </div>
                                <div class="row static-info">
                                    <div class="col-md-5 name">
                                        Data utworzenia:
                                    </div>
                                    <div class="col-md-7 value">
                                        <?= $order["date"]; ?>
                                    </div>
                                </div>
                                <div class="row static-info">
                                    <div class="col-md-5 name">
                                        Status:
                                    </div>
                                    <div class="col-md-7 value status_change">
                                        <span class="label label-sm <?= $status["color"]; ?>"
                                              style="cursor: pointer;"><?= $status["text"]; ?></span>
                                    </div>
                                </div>
                                <div class="row static-info">
                                    <div class="col-md-5 name">
                                        Opis:
                                    </div>
                                    <div class="col-md-7 value">
                                        <?= $order["des"]; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <div class="portlet blue-chambray box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-user"></i>
                                    Klient
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div class="row static-info">
                                    <div class="col-md-5 name">
                                        Nazwa:
                                    </div>
                                    <div class="col-md-7 value">
                                        <?= $order["client_name"]; ?>
                                    </div>
                                </div>
                                <div class="row static-info">
                                    <div class="col-md-5 name">
                                        NIP:
                                    </div>
                                    <div class="col-md-7 value">
                                        <?= $order["client_nip"]; ?>
                                    </div>
                                </div>
                                <div class="row static-info">
                                    <div class="col-md-5 name">
                                        Adres:
                                    </div>
                                    <div class="col-md-7 value">
                                        <?= $order["client_address"]; ?>
                                    </div>
                                </div>
                                <div class="row static-info">
                                    <div class="col-md-5 name">
                                        Email:
                                    </div>
                                    <div class="col-md-7 value">
                                        <i class="fa fa-phone-square"></i> <?= $order["client_email"]; ?>
                                    </div>
                                </div>
                                <div class="row static-info">
                                    <div class="col-md-5 name">
                                        Telefon:
                                    </div>
                                    <div class="col-md-7 value">
                                        <i class="fa fa-envelope"></i> <?= $order["client_phone"]; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet green-haze box">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-list"></i>
                                    Lista detali
                                </div>
                                <div class="actions">
                                    <a href="/wz/create/<?= $order['id'] ?>/"
                                       class="btn btn-default" style="margin-right: 10px;">
                                        Generuj WZ <i class="fa fa-globe"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="portlet-body">
                                <?php
                                $odq = $db->prepare("
                                  SELECT 
                                  oi.id as oi_id, 
                                  oi.code, 
                                  oi.dct, 
                                  oi.stored,
                                  oi.mpw,
                                  mpw.did,
                                  mpw.pieces,
                                  mpw.program,
                                  mpw.type,
                                  details.src as detailName
                                  FROM 
                                  oitems oi
                                  LEFT JOIN mpw mpw ON mpw.id = oi.mpw
                                  LEFT JOIN details ON details.id = oi.did
                                  WHERE 
                                  oi.oid = :oid
                                ");
                                $odq->bindValue(":oid", $oid, PDO::PARAM_INT);
                                $odq->execute();
                                ?>
                                <?php foreach ($odq->fetchAll(PDO::FETCH_ASSOC) as $oitem): ?>
                                    <?php
                                    $mpw_id = $oitem["mpw"];
                                    $qmpc = $db->query("SELECT `mtype`, `atributes`, `d_qty`, `thickness` FROM `mpc` WHERE `wid` = '$mpw_id'");
                                    $mpc = $qmpc->fetch();
                                    $did = $oitem["did"];
                                    if ($oitem["type"] >= OT::AUTO_WYCENA_BLACH_MULTI_ZATWIERDZONA && $oitem["type"] <= OT::AUTO_WYCENA_BLACH_MULTI_DODANE_DO_ZAMOWIENIA) {
                                        $qmpc = $db->query("
                                          SELECT m.name as mtype, mpw.`attributes`, mpw.`pieces` as d_qty, mpw.`thickness` 
                                          FROM `mpw` mpw 
                                          LEFT JOIN material m ON m.id = mpw.material
                                          WHERE mpw.`id` = '$mpw_id'
                                        ");
                                        $mpc = $qmpc->fetch();
                                    }
                                    ?>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="portlet yellow-gold box">
                                                <div class="portlet-title">
                                                    <div class="caption"><?= $oitem["code"] ?> - [<?= $oitem['detailName'] ?>]</div>
                                                    <div class="actions">
                                                        <a href="<?= $site_path ?>/detail/<?= $did ?>/"
                                                           class="btn btn-default" style="margin-right: 10px;">
                                                            Karta detalu <i class="fa fa-mail-forward"></i>
                                                        </a>
                                                        <a href="javascript:;" class="btn btn-default OrDB"
                                                           id="<?= $oitem["oi_id"] ?>_odba">
                                                        Usuń <i class="fa fa-trash"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="portlet-body">
                                                    <div class="col-md-6">
                                                        <div class="row static-info">
                                                            <div class="col-md-5 name">Rodzaj blachy:</div>
                                                            <div class="col-md-7 value"><?= $mpc["mtype"] ?></div>
                                                        </div>
                                                        <div class="row static-info">
                                                            <div class="col-md-5 name">Grubość:</div>
                                                            <div class="col-md-7 value"><?= $mpc["thickness"] ?></div>
                                                        </div>
                                                        <div class="row static-info">
                                                            <div class="col-md-5 name">Parametry:</div>
                                                            <div class="col-md-7 value">
                                                                <?php //Checkboxy
                                                                if (isset($mpc["attributes"])) {
                                                                    $attributes = json_decode($mpc["attributes"]);
                                                                    if (count($attributes) > 0) {
                                                                        foreach ($attributes as $attribute) {
                                                                            echo _getChecboxText($attribute) . " ";
                                                                        }
                                                                    }
                                                                }
                                                                ?>
                                                            </div>
                                                        </div>
                                                        <div class="row static-info">
                                                            <div class="col-md-5 name">Ilość sztuk:</div>
                                                            <div class="col-md-7 value"><?= $mpc["d_qty"] ?></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <?php
                                                        //production info
                                                        $pcr = 0;
                                                        $warehousePcr = 0;

                                                        $cprogram = $oitem["program"];
                                                        $programs = explode("|", $cprogram);

                                                        $pval = array();

                                                        $programsQuery = $db->prepare('
                                                          SELECT
                                                          d.quantity,
                                                          q.sheet_count,
                                                          q.sheet_name,
                                                          q.created_at,
                                                          p.status
                                                          FROM cutting_queue_details d
                                                          LEFT JOIN cutting_queue_list l ON l.id = cutting_queue_list_id
                                                          LEFT JOIN cutting_queue q ON q.id = l.cutting_queue_id
                                                          LEFT JOIN programs p ON p.new_cutting_queue_id = q.id
                                                          WHERE
                                                          d.oitem_id = :oitemId
                                                        ');
                                                        $programsQuery->bindValue(':oitemId', $oitem['oi_id'], PDO::PARAM_INT);
                                                        $programsQuery->execute();

                                                        $programs = [];

                                                        while ($program = $programsQuery->fetch()) {
                                                            if ($program['status'] !== ProgramStatus::ANULOWANY) {
                                                                $pcr += $program["quantity"] * $program["sheet_count"];
                                                            }
                                                            $warehousePcr += $program["quantity"] * $program["sheet_count"];
                                                            $programs[] = $program;
                                                        }

                                                        $descStyle = 'style="top: 0px; left: 40%; position:absolute;"';
                                                        $pcr_des1 = '<span ' . $descStyle . '> ' . $pcr . '/' . $oitem["pieces"] . '</span>';

                                                        $bar1_size = $pcr * 100 / $oitem["pieces"];
                                                        $bar12_size = 0;
                                                        $active1 = "active";
                                                        if ($bar1_size >= 100) {
                                                            $bar1_size = $oitem["pieces"] * 100 / $pcr;
                                                            $bar12_size = 100 - $bar1_size;
                                                            $active1 = "";
                                                        }

                                                        $dct_des2 = '<span ' . $descStyle . '>' . $oitem["dct"] . '/' . $pcr . '</span>';

                                                        $bar2_size = 0;
                                                        if ($warehousePcr > 0) {
                                                            $bar2_size = $oitem["dct"] * 100 / $warehousePcr;
                                                        }

                                                        $bar22_size = 0;
                                                        $active2 = "active";
                                                        if ($bar2_size >= 100) {
                                                            $bar2_size = $warehousePcr * 100 / $oitem["dct"];
                                                            $bar22_size = 100 - $bar2_size;
                                                            $active2 = "";
                                                        }

                                                        $bar3_max = $oitem["pieces"] - $oitem["dct"] + $oitem["stored"];
                                                        $bar3_size = $oitem["stored"] * 100 / $bar3_max;
                                                        $bar32_size = 0;
                                                        $active3 = "active";

                                                        $str_des3 = '<span ' . $descStyle . '>' . $oitem["stored"] . '/' . $bar3_max . '</span>';
                                                        ?>
                                                        <div class="row static-info">
                                                            <div class="col-md-2 name">Program:</div>
                                                            <div class="col-md-10 value">
                                                                <?= $pcr_des1 ?>
                                                                <div class="progress progress-striped <?= $active1 ?>"
                                                                     style="height: 20px;">
                                                                    <div class="progress-bar progress-bar-success"
                                                                         role="progressbar"
                                                                         style="width: <?= $bar1_size ?>%"></div>
                                                                    <div class="progress-bar progress-bar-warning"
                                                                         role="progressbar"
                                                                         style="width: <?= $bar12_size ?>%"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row static-info">
                                                            <div class="col-md-2 name">Produkcja:</div>
                                                            <div class="col-md-10 value"><?= $dct_des2 ?>
                                                                <div class="progress progress-striped <?= $active2 ?>"
                                                                     style="height: 20px;">
                                                                    <div class="progress-bar progress-bar-success"
                                                                         role="progressbar"
                                                                         style="width: <?= $bar2_size ?>%"></div>
                                                                    <div class="progress-bar progress-bar-warning"
                                                                         role="progressbar"
                                                                         style="width: <?= $bar22_size ?>%"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row static-info">
                                                            <div class="col-md-2 name">Magazyn:</div>
                                                            <div class="col-md-10 value"><?= $str_des3 ?>
                                                                <div class="progress progress-striped <?= $active3 ?>"
                                                                     style="height: 20px;">
                                                                    <div class="progress-bar progress-bar-success"
                                                                         role="progressbar"
                                                                         style="width: <?= $bar3_size ?>%"></div>
                                                                    <div class="progress-bar progress-bar-warning"
                                                                         role="progressbar"
                                                                         style="width: <?= $bar32_size ?>%"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div style="clear: both;"></div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="portlet">
                                                                <div class="portlet-body">
                                                                    <div class="note note-info">
                                                                        <h4 class="block">Programy</h4>
                                                                        <p>
                                                                        <table class="table table-hover programList">
                                                                            <thead>
                                                                            <tr>
                                                                                <th style="width: 10%;">Nazwa
                                                                                </th>
                                                                                <th style="width: 25%;">Data dodania
                                                                                </th>
                                                                                <th style="width: 30%;">
                                                                                    Ilość sztuk detalu
                                                                                </th>
                                                                                <th style="width: 25%;">
                                                                                    Sztuk programu
                                                                                </th>
                                                                                <th>
                                                                                    Status
                                                                                </th>
                                                                                <th style="width: 10%;"></th>
                                                                            </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                            <?php foreach ($programs as $program): ?>
                                                                            <?php var_dump($program)?>
                                                                                <tr>
                                                                                    <td>
                                                                                        <?= $program['sheet_name'] ?>
                                                                                    </td>
                                                                                    <td>
                                                                                        <?= $program['created_at'] ?>
                                                                                    </td>
                                                                                    <td>
                                                                                        <?= $program['quantity'] ?>
                                                                                    </td>
                                                                                    <td>
                                                                                        <?= $program['sheet_count'] ?>
                                                                                    </td>

                                                                                    <?php
                                                                                    $status = 'Brak';
                                                                                    switch ($program['status']) {
                                                                                        case ProgramStatus::ZAPROGRAMOWANY:
                                                                                            $status = 'Zaprogramowany';
                                                                                            break;
                                                                                        case ProgramStatus::DO_POTWIERDZENIA:
                                                                                            $status = 'Do potwierdzenia';
                                                                                            break;
                                                                                        case ProgramStatus::WYCIETY:
                                                                                            $status = 'Wycięty';
                                                                                            break;
                                                                                        case ProgramStatus::ANULOWANY:
                                                                                            $status = 'Anulowany';
                                                                                            break;
                                                                                    }
                                                                                    ?>

                                                                                    <td><span class="badge badge-info"><?= $status ?></span>
                                                                                    </td>
                                                                                    <td id="" class="dpb"
                                                                                        style="text-align: center; cursor: pointer;">
                                                                                        Usuń <i class="fa fa-trash"></i>
                                                                                    </td>
                                                                                </tr>
                                                                            <?php endforeach; ?>
                                                                            </tbody>
                                                                        </table>
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var oid = <?php echo $order["id"]; ?>;
    var status = <?php echo $order["status"]; ?>

    var temp_multi = "";
    var temp_multi_id = null;

    function blockSite() {
        App.blockUI({boxed: !0});
    }

    function unblockSite() {
        App.unblockUI();
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

        $("#order-details").on("click", ".status_change", function () {
            var item = this;
            blockSite();
            $(item).removeClass("status_change");
            $(item).addClass("status_change_t");
            $.ajax({
                url: "<?php echo $site_path; ?>/engine/chart/orderAjax.php?action=1&ds=" + status + "&oid=" + oid
            }).done(function (msg) {
                $(item).html(msg);
                unblockSite();
            });
        });
        $("#order-details").on("click", "#statusClose", function () {
            blockSite();
            var item = $(".status_change_t");
            $(item).removeClass("status_change_t");
            $(item).addClass("status_change");
            $.ajax({
                url: "<?php echo $site_path; ?>/engine/chart/orderAjax.php?status=" + status + "&action=2"
            }).done(function (msg) {
                $(item).html(msg);
                unblockSite();
            });
        });
        $("#order-details").on("change", "#scs", function () {
            var s = $(this).val();
            status = s;
            blockSite();
            var item = $(".status_change_t");
            $.ajax({
                url: "<?php echo $site_path; ?>/engine/chart/orderAjax.php?oid=<?php echo $order["id"]; ?>&action=3&status=" + s
            }).done(function (msg) {
                $(item).removeClass("status_change_t");
                $(item).addClass("status_change");
                $(item).html(msg);
                unblockSite();
            });
        });
        $(".dpb").on("click", function () { // Delete program
            if (confirm("Całkowicie usunąć program?")) {
                var dpid = parseInt($(this).attr("id"));
                window.location.href = "<?php echo $site_path; ?>/engine/chart/orderAjax.php?action=4&pid=" + dpid + "&hb=" + oid;
            }
        });
        $(".programList").on("click", ".epb", function () { // Change multiplier GET FORM
            blockSite();
            if (temp_multi_id != null) {
                $(temp_multi_id).html(temp_multi);
                $(temp_multi_id).addClass("epb");

                temp_multi_id = null;
                temp_multi = "";
            }
            var epid = parseInt($(this).attr("id"));
            var box = this;
            temp_multi = $(this).html();
            $(this).removeClass("epb");
            temp_multi_id = this;
            $.ajax({
                url: "<?php echo $site_path; ?>/engine/chart/orderAjax.php?action=5&pid=" + epid
            }).done(function (form) {
                $(box).html(form);
                unblockSite();
            });
        });
        $(".programList").on("mouseup", "#multiClose", function () {
            if (temp_multi_id != null) {
                $(temp_multi_id).html(temp_multi);
                $(temp_multi_id).addClass("epb");

                temp_multi_id = null;
                temp_multi = "";
            }
        });
        $(".programList").on("mouseup", "#multiSave", function () {
            var epid = parseInt($(temp_multi_id).attr("id"));
            var nv = $("#multiVal").val();
            window.location.href = "<?php echo $site_path; ?>/engine/chart/orderAjax.php?action=6&pid=" + epid + "&val=" + nv + "&oid=" + oid;
        });
        $(".OrDB").on("click", function () {
            var _id = parseInt($(this).attr("id"));
            if (confirm("Usunać detal z zamówienia?")) {
                blockSite();
                $.ajax({
                    url: "<?php echo $site_path; ?>/engine/chart/orderAjax.php?action=7&oitemId=" + _id
                }).done(function (msg) {
                    unblockSite();
                    if (msg != "1") {
                        toastr.error(msg, "Błąd!");
                    } else {
                        location.reload();
                    }
                });
            }
        });
    });
</script>