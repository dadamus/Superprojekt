<table class="table">
    <thead>
    <tr>
        <th></th>
        <th>Nazwa</th>
        <th>Sztuka</th>
        <th>Komplet</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($data["attributes"] as $key => $attribute): ?>
        <tr>
            <td><input type="checkbox" name="attribute[]" value="<?= $key ?>" style="width: 20px; height: 20px;"/></td>
            <td><?= $attribute["name"] ?></td>
            <?php if (!isset($attribute["not-inputs"])): ?>
                <td><input type="text" name="a<?= $key ?>i1" id="a<?= $key ?>i1" class="form-control ai"/></td>
                <td><input type="text" name="a<?= $key ?>i2" id="a<?= $key ?>i2" class="form-control aik"/></td>
            <?php endif; ?>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>