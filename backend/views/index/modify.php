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
                    <h3>修改</h3>
                </div> <!-- /widget-header -->
                <div class="widget-content">
                    <div class="tabbable">
                        <div class="tab-pane" id="2">
                            <fieldset>
                                <div id="modifybox">

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
                                <?=Html::submitButton('修改',['class'=>'btn btn-primary'])?>
                                <a href="<?=urlDecode(Url::toRoute(['index/list','id'=>Yii::$app->session['class_id']]))?>" class="btn btn-success">返回</a>
                                <?php ActiveForm::end() ?>

                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>