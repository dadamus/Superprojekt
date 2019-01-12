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
                        <?php foreach ($data['programs'] as $program): ?>
                            <li class="dd-item dd3-item" data-id="<?= $program["id"] ?>">
                                <div class="dd-handle dd3-handle"></div>
                                <div class="dd3-content">
                                    <?= $program['name'] ?>
                                    <div style="float: right; cursor: pointer;" class="bPinfo" data-extended="false">
                                        <?= $program["done_programs_quantity"] ?> / <?= $program['all_programs_quantity'] ?>
                                        <i class="fa fa-info-circle"></i>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
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

<script type="text/javascript" src="/js/operator/main.js"></script>