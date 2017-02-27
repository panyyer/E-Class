<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

?>
<span id="assistnum">1</span>
<div class="span9">
    <h1 class="page-title">
        <i class="icon-user"></i>
        作业发布与批改
    </h1>

    <div class="row">
        <div class="span9">
            <div class="widget">
                <div class="widget-header">
                    <h3>批改作业</h3>
                    <a href="<?=urlDecode(Url::toRoute(['index/check','id'=>Yii::$app->session['class_id'],'hid'=>$_GET['hid']]))?>" class="btn back">返回</a>
                </div> <!-- /widget-header -->
                <div class="widget-content">
                    <div class="tabbable">
                        <fieldset>
                            <div class="homeworkStu">
                                <p class="homeworkLabel"><i class="icon-upload-alt"></i>学生</p>
                                <p>
                                    <b>姓名：</b>
                                    <?php if($student->name)echo $student->name;else echo $student->phone?>
                                    
                                </p>
                                <p>
                                    <b>作业附件：</b>
                                    <a href="<?= urlDecode(Url::toRoute(['index/download','shid'=>$one->id ])) ?>"><?= $one->fileName ?></a>
                                </p>
                                <p>
                                    <b>提交时间：</b>
                                    <?=$one->submit_time?>
                                </p>    
                            </div>
                            <div class="homeworkTea">
                                <p class="homeworkLabel"><i class="icon-check"></i>老师</p>
                                <?php $form=ActiveForm::begin()?>
                                    <div class="form-group">
                                        <label class="control-label">分数</label>
                                        <?= $form->field($one,'score')->label(false) ?>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">评语</label>
                                        <?=$form->field($one,'message')->textarea()->label(false) ?>
                                    </div> 
                                    <input type="submit" class="btn btn-primary" name="submitButton" value="确定">
                                <?php ActiveForm::end()?>  

                            </div>
                        </fieldset>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>