<?php
$dir_count = 0;
?>

<?php foreach($data as $dir): ?>
    <tr data-id="<?= $dir["id"] ?>" class="multiDirChoose">
        <td style="text-align: center; cursor: pointer;"><?= $dir["name"] ?></td>
    </tr>
    <?php
        $dir_count++;
        if ($dir_count > 5) {
            break;
        }
    ?>
<?php endforeach; ?>
