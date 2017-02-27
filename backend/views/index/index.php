<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\datetime\DateTimePicker;
?>
<span id="assistnum">1</span>
<div class="span9">
    <h1 class="page-title">
        <i class="icon-th-large"></i>
        作业发布与批改
    </h1>

    <div class="row">
        <div class="span9">
            <div class="widget">
                <div class="widget-header">
                </div> <!-- /widget-header -->
                <div class="widget-content">
                    <div class="tabbable">
                        <ul class="nav nav-tabs">
                            <li>
                                <a href='<?= urlDecode(Url::toRoute(['index/list','id'=>Yii::$app->session['class_id']])); ?>'>作业列表</a>
                            </li>
                            <li class="active">
                                <a href="<?= urlDecode(Url::toRoute(['index/index','id'=>Yii::$app->session['class_id']])); ?>">发布作业</a>
                            </li>
                        </ul>
                        <div class="tab-pane" id="2">
                            <fieldset>
                                <?php
                                if( \Yii::$app->getSession()->hasFlash('succeedH') ) {
                                    echo "<div class='alert alert-success'role='alert'>".
                                        \Yii::$app->getSession()->getFlash('succeedH')."</div>";
                                }
                                ?>
                                <?php $form = ActiveForm::begin([
                                'options' => ['enctype'=>'multipart/form-data']]); ?>
                                    <?=$form->field($homework,'title')?>
                                    <?=$form->field($homework,'content')->textarea()?>
                                    <?= $form->field($homework, 'deadline')->widget(DateTimePicker::classname(), [
                                        'options' => ['placeholder' => ''],
                                        'pluginOptions' => [

                                        ]
                                    ]);
                                    ?>
                                   <?= $form->field($homework,'fileName')->fileInput() ?>
                                   <?=Html::submitButton('发布',['class'=>'btn btn-primary'])?>
                                   <?=Html::resetButton('重置',['class'=>'btn btn-success'])?>
                                <?php ActiveForm::end() ?>

                            </fieldset>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>