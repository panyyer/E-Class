<?php
use yii\helpers\Url;
use yii\helpers\Html;
use backend\assets\AllAsset;
use backend\assets\CommonAsset;
AllAsset::register($this);
CommonAsset::register($this);
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
    <?= $content ?>
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


