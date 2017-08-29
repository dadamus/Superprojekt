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
            <td><input
                        type="checkbox"
                        name="attribute[]"
                        value="<?= $key ?>"
                        style="width: 20px; height: 20px;"
                        <?php if ($data["disabled"]): ?>disabled="disabled"<?php endif; ?>
                    <?php if (isset($attribute["checked"])): ?>
                        <?php if ($attribute["checked"] == 1): ?>
                            checked="checked"
                        <?php endif ?>
                    <?php endif ?>
                /></td>
            <td><?= $attribute["name"] ?></td>
            <?php if (!isset($attribute["not-inputs"])): ?>
                <td>
                    <input
                            type="text"
                            name="a<?= $key ?>i1"
                            id="a<?= $key ?>i1"
                            class="form-control ai"
                            data-id="<?= $key ?>"
                            value="<?= $attribute["szt"] ?>"
                            <?php if ($data["disabled"]): ?>disabled="disabled"<?php endif; ?>
                    />
                </td>
                <td>
                    <input
                            type="text"
                            name="a<?= $key ?>i2"
                            id="a<?= $key ?>i2"
                            class="form-control aik"
                            data-id="<?= $key ?>"
                            value="<?= $attribute["szt"] * $data["partCount"] ?>"
                            <?php if ($data["disabled"]): ?>disabled="disabled"<?php endif; ?>
                    />
                </td>
            <?php endif; ?>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>