<div class="row">
    <div class="col-lg-6">
        <div class="portlet box green-soft">
            <div class="portlet-title">
                <div class="caption">
                    Informacje
                </div>
                <div class="actions">
                    <a href="#" class="btn btn-default">Usuń sztukę</a>
                </div>
            </div>
            <div class="portlet-body">
                <?php
                $sheet = $data['sheetData'];
                ?>
                <table class="table table-striped">
                    <tbody>
                    <tr>
                        <td>SheetCode</td>
                        <td><?= $sheet['SheetCode'] ?></td>
                    </tr>
                    <tr>
                        <td>MaterialName</td>
                        <td><?= $sheet['MaterialName'] ?></td>
                    </tr>
                    <tr>
                        <td>Ilość</td>
                        <td><?= $sheet['QtyAvailable'] ?></td>
                    </tr>
                    <tr>
                        <td>Szerokość</td>
                        <td><?= $sheet['Width'] ?></td>
                    </tr>
                    <tr>
                        <td>Wysokość</td>
                        <td><?= $sheet['Height'] ?></td>
                    </tr>
                    <tr>
                        <td>SheetType</td>
                        <td><?= $sheet['SheetType'] ?></td>
                    </tr>
                    <tr>
                        <td>SpecialInfo</td>
                        <td><?= $sheet['SpecialInfo'] ?></td>
                    </tr>
                    <tr>
                        <td>Data utorzenia</td>
                        <td><?= $sheet['createDate'] ?></td>
                    </tr>
                    <tr>
                        <td>Data modyfikacji</td>
                        <td><?= $sheet['modifyDate'] ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-info"></i>
                    Akcje
                </div>
            </div>
            <div class="portlet-body">
                <a href="#release-modal" data-toggle="modal" class="icon-btn">
                    <i class="fa fa-share-square-o"></i>
                    <div>Wydanie</div>
                </a>
                <a href="javascript:;" class="icon-btn">
                    <i class="fa fa-globe"></i>
                    <div>Atesty</div>
                </a>
                <a href="javascript:;" class="icon-btn">
                    <i class="fa fa-file-image-o"></i>
                    <div>Zdjęcia</div>
                </a>
                <a href="javascript:;" class="icon-btn">
                    <i class="fa fa-line-chart"></i>
                    <div>Aktywność</div>
                </a>
            </div>
        </div>
    </div>
</div>

<div id="release-modal" class="modal fade modal-overflow">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4 class="modal-title">Wydanie</h4>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-lg-12">
                <form id="release-form">
                    <div class="row">
                        <div class="col-lg-12">
                            <select class="form-control">
                                <option>Przyjęcie</option>
                                <option>Wydanie zewnętrzne</option>
                                <option>Wydanie wewnętrzne</option>
                                <option>Korekta dodająca</option>
                                <option>Korekta odejmująca</option>
                                <option>Zagubiona</option>
                                <option>Złomowanie</option>
                            </select>
                        </div>
                    </div>
                    <div class="row" style="margin: 5px -15px 5px -15px">
                        <div class="col-lg-12">
                            <input type="number" class="form-control" placeholder="Ilość">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <input type="text" class="form-control" placeholder="Powód">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" data-dismiss="modal" class="btn btn-outline dark">Zamknij</button>
        <button type="button" class="btn green">Zapisz</button>
    </div>
</div>