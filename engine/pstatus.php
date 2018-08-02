<?php
$action = @$_GET["a"];

if ($action != null) { // Ajax
    require_once dirname(__FILE__) . '/../config.php';
    require_once dirname(__FILE__) . '/protect.php';
}

require_once dirname(__FILE__) . '/class/pstatus.php';
$p_status = new p_status();

if ($action == 1) {
    
}

function getStatusList() {
    
}
?>

<div class="row">
    <div class="col-lg-12">
        <h2 class="page-title">Statusy</h2>
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
                            <select class="bs-select form-control" multiple="multiple" name="typeFilter">
                                <?php
                                $stats = $db->query("SELECT `name` FROM `pstatus_text` WHERE `perm_manual` = '1'");
                                foreach ($stats as $value) {
                                    echo '<option>' . $value["name"] . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="row" style="margin-top: 10px; text-align: right;">
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
                                    <h3 class="list-title uppercase sbold">Lista statusów</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-list-container list-default ext-1">
                        <ul id="listContent">
                            <?php
                            echo getStatusList();
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $(".bs-select").selectpicker({iconBase: "fa", tickIcon: "fa-check"});
    });
</script>