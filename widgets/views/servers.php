<?php
/** @var array $servers */
?>
<div class="card">
    <div class="card-body">
        <?php foreach ($servers as $server) : ?>
            <p class="mb-2">
                <h5 class="card-title"><?= $server['hostname'] ?></h5>
                <h6 class="card-subtitle mb-2 text-muted"><?= $server['ip'] ?></h6>
                <div class="progress" style="height: 25px; position: relative;">
                    <span style="position: absolute; line-height: 25px; width: 100%; text-align: center;">
                        <?= $server['online'] ?>
                    </span>
                <div class="progress-bar progress-bar-striped <?php
                if ($server['onlinePercents'] <= 50) {
                    echo 'bg-success';
                } elseif ($server['onlinePercents'] > 50 && $server['onlinePercents'] < 90) {
                    echo 'bg-warning';
                } else {
                    echo 'bg-danger';
                }
                ?>"
                     role="progressbar"
                     style="width: <?= $server['onlinePercents'] ?>%"
                     aria-valuenow="<?= $server['onlinePercents'] ?>"
                     aria-valuemin="0"
                     aria-valuemax="100">
                </div>
            </div>
            </p>
        <?php endforeach; ?>
    </div>
</div>