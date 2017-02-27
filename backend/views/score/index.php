    <?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

use backend\assets\ScoreAsset;
ScoreAsset::register($this);
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
                            <li class="active">
                                <a href="<?= urlDecode(Url::toRoute(['score/index','id'=>Yii::$app->session['class_id']])); ?>">设置比例</a>
                            </li>
                            <li>
                                <a href="<?= urlDecode(Url::toRoute(['score/view','id'=>Yii::$app->session['class_id']])); ?>">查看分数</a>
                            </li>
                        </ul>
                        <div class="tab-pane" id="2">
                            <fieldset>
                                <div id='top'>
                                    <div id="chart">
                                        <canvas id="doughnut" width="150" height="150"></canvas>
                                    </div>
                                    <div id="set">
 
                                        <form action="<?= urlDecode(Url::toRoute(['score/index','id'=>Yii::$app->session['class_id']])); ?>" method="post">
                                            <p>
                                                <span class="homeworkblock"></span>
                                                <label for="">作业</label>
                                                <span class="percent">%</span>
                                                <input type="text" name="homework" value='<?= $homework ?>'>
                                            </p>
                                            <p>
                                                <span class="attendblock"></span>
                                                <label for="">考勤</label>
                                                <span class="percent">%</span>
                                                <input type="text" name="attend" value='<?= $attend ?>'>
                                            </p>
                                            <button>设置</button>
                                        </form>
                                    </div>
                                </div>
                                <hr/>
                                <div id="bottom">
                                    <div id="barchart">
                                        <span class="y-name">人数</span>
                                        <canvas id="bar" height="300" width="330"></canvas>
                                        <span>分数</span>
                                    </div>
                                    <div id="table">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th>平均分</th>
                                                <td><?= round($cnt['aver'],2) ?>分</td>
                                            </tr>
                                            <tr>
                                                <th>最高分</th>
                                                <td><?= round($cnt['maxm'],2) ?>分</td>
                                            </tr>
                                            <tr>
                                                <th>最低分</th>
                                                <td><?= round($cnt['minm'],2) ?>分</td>
                                            </tr>
                                            <tr>
                                                <th><60分</th>
                                                <td><?= $cnt[4] ?>人</td>
                                            </tr>
                                            <tr>
                                                <th>60-69分</th>
                                                <td><?= $cnt[3] ?>人</td>
                                            </tr>
                                            <tr>
                                                <th>70-79分</th>
                                                <td><?= $cnt[2] ?>人</td>
                                            </tr>
                                            <tr>
                                                <th>80-89分</th>
                                                <td><?= $cnt[1] ?>人</td>
                                            </tr>
                                            <tr>
                                                <th>>=90分</th>
                                                <td><?= $cnt[0] ?>人</td>
                                            </tr>
                                        </table>
                                    </div>
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
    var context=document.getElementById("doughnut").getContext("2d");
    var doughnutData=[
        {
            value: <?= $homework/100 ?>,
            color:"#ef553a"
        },
        {
            value : <?= $attend/100 ?>,
            color : "#9358ac"
        }
    ];
    new Chart(context).Doughnut(doughnutData);
    var barChartData = {
        labels : [">=90","80-89","70-79","60-69","<60"],
        datasets : [
            {
                fillColor : "#ef553a",
                strokeColor : "#ef553a",
                data : [<?= $array ?>]
            }
        ]

    };
    new Chart(document.getElementById("bar").getContext("2d")).Bar(barChartData);

</script>