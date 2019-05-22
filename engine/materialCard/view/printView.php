<div style="width: 100%;height:100%;position:absolute;top:0px;bottom:0px;margin: auto;margin-top: 0px !important;">
    <table style="height: 100%; width: 100%; font-size: 2ex;">
        <tr style="height: 60%">
            <td>
                <img style="height: 100%;" src="/engine/materialCard.php?sheet_code=<?= $data['sheet_code'] ?>&action=image"/>
            </td>
            <td style="text-align: center;">
                <b><?= $data['material'] ?> - </b> <?= substr($data['sheet_code'], 0, 30) ?>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <div style="width: 100%; height: 100%; margin-top: 2px; border-top: black dashed 2px;"></div>
            </td>
        </tr>
    </table>
</div>

<style>
    body {
        margin: 0;
        padding: 0;
    }

    @page {
        size: landscape;
        margin: 0; /* change the margins as you want them to be. */
    }
</style>

<script>window.print()</script>