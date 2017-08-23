<div class="row">
    <div class="col-lg-12">
        <h2 class="page-title">Costing - multipart </h2>
    </div>
</div>

<pre>
    <?= var_dump($data["multiPart"]) ?>
</pre>

<div class="row">
    <div class="col-lg-12">
        <?php foreach ($data["alerts"] as $alert): ?>
            <div class="alert alert-<?= $alert["type"] ?>">
                <strong>Uwaga!</strong> <?= $alert["message"] ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<div class="row">
    <?php if ($data["frameSetup"] !== false): ?>
        <?= $data["frameView"] ?>
    <?php else: ?>
        ramka jest ok
    <?php endif; ?>
</div>

<script type="text/javascript" src="/js/plateFrame/jcanvas.min.js"></script>
<script type="text/javascript" src="/js/plateFrame/plateFrame.js"></script>