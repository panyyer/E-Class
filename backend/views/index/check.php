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
                    <a href="<?=urlDecode(Url::toRoute(['index/list','id'=>Yii::$app->session['class_id']]))?>" class="btn back">返回</a>
                </div> <!-- /widget-header -->
                <div class="widget-content">
                    <div class="tabbable">
                        <fieldset>
                            <!-- 标签下的内容 -->

                            <div id="homeworwlist">
                                <?php
                                echo '<table class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>姓名</th>
                                <th>分数</th>
                                <th>操作</th>
                            </tr>
                            </thead>';
                                ?>
                                <tbody>
                                <?php $i=1; foreach($model as $k=>$v) :?>
                                    <tr>
                                        <td><?= $i; ?></td>
                                        <td><?= $data[$v->sid] ?></td>
                                        <td><?php if($v->score==-1)echo "未批改";else echo $v->score; ?></td>
                                        <td>
                                            <a href="<?=urlDecode(Url::toRoute(['index/check-one','id'=>Yii::$app->session['class_id'],'hid'=>$_GET['hid'],'shid'=>$v->id]))?>"><?php if($v->score==-1)echo "进入批改";else echo "查看批改"; ?></a>
                                        </td>
                                    </tr>

                                    <?php $i++; endforeach ?>

                                </tbody>
                                </table>
                            </div>

                        </fieldset>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>