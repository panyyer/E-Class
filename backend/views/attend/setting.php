<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use backend\assets\AttendAsset;
?>
<span id="assistnum">2</span>
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
                            <li class="active">
                                <a href='<?= urlDecode(Url::toRoute(['attend/setting','id'=>Yii::$app->session['class_id']])); ?>'>签到设置</a>
                            </li>
                            <li>
                                <a href='<?= urlDecode(Url::toRoute(['attend/index','id'=>Yii::$app->session['class_id']])); ?>'>签到情况</a>
                            </li>
                        </ul>
                        <div class="tab-pane" id="2">
                            <fieldset>
                                <div id="attendSetting">
                                    <div class="alert alert-warning">
                                        提示：一个课程只能设置一次签到方式，设置后将不能修改！
                                    </div>
                                    <form action="">
                                        <div class="form-group">
                                            <label class="control-label">签到方式</label>
                                            <div class="radio">
                                                <label class="radio-inline"><input type="radio" name="way" value="0" checked>全员签到</label>
                                                <label class="radio-inline"><input type="radio" name="way" value="1">随机签到</label>
                                            </div>
                                        </div>
                                        <div id="times" class="form-group">
                                            <label class="control-label">随机签到次数</label>
                                            <select name="" id="">
                                                <option value="5">5</option>
                                                <option value="6">6</option>
                                                <option value="7">7</option>
                                                <option value="8">8</option>
                                                <option value="9">9</option>
                                                <option value="10">10</option>
                                                <option value="11">11</option>
                                                <option value="12">12</option>
                                                <option value="13">13</option>
                                            </select>
                                        </div>
                                        <input type="submit" value="设置" class="btn btn-primary" name="submitButton" onclick="if(confirm('设置后将不能再修改，确定要设置？'))return true;else return false;">
                                    </form>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $("input:radio[name='way']").click(function(){
        if($(this).val()=='1'){
            $('#times').css('display','block');
        }else{
            $('#times').css('display','none');
        }
    })
</script>