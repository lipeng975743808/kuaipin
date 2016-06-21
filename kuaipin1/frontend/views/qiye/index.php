<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Qiye */
/* @var $form ActiveForm */
?>
<div class="qiye-index">

    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'q_name') ?>
        <?= $form->field($model, 'q_pf') ?>
    
        <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div><!-- qiye-index -->
