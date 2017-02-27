<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

?>
<span id="assistnum">0</span>
<div class="span9">
    <h1 class="page-title">
        <i class="icon-user"></i>
        学生管理
    </h1>

    <div class="row">
        <div class="span9">
            <div class="widget">
                <div class="widget-header">
                </div> <!-- /widget-header -->
            <div class="widget-content">
                <div class="tabbable">
                    <fieldset>
                    <!-- 标签下的内容 -->
                    <?php echo Html::beginForm();?>
                    <span style="float:left">
                    <input type="text" placeholder="姓名" value="" name="keywords">
                    </span>
                    <span style="float:left">&nbsp;
                    <button type="submit" style="height: 28px;width: 55px;vertical-align:middle" class="btn">检索</button>
                    </span>
                    <?php echo Html::endForm();?>
                    <table>
                        <?php
                            echo '<table class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>姓名</th>
                                <th>电话</th>
                                <th>性别</th>
                                <th>学号</th>
                                <th>专业</th>
                                <th>系</th>
                                <th>学院</th>
                                <th>学校</th>
                                <th>省份</th>
                            </tr>
                            </thead>';
                        ?>
                        <tbody>
                        <?php $i=1; foreach($model as $k=>$v) :?>
                        <tr>
                            <td><?= $i; ?></td>
                            <td><?= $v['name'] ?></td>
                            <td><?= $v['phone'] ?></td>
                            <td><?= $v['sex'] ?></td>
                            <td><?= $v['number'] ?></td>
                            <td><?= $v['major'] ?></td>
                            <td><?= $v['department'] ?></td>
                            <td><?= $v['academy'] ?></td>
                            <td><?= $v['school'] ?></td>
                            <td><?= $v['province'] ?></td>
                        </tr>

                        <?php $i++; endforeach ?>

                        </tbody>
                        </table>

                    </fieldset>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>