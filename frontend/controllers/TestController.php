<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;

use frontend\models\test\Climate_factor;
use frontend\models\test\Space_factor;
use frontend\models\test\Date_factor;
use frontend\models\test\Forest_factor;
use frontend\models\test\Forest_fires;
use frontend\models\test\Main;
use frontend\models\test\Loss;


/**
 * Site controller
 */
class TestController extends Controller
{
    public function actionTest() {
        // $sql = "SELECT st.* FROM Eclass_student st, Eclass_SC sc WHERE st.id=sc.sid AND sc.cid=".$cid;
        // $stu = Student::findBySql($sql)->all();
        // $sc = SC::find()->where('cid=:cid', [':cid'=>$model->cid])->all();
        $main = Main::find()->all();
        // $loss = Loss::find()->all();
        $fires = Forest_fires::find()->all();
        // $climate = new Climate_factor();
        // $space = new Space_factor();
        // $climate = new Forest_factor();
        // $climate = new Date_factor();
            // $space = new Space_factor();

            // $fires = new Forest_fires();
        // $climate = new Forest_fires();
            // $cid = array();
            // $did = array();
            // $sid = array();
            // $fid = array();
            // $lid = array();
            // var_dump($main);
        foreach ($main as $key => $value) {
            // $climate = new Climate_factor();
            // $climate->temp = $value->temp;
            // $climate->RH = $value->RH;
            // $climate->rain = $value->rain;
            // $climate->wind = $value->wind;
            // $climate->insert();
            // $cid[] = $climate->id;
            // $space = new Space_factor();
            // $space->X = $value->X;
            // $space->Y = $value->Y;
            // $space->insert();

            // $forest = new Forest_factor();
            // $forest->FFMC = $value->FFMC;
            // $forest->DMC = $value->DMC;
            // $forest->DC = $value->DC;
            // $forest->ISI = $value->ISI;
            // $forest->insert();

            // $date = new Date_factor();
            // $date->month = $value->month;
            // $date->day = $value->day;
            // $date->insert();

            // $loss = new Loss();
            // if($value->area > 0) {
            //     $loss->money = $value->area + rand(1000,10000)*$value->area/100000;
            //     $loss->wounded = (int)rand(1000,3000)*$value->area/10000;
            //     $loss->deadth = (int)rand(1000,3000)*$value->area/100000;
            //     if($loss->wounded < 1) $loss->wounded = 0;
            //     if($loss->deadth < 1) $loss->deadth = 0;
            // }
            // $loss->insert();

            $fires[$key]->cid = $key+1;
            $fires[$key]->did = $key+1;
            $fires[$key]->sid = $key+1;
            $fires[$key]->fid = $key+1;
            $fires[$key]->lid = $key+1;
            // $fires->area = $value->area;

            // if($value->area > 0) {
            //     $fires->level = $value->area*0.03+$loss[$key]->deadth+$loss[$key]->wounded;
            // } else {
            //     $fires->level = 0;
            // }
            // $len = [0,0,0,0,0,0,0];
            // $arr = [$value->area,$value->FFMC,$value->DMC,$value->temp,$value->DC,$value->RH,$value->rain];
            // // var_dump($arr);exit;
            // for($i=0;$i<7;$i++) {
            //     while($arr[$i] >= 1) {
            //         $len[$i]++;
            //         $arr[$i]/=10;
            //     }
            //     // echo $len[$i];
            // }
            // $fires[$key]->possibility = $value->area*pow(10,5-$len[0])*0.2 + $value->FFMC*pow(10,5-$len[1])*0.3 + $value->DMC*pow(10,5-$len[2])*0.05 + $value->temp*pow(10,5-$len[3])*0.2 + $value->DC*pow(10,5-$len[4])*0.05 + $value->RH*pow(10,5-$len[5])*0.1 + $value->rain*pow(10,5-$len[6])*0.1;
            // $fires[$key]->possibility/=pow(10,5);
            // var_dump($fires[$key]->possibility);

            // while(!($fires[$key]->possibility < 1)) {
            //     $fires[$key]->possibility/=10.0;
            // }

            $fires[$key]->update();
        }
        echo "<script>alert('succeed')</script>";
    }
}
