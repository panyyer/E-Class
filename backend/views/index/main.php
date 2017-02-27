<?php
use yii\helpers\Url;
use yii\helpers\Html;
use backend\assets\MainAsset;
use yii\bootstrap\ActiveForm;
MainAsset::register($this);
?>

<div class="container">
    <div class="row">
    <!--没有课程的时候显示  
      <div id="nothing" data-dialog="#popup-dialog" data-effect="effect-fade-scale" >
          <span id="plus1">+</span>
          <p>您还没有创建任何课程，点击这里创建一个</p>
      </div>
    -->
      <div class="popup effect-fade-scale" id="popup-dialog">
          <div class="popup-content">
                <?php $form=ActiveForm::begin()?>
                    <?=$form->field($newClass,'name')?>
                    <?=$form->field($newClass,'class_time')?>
                    <?=$form->field($newClass,'class_place')?>
                    <?=$form->field($newClass,'period')?>
                    <?=Html::submitButton('创建',['class'=>'btn btn-primary'])?>
                <?php ActiveForm::end()?>
          </div>
      </div>
    <?php foreach ($model as $v):?>
        <a href="<?=urlDecode(Url::toRoute(['student/show','id'=>$v->id]))?>">
            <div class="span3">
                <div class="widget classBox">
                    <div class="widget-content">
                        <h3><?=$v->name?></h3>
                        <p>上课时间：<?=$v->class_time?></p>
                        <p>上课地点：<?=$v->class_place?></p>
                        <p>学时：<?=$v->period?></p>
                    </div> <!-- /widget-content -->
                    <div class="mask"><span><i class="icon-pencil"></i>&nbsp;进入课程管理</span></div>
                </div> <!-- /widget -->
            </div> <!-- /span3 -->
        </a>
    <?php endforeach;?>
     <div class="span3" data-dialog="#popup-dialog" data-effect="effect-fade-scale">
        <div class="widget">
            <div class="widget-content" id="newOne">
                <span id="plus2">+</span>
                <p>点击这里创建一个新课程</p>
            </div> <!-- /widget-content -->
        </div> <!-- /widget -->
    </div> <!-- /span3 -->  
    </div> <!-- /rows -->
</div>
