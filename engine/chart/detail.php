<?php
$did = $_GET["did"];
$qdetail = $db->query("SELECT * FROM `details` WHERE `id` = '$did'");
$detail = $qdetail->fetch();
?>

<div class="row">
    <div class="col-lg-12">
        <h2 class="page-title">Karta detalu</h2>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="portlet light portlet-fit portlet-datatable bordered">
            <div class="portlet-title">
                <div class="caption">
                    <span class="caption-subject font-dark sbold uppercase">Detal: </span><?php echo $detail["src"]; ?>
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-sm-12 col-md-5">
                        <div class="portlet light bordered">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-info"></i>
                                    Akcje
                                </div>
                            </div>
                            <div class="portlet-body">
                                <a href="javascript:;" class="icon-btn">
                                    <i class="fa fa-share-square-o"></i>
                                    <div>WZ</div>
                                </a>
                                <a href="javascript:;" class="icon-btn">
                                    <i class="fa fa-globe"></i>
                                    <div>Obróbka</div>
                                </a>
                                <a href="javascript:;" class="icon-btn">
                                    <i class="fa fa-times-circle-o"></i>
                                    <div>Złom</div>
                                </a>
                                <a href="javascript:;" class="icon-btn">
                                    <i class="fa fa-question-circle"></i>
                                    <div>UFO</div>
                                </a>
                                <a href="javascript:;" class="icon-btn">
                                    <i class="fa fa-euro"></i>
                                    <div>Płatność</div>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <a href="javascript:;" class="thumbnail">
                            <?php
                            if ($detail["img"] == '') {
                                echo '<img src="holder.js/100%x180" alt="100%x180" style="height: 180px; width: 100%; display: block;">';
                            } else {
                                echo '<img src="' . $detail["img"] . '" alt="100%x180" style="height: 180px; width: 100%; display: block;">';
                            }
                            ?>
                        </a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet green-jungle box">
                            <div class="portlet-title">
                                <div class="caption">
                                    Programy
                                </div>
                            </div>
                            <div class="portlet-body">
                                <table class="table table-striped table-bordered table-advance table-hover">
                                    <thead>
                                        <tr>
                                            <th><i class="fa fa-info-circle"></i> Status</th>
                                            <th><i class="fa fa-key"></i> ID</th>
                                            <th><i class="fa fa-book"></i> Nazwa</th>
                                            <th><i class="fa fa-pie-chart"></i> Ilość</th>
                                            <th><i class="fa fa-calendar-check-o"></i> Data cięcia</th>
                                            <th><i class="fa fa-calendar-plus-o"></i> Data dodania</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $qmpw = $db->query("SELECT `id`, `program` FROM `mpw` WHERE `did` = '$did' AND `program` != ''");
                                        $programs = array();
                                        foreach ($qmpw as $mpw) {
                                            $_p = explode("|", $mpw["program"]);
                                            foreach ($_p as $pid) {
                                                if ($pid == '') {
                                                    continue;
                                                }

                                                $qprogram = $db->query("SELECT `id`, `status`, `name`, `mpw`, `date` FROM `programs` WHERE `id` = '$pid'");
                                                $program = $qprogram->fetch();

                                                $status_class = "";
                                                $status_text = "";
                                                switch ($program["status"]) {
                                                    case 0:
                                                        $status_class = "info";
                                                        $status_text = "Dodano";
                                                        break;
                                                    case 1:
                                                        $status_class = "success";
                                                        $status_text = "Wycięto";
                                                        break;
                                                    case 2:
                                                        $status_class = "success";
                                                        $status_text = "Wycięto";
                                                        break;
                                                    case 4:
                                                        $status_class = "warning";
                                                        $status_text = "Błąd";
                                                        break;
                                                    default:
                                                        $status_class = "warning";
                                                        $status_text = "Błąd";
                                                        break;
                                                }
                                                $cut_date = "-";
                                                if ($program["status"] > 0) {
                                                    $qcd = $db->query("SELECT `date` FROM `email` WHERE `pid` = '$pid' ORDER BY `id` DESC LIMIT 1");
                                                    $fcd = $qcd->fetch();
                                                    $cut_date = $fcd["date"];
                                                }

                                                $pmpw = json_decode($program["mpw"], true);
                                                //die(var_dump($pmpw));
                                                $size = $pmpw[$mpw["id"]];

                                                echo '<tr><td class="highlight"><div class="' . $status_class . '"></div><a href="javascript:;">' . $status_text . '</a></td><td>' . $program["id"] . '</td><td>' . $program["name"] . '</td><td>' . $size . '</td><td>' . $cut_date . '</td><td>' . $program["date"] . '</td></tr>';
                                            }
                                        }
                                        ?>
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