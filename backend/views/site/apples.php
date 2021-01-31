<?php
/** @var $apples common\models\Apple */
use common\models\Apple;

?>

<div id="apples" class="card-deck mb-4 text-center">
    <?php foreach ($apples as $apple): ?>
        <div data-id="<?=$apple->id?>" class="apple-card card mb-4 shadow-sm">
            <div class="card-header">
                <div class="apple-name"><b>Яблоко</b></div>
                <span class="circle" style="color: <?= '#' . $apple->color ?>"></span>
            </div>
            <div class="card-body">
                <h4 class="card-title pricing-card-title apple-state"><?=Apple::$state_apple[$apple->state]?></h4>
                Съедено:
                <?= $this->render('_progress', [
                    'apple' => $apple
                ]); ?>
                <br>
                <button type="button" class="btn btn-lg btn-block btn-success fall-apple">Падает на землю</button>
                Съесть:
                <div class="btn-group btn-group-toggle">
                    <button data-percent="25" type="button" class="btn btn-primary btn-sm eat-apple">25%</button>
                    <button data-percent="50" type="button" class="btn btn-secondary btn-sm eat-apple">50%</button>
                    <button data-percent="100" type="button" class="btn btn-info btn-sm eat-apple">100%</button>
                </div>
            </div>
        </div>
    <?php endforeach;?>
</div>
