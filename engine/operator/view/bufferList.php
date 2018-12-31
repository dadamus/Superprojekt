<div class="row">
    <div class="col-lg-12">
        <h2 class="page-title">Panel operatora</h2>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">Bufor detali</div>
            </div>
            <div class="portlet-body">
                <table class="table table-bordered table-striped" id="nestable">
                    <thead>
                    <tr>
                        <th>Nazwa</th>
                        <th>Data</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($data['programs'] as $program): ?>
                        <tr data-id="<?= $program["id"] ?>" style="background-color: <?= ProgramColorGenerator::generate($program) ?>">
                            <td><?= $program['name'] ?></td>
                            <td><?= $program['modified_at'] ?></td>
                            <td>
                                <div style="float: right; cursor: pointer;" class="bPinfo" data-extended="true">
                                    <?= $program["done_programs_quantity"] ?> / <?= $program['all_programs_quantity'] ?> <i
                                        class="fa fa-info-circle"></i>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
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