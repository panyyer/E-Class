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
                </div> <!-- /widget-header -->
            <div class="widget-content">
                <div class="tabbable">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href='<?= urlDecode(Url::toRoute(['index/list','id'=>Yii::$app->session['class_id']])); ?>'>作业列表</a>
                        </li>
                        <li>
                            <a href="<?= urlDecode(Url::toRoute(['index/index','id'=>Yii::$app->session['class_id']])); ?>">发布作业</a>
                        </li>
                    </ul>
                    <fieldset>
                    <!-- 标签下的内容 -->

                    <div id="homeworwlist">
                        <?php
                            echo '<table class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>标题</th>
                                <th>截止时间</th>
                                <th class="operation">操作</th>
                            </tr>
                            </thead>';
                        ?>
                        <tbody>
                        <?php $i=1; foreach($model as $k=>$v) :?>
                        <tr>
                            <td><?= $i; ?></td>
                            <td><?= $v->title ?></td>
                            <td><?= $v->deadline ?></td>
                            <td>
                                <a href="<?=urlDecode(Url::toRoute(['index/view','id'=>Yii::$app->session['class_id'],'hid'=>$v->id]))?>">查看详情</a>
                                <a href="<?=urlDecode(Url::toRoute(['index/check','id'=>Yii::$app->session['class_id'],'hid'=>$v->id]))?>">批改作业</a>
                                <a href="<?=urlDecode(Url::toRoute(['index/similar','id'=>Yii::$app->session['class_id'],'hid'=>$v->id]))?>">相似度检查</a>
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