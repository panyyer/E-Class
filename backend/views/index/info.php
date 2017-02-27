<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\datetime\DateTimePicker;
?>
<span id="assistnum">4</span>
<div class="span9">
    <h1 class="page-title">
        <i class="icon-th-large"></i>
        修改课程信息
    </h1>

    <div class="row">
        <div class="span9">
            <div class="widget">
                <div class="widget-header">
                </div> <!-- /widget-header -->
                <div class="widget-content">
                    <div class="tabbable">
                        <div class="tab-pane" id="2">
                            <fieldset>
                                <?php
                                //修改成功
                                if( \Yii::$app->getSession()->hasFlash('succeedC') ) {
                                    echo "<div class='alert alert-success'role='alert'>".
                                        \Yii::$app->getSession()->getFlash('succeedC')."</div>";
                                }
                                ?>
                                <?php
                                //删除失败
                                if( \Yii::$app->getSession()->hasFlash('failC') ) {
                                    echo "<div class='alert alert-danger'role='alert'>".
                                        \Yii::$app->getSession()->getFlash('failC')."</div>";
                                }
                                ?>
                                <div id="modifybox">

                                    <?php $form=ActiveForm::begin()?>
                                    <?=$form->field($model,'name')?>
                                    <?=$form->field($model,'class_time')?>
                                    <?=$form->field($model,'class_place')?>
                                    <?=$form->field($model,'period')?>
                                    <?=$form->field($model,'code')->textInput(['disabled'=>true])?>
                                    <?=Html::submitButton('修改信息',['class'=>'btn btn-primary'])?>
                                    <a class="btn btn-danger" onclick="if(confirm('确定要删除课程？'))return true;else return false;" href="<?=urlDecode(Url::toRoute(['index/delete-class','id'=>Yii::$app->session['class_id']]))?>">删除课程</a>

                                    <?php ActiveForm::end()?>

                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>