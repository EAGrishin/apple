<?php
/** @var $apple common\models\Apple */
?>

<div class="progress">
    <div class="progress-bar" role="progressbar" style="width: <?= $apple->eat_percent ?>%;"
         aria-valuenow="<?= $apple->eat_percent ?>" aria-valuemin="0"
         aria-valuemax="100"><?= $apple->eat_percent ?>%
    </div>
</div>