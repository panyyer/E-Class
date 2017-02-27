<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use backend\assets\AttendAsset;
AttendAsset::register($this);
?>
<span id="assistnum">2</span>
<span id="triangle"></span>

<div class="span9">
    <h1 class="page-title">
        <i class="icon-th-large"></i>
        考勤情况
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
                                <a href='<?= urlDecode(Url::toRoute(['attend/setting','id'=>Yii::$app->session['class_id']])); ?>'>签到设置</a>
                            </li>
                            <li class="active">
                                <a href='<?= urlDecode(Url::toRoute(['attend/index','id'=>Yii::$app->session['class_id']])); ?>'>签到情况</a>
                            </li>
                        </ul>
                        <div class="tab-pane" id="2">
                            <fieldset>
                                <div id="attendTips">
                                    <span class="notblock"></span>未抽到
                                    <span class="lateblock"></span>迟到
                                    <span class="absentblock"></span>缺席
                                    <span class="askleaveblock"></span>请假
                                    <span class="okblock"></span>成功
                                    <div class="alert alert-danger" role="alert" id="alert"></div>
                                </div>
                                <div class="table-responsive" id="attendBox">
                                    <table class="table table-bordered">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>学号</th>
                                            <th>姓名</th>
                                            <th class="chosen">1</th>
                                            <th>2</th>
                                            <th>3</th>
                                            <th>4</th>
                                            <th>5</th>
                                            <th>6</th>
                                            <th>7</th>
                                            <th>8</th>
                                            <th>统计</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        <?php
                                            $k = 0;
                                            if($student != null) {
                                                foreach ($student as $value) {
                                                    $k++;
                                                    ?>
                                                    <tr>
                                                        <td><?= $k ?></td>
                                                        <td><?= $value['number'] ?></td>
                                                        <td><?= $value['name'] ?></td>
                                                        <?php for($i=0;$i<$sign_num;$i++) {?>
                                                            <td class="<?=$value[$i] ?>"></td>
                                                        <?php } ?>
                                                        <td class="count">抽到<?=$value['count'] ?>次，成功<?=$value['ok'] ?>，迟到<?=$value['late'] ?>，旷课<?=$value['absent'] ?>，请假0</td>
                                                    </tr>

                                        <?php
                                                }
                                            }
                                        ?>
                                        <tr id="endrow">
                                            <td>#</td>
                                            <td>#</td>
                                            <td>#</td>

                                            <?php
                                            if($count != null) {
                                                foreach ($count as $value2) {
                                                    echo '<td>' . $value2['time'] . '：抽到' . $value2['count'] . '人，成功' . $value2['ok'] . '，迟到' . $value2['late'] . '，旷课' . $value2['absent'] . '，请假0</td>';
                                                }
                                            }
                                            ?>

                                        </tr>
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
</div>