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
                    <h3>作业详情</h3>
                    <a href="<?=urlDecode(Url::toRoute(['index/list','id'=>Yii::$app->session['class_id']]))?>" class="btn back">返回</a>
                </div> <!-- /widget-header -->
                <div class="widget-content">
                    <div class="tabbable">
                        <fieldset>
                            <!-- 标签下的内容 -->
                            <div class="homeworkDetail">
                                <h2><?=$model->title?></h2>
                                <a class="btn btn-primary"href="<?=urlDecode(Url::toRoute(['index/modify','id'=>Yii::$app->session['class_id'],'hid'=>$model->id]))?>">修改</a>
                                <a class="btn btn-success"href="<?=urlDecode(Url::toRoute(['index/delete','id'=>Yii::$app->session['class_id'],'hid'=>$model->id]))?>" onclick="if(confirm('确定要删除吗？'))return true;else return false;">删除</a>
                                <div class="content">
                                    <p><b>发布时间:</b><?=$model->publish_time?></p>
                                    <p><b>截止时间:</b><?=$model->deadline?></p>
                                    <p><b>内容:</b><?=$model->content?></p>
                                    <?php if(!empty($model->fileName)):?>
                                    <p><b>附件:</b> <a href="<?= urlDecode(Url::toRoute(['index/download','hid'=>$model->id ])) ?>"><?= $model->fileName ?></a></p>
                                    <?php else:?>   
                                    <p><b>附件:无</p>
                                    <?php endif;?>
                                </div>

                            </div>


                        </fieldset>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>