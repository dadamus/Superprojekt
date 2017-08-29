<?php
$dir_count = 0;
?>

<?php foreach ($data as $dir): ?>
    <tr data-id="<?= $dir["id"] ?>" class="<?php if ($dir["blocked"] < 1): ?>multiDirChoose<?php endif ?>">
        <td style="text-align: center; cursor: pointer;">
            <?php if ($dir["blocked"] == 1): ?><strike><?php endif; ?><?= $dir["name"] ?><?php if ($dir["blocked"] == 1): ?></strike><?php endif; ?>
        </td>
    </tr>
    <?php
    $dir_count++;
    if ($dir_count > 5) {
        break;
    }
    ?>
<?php endforeach; ?>
