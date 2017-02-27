<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>
<span id="assistnum">3</span>
<div class="span9">
    <h1 class="page-title">
        <i class="icon-th-large"></i>
        平时分设置与计算
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
                                <a href="<?= urlDecode(Url::toRoute(['score/index','id'=>Yii::$app->session['class_id']])); ?>">设置比例</a>
                            </li>
                            <li  class="active">
                                <a href="<?= urlDecode(Url::toRoute(['score/view','id'=>Yii::$app->session['class_id']])); ?>">查看分数</a>
                            </li>
                        </ul>
                        <div class="tab-pane" id="2">
                            <fieldset>
                                <a href="<?= urlDecode(Url::toRoute(['score/export','data'=>$data])) ?>" id="download">导出成绩到Excel</a>
                                <table class="table table-bordered table-striped">
                                    <tr>
                                        <th>#</th>
                                        <th>学生姓名</th>
                                        <th>性别</th>
                                        <th>学号</th>
                                        <th>手机</th>
                                        <th>专业</th>
                                        <th>作业成绩</th>
                                        <th>考勤成绩</th>
                                        <th>综合成绩</th>
                                    </tr>
                                    <?php  $i=0; foreach($data as $k=>$v): ?>
                                    <tr>
                                        <td><?= $i ?></td>
                                        <td><?= $v['name'] ?></td>
                                        <td><?= $v['sex'] ?></td>
                                        <td><?= $v['number'] ?></td>
                                        <td><?= $v['phone'] ?></td>
                                        <td><?= $v['major'] ?></td>
                                        <td><?= round($v['homework'],2) ?></td>
                                        <td><?= round($v['attend'],2) ?></td>
                                        <td><?= round($v['final'],2) ?></td>
                                    </tr>

                                    <?php $i++; endforeach ?>
                  <!--                   <tr>
                                        <td>1</td>
                                        <td>张三</td>
                                        <td>80</td>
                                        <td>89</td>
                                        <td>86</td>
                                    </tr>
                                    <tr>
                                        <td>1</td>
                                        <td>15636475908</td>
                                        <td>40</td>
                                        <td>89</td>
                                        <td>76</td>
                                    </tr> -->
                                </table>
                            </fieldset>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
