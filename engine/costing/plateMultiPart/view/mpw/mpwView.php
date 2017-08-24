<div class="row">
    <div class="col-lg-12">
        <div style="text-align: center"><p>Wybranych detali: <?= count($data["details"]) ?></p>

            <div class="row" id="multiMPWVersion" style="display: none">
                <div class="col-lg-6 col-lg-offset-3">
                    <p>Problem wersji:</p>
                    <table class="table">
                        <thead>
                        <tr>
                            <th>
                                ID
                            </th>
                        </tr>
                        </thead>
                        <tbody id="multiMPWVersionWrapper">

                        </tbody>
                    </table>
                </div>
            </div>

            <p></p>
            <input type="text" hidden="hidden" id="mpw_versions" name="mpw_versions" value='<?= json_encode($data["versions"])?>'/>
            <input type="text" hidden="hidden" id="mpw_directory" name="mpw_directory"
                   value="<?= $data["directory"] ?>"/>
            <input type="text" hidden="hidden" id="mpw_project" name="mpw_project" value="<?= $data["project_id"] ?>"/>
            <input type="text" hidden="hidden" id="mpw_details" name="mpw_details"
                   value='<?= json_encode($data["details"]) ?>'/>
            <table style="margin: 0 auto; border-spacing: 2px; border-collapse: separate;">
                <tbody>
                <tr>
                    <td>Blacha</td>
                    <td>
                        <select class="form-control" name="material" id="cmaterial">
                            <?php foreach ($data["material"] as $material): ?>
                                <option value="<?= $material["id"] ?>"><?= $material["name"] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Grubość</td>
                    <td><input type="text" class="form-control" name="thickness" id="cthickness" required="required">
                    </td>
                </tr>
                <tr>
                    <td>Sztuk</td>
                    <td><input type="number" class="form-control" name="pieces" id="cpieces" required="required"></td>
                </tr>
                <tr>
                    <td>Wersja</td>
                    <td>
                        <select class="form-control" name="version" id="pversioni">
                            <option value="0">-</option>
                            <?php foreach ($data["versions"] as $v => $d): ?>
                                <option value="<?= substr($v, 1) ?>"><?= substr($v, 1) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <?php for ($i = 1; $i <= 7; $i++): ?>
                    <tr>
                        <td><?= _getChecboxText($i) ?></td>
                        <td><input type="checkbox" name="cba[]" value="<?= $i ?>" id="c<?= $i ?>" class="form-control">
                        </td>
                    </tr>
                <?php endfor; ?>
                <tr>
                    <td>Opis</td>
                    <td><textarea class="form-control" name="des" id="cdes"></textarea></td>
                </tr>
                </tbody>
            </table>
            <p></p></div>
    </div>
</div>

<div class="row">
    <div class="col-lg-3 col-lg-offset-9">
        <input type="button" class="btn btn-success" id="submitMultiMPW" value="Zapisz"/>
    </div>
</div>

<script type="text/javascript">
    mpwMultiLoad();
</script>