<?php

/* @var $this yii\web\View */

/* @var $apples array */

use common\models\Apple;
use yii\bootstrap4\Html;

$this->title = 'Яблоки';
?>
<div class="site-index">

    <div class="jumbotron">
        <p>
            <?= Html::a("Сгенерировать яблоки", ['site/create-apple'], ['class' => 'btn btn-lg btn-success create-apple']) ?>
        </p>
    </div>

    <div class="container">
        <?= $this->render('apples', [
            'apples' => $apples
        ]); ?>
    </div>
</div>
