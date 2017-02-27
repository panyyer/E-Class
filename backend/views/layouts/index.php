<?php
use yii\helpers\Url;
use yii\helpers\Html;
use backend\assets\AllAsset;
use backend\assets\CommonAsset;
use common\models\Classes;

AllAsset::register($this);
CommonAsset::register($this);
$id = Yii::$app->request->get('id',-1);
$model = Classes::find()->where('id=:id', [':id' => $id])->one();
if(!empty($model)) {
    Yii::$app->session['class_name'] = $model->name;
    Yii::$app->session['class_time'] = $model->class_time;
    Yii::$app->session['class_place'] = $model->class_place;
    Yii::$app->session['class_period'] = $model->period;
    Yii::$app->session['class_id'] = $model->id;
}
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>易课堂教学端</title>
    <link rel="icon" href="<?= IMAGE_URL ?>/logo.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container">
<!--            <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">-->
<!--                <span class="icon-bar"></span>-->
<!--                <span class="icon-bar"></span>-->
<!--                <span class="icon-bar"></span>-->
<!--            </a>-->
            <a class="brand" href="./">易课堂教学端</a>
            <div class="nav-collapse">
                <ul class="nav pull-right">
                    <li class="divider-vertical"></li>
                    <li class="dropdown">
                        <a data-toggle="dropdown" class="dropdown-toggle " href="#">
                            欢迎您！<?= \Yii::$app->session['ad_phone'] ?> <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="<?= urlDecode(Url::toRoute('index/main')); ?>"><i class="icon-user"></i> 课程列表 </a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a href="<?= urlDecode(Url::toRoute('admin/logout')); ?>"><i class="icon-off"></i> 退出 </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div> <!-- /nav-collapse -->
        </div> <!-- /container -->
    </div> <!-- /navbar-inner -->
</div> <!-- /navbar -->
<div id="content">
    <div class="container">
    <div class="row">
        <div class="span3">
            <div class="account-container">
                <div class="account-details">
                    <span class="account-name">课程名：<?=Yii::$app->session['class_name']?></span>
                    <span class="account-role">上课时间：<?=Yii::$app->session['class_time']?></span>
                    <span class="account-role">上课地点：<?=Yii::$app->session['class_place']?></span>
                    <span class="account-role">学时：<?=Yii::$app->session['class_period']?></span>
                </div> <!-- /account-details -->
            </div> <!-- /account-container -->
            <hr />
            <ul id="main-nav" class="nav nav-tabs nav-stacked">
                <li>
                    <a href="<?= urlDecode(Url::toRoute(['student/show','id'=>Yii::$app->session['class_id']])); ?>">
                        <i class="icon-user"></i>
                        学生管理
                    </a>
                </li>
                <li>
                    <a href="<?= urlDecode(Url::toRoute(['index/list','id'=>Yii::$app->session['class_id']])); ?>">
                        <i class="icon-home"></i>
                        作业发布与批改
                    </a>
                </li>
                <li>
                    <a href="<?= urlDecode(Url::toRoute(['attend/setting','id'=>Yii::$app->session['class_id']])); ?>">
                        <i class="icon-pencil"></i>
                        考勤情况
                    </a>
                </li>
                <li>
                    <a href="<?= urlDecode(Url::toRoute(['score/index','id'=>Yii::$app->session['class_id']])); ?>">
                        <i class="icon-th-large"></i>
                        平时分设置与计算
                    </a>
                </li>
                <li>
                    <a href="<?= urlDecode(Url::toRoute(['index/info','id'=>Yii::$app->session['class_id']])); ?>">
                        <i class="icon-tag"></i>
                        修改课程信息
                    </a>
                </li>
                <li>
                    <a href="<?= urlDecode(Url::toRoute('index/main')); ?>">
                        <i class="icon-step-backward"></i>
                        返回课程列表
                    </a>
                </li>
                <li>
                    <a href="<?= urlDecode(Url::toRoute('admin/logout')); ?>">
                        <i class="icon-backward"></i>
                        退出
                    </a>
                </li>
            </ul>
        </div> <!-- /span3 -->
        <?=$content?>
    </div>   
</div>

</div>
<div id="footer">
    <div class="container">
        <hr />
        <p>&copy;<?=date("Y",time());?> 易课堂.</p>
    </div> <!-- /container -->
</div>
<?php $this->endBody() ?>

</body>
</html>
<?php $this->endPage() ?>
