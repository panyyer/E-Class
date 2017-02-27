<?php
/**
 * Created by PhpStorm.
 * User: home
 * Date: 2016/12/6
 * Time: 22:39
 */

namespace backend\controllers;

use common\models\Classes;
use common\models\SC;
use common\models\Sign;
use common\models\SS;
use common\models\Student;
use frontend\controllers\SignController;
use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
class AttendController extends Controller
{
    public $sign_num = 8;
    public $layout='index';
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only'  => ['index'],
                'rules' => [
                    [
                        'allow'   => true,
                        'actions' => ['index'],
                        'roles'   => ['@']
                    ],
                ],
                'denyCallback' => function () {
                    if(Yii::$app->user->isGuest)
                    {
                        return Yii::$app->getResponse()->redirect(["admin/login"]);
                    }else {
                        return Yii::$app->getResponse()->redirect(["index/main"]);
                    }
                }
            ],
        ];
    }
    public function actionSetting($id){
        return $this->render('setting');
    }
    public function actionIndex($id){
        $sc = SC::find()->where('cid=:cid',['cid'=>$id])->all();
        if(!empty($sc)) {
            $sign_model = Sign::find()->where('cid=:cid',['cid'=>$id])->andWhere(['<','expire_time',(time()-SignController::LATE_TIME)])->all();
            $j = 0;
            $n = 0;
            if(!empty($sign_model)){
                foreach ($sign_model as $v) {
                    $model = SS::find()->where('sign_id=:sign_id',[':sign_id'=>$v['id']])->all();
                    $count[$n]['time'] =  substr(date("Y-m-d H:i:s",Sign::find()->where('id=:id',['id'=>$v['id']])->one()->expire_time),0,10);
                    $count[$n]['count'] = SS::find()->where('sign_id=:sign_id',[':sign_id'=>$v['id']])->count();
                    $count[$n]['absent'] = SS::find()->where('sign_id=:sign_id',[':sign_id'=>$v['id']])->andWhere('status=:st',[':st'=>0])->count();
                    $count[$n]['ok'] = SS::find()->where('sign_id=:sign_id',[':sign_id'=>$v['id']])->andWhere('status=:st',[':st'=>1])->count();
                    $count[$n]['late'] = SS::find()->where('sign_id=:sign_id',[':sign_id'=>$v['id']])->andWhere('status=:st',[':st'=>2])->count();
                    $n++;
                    foreach ($model as $v2) {
                        $sign[$j] = ['status'=>$v2['status'],'sid'=>$v2['sid'],'count'=>$v['count']];
                        $j++;
                    }
                }
            }else{
                $count = null;
            }

            //所有学生
            $i = 0;
            foreach ($sc as $value) {
                $info = Student::find()->where('id=:id',[':id'=>$value['sid']])->one();
                $student[$i] = ['name'=>$info->name,'sid'=>$info->id,'number'=>$info->number,'count'=>0,'absent'=>0,'late'=>0,'ok'=>0];
                for($m=0;$m<$this->sign_num;$m++) {
                    $student[$i][$m] = 'not';
                }
                if(!empty($sign)) {
                    foreach ($sign as $value2) {
                        if($info->id == $value2['sid']) {
                            $student[$i]['count']++;
                            if($value2['status'] == 0) {
                                $student[$i]['absent']++;
                                $student[$i][($value2['count']-1)] = 'absent';
                            }elseif ($value2['status'] == 1) {
                                $student[$i]['ok']++;
                                $student[$i][($value2['count']-1)] = 'ok';
                            }else {
                                $student[$i]['late']++;
                                $student[$i][($value2['count']-1)] = 'late';
                            }
                        }
                    }
                }
                $i++;
            }
        }
        return $this->render('index',['student'=>$student,'count'=>$count,'sign_num'=>$this->sign_num]);
    }
}