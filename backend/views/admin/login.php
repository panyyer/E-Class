<?php
use yii\widgets\ActiveForm;
use yii\captcha\Captcha;
?>

<div id="login-container">
    <div id="login-header">
        <h3>登录</h3>
    </div> <!-- /login-header -->
    <div id="login-content" class="clearfix">
        <?php $form=ActiveForm::begin()?>
        <fieldset>
            <?= $form->field($loginForm,'phone') ?>
            <?= $form->field($loginForm,'password')->passwordInput() ?>
            <?= $form->field($loginForm, 'verifyCode',['options'=>['class' => 'form-group']])->widget(Captcha::className(),[
                'imageOptions' => ['alt'=>'点击换图','title'=>'点击换图','style'=>'cursor:pointer;float:left;'],
                'options' => [
                    'placeholder' => '验证码',
                    'class' => 'form-control',
                    'style' => 'position:relative;left:20px;top:5px;width:50%;'
                ]
            ])->label(false);
            ?>
        </fieldset>
        <div class="pull-right">
            <button type="submit" class="btn btn-warning btn-large">
                登录
            </button>
        </div>
        <?php ActiveForm::end(); ?>
    </div> <!-- /login-content -->
</div> <!-- /login-wrapper -->

