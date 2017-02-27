<?php
namespace frontend\controllers;

use common\models\SC;
use common\models\Sign;
use common\models\SS;
use common\models\Token;
use Yii;
use yii\web\Controller;

class SignController extends Controller {

    const TIME = 300;//签到过期时间为5*60=300秒
    const LATE_TIME = 600;//过期10分钟后为迟到签到时间
    const DISTANCE = 1000;//判断合法的签到距离为30米
    private $cid;
    private $type;//用户类型，学生还是老师
    private $uid;
    const WEIGHT = 5;//保证每个学生签到4次(这里权重必须是每个学生最低签到次数+1)
    const SIGN_NUM = 8;//老师每学期开启签到8次

    private $return = ['status'=>false,'code'=>4004];

    public function Before() {
        $token = Yii::$app->request->post('access_token');
        $this->cid = Yii::$app->request->post('cid');
        $res = Token::Auth($token);

        if(!$res['status']) {
            $this->return = ['status'=>false,'code'=>4007];
        }else{
            $this->type = $res['token']->type;
            $this->uid = $res['token']->uid;
        }
    }

    //老师设置本学期签到次数
    public function actionSetting() {

    }

    //老师开启签到接口
    public function actionStart() {
        $this->Before();
        if($this->type == 1) {
            $count = (Sign::find()->where('tid=:tid',[':tid'=>$this->uid])->andWhere('cid=:cid',['cid'=>$this->cid])->count())+1;
            if($count >= SignController::SIGN_NUM) {
                $this->return = ['status'=>false,'code'=>4012];//签到次数达到上限
                return json_encode($this->return);
            }
            $result = $this->Draw();

            //插入数据到SignforTeacher
            $longitude = Yii::$app->request->post('longitude');
            $latitude = Yii::$app->request->post('latitude');
            $model = new Sign();
            $model->tid = $this->uid;
            $model->cid = $this->cid;
            $model->count = $count;
            $model->longitude = $longitude;
            $model->latitude = $latitude;
            $model->expire_time = time() + SignController::TIME;
            if($model->save()) {
                $find = Sign::find()->where('tid=:tid',[':tid'=>$this->uid])->andWhere('cid=:cid',['cid'=>$this->cid])->andWhere('count=:c',[':c'=>$count])->one();
                foreach ($result as $v) {
                    $ss = new SS();
                    $ss->sid = $v['sid'];
                    $ss->sign_id = $find->id;
                    if($ss->save()) {
                        $this->return = ['status'=>true,'code'=>4000];
                    }
                }
                $this->return['left'] = SignController::SIGN_NUM - $find->count;
            }
        }else {
            $this->return = ['status'=>false,'code'=>4003];
        }
        return json_encode($this->return);
    }

    /**-------- 签到核心算法(抽取学生)  ---------**/
    private function Draw() {
        //算出每个学生的比重 被抽取过：每正常签到1次-2，正常签到大于等于4次的比重直接变为0，旷课1次加5次，迟到1次加3次
        //假设每个学生占份,正常签到一次
        $student = SC::find()->where('cid=:cid',[':cid'=>$this->cid])->all();
        $i = 0;
        $All_weight = 0;
        foreach ($student as $value) {
            //某个学生的正常签到次数，迟到次数，旷课次数
            $stu[$i] = ['sid'=>$value['sid'],'flag'=>0,'weight'=>SignController::WEIGHT];
            $All = SS::find()->where('sid=:sid',[':sid'=>$value['sid']])->count();
            if($All == SignController::WEIGHT) {
                $stu[$i]['weight'] = 1;
            }elseif ($All == SignController::WEIGHT+1) {
                $stu[$i]['weight'] = 0;
            }else{
                $sign_num = SS::find()->where('sid=:sid',[':sid'=>$value['sid']])->andWhere('status=:st',[':st'=>1])->count();
                $late_num = SS::find()->where('sid=:sid',[':sid'=>$value['sid']])->andWhere('status=:st',[':st'=>2])->count();
                $absent_num = SS::find()->where('sid=:sid',[':sid'=>$value['sid']])->andWhere('status=:st',[':st'=>0])->count();
                $stu[$i]['weight'] = $stu[$i]['weight'] + $sign_num*(-1) + 3*$late_num + 4*$absent_num;
            }
            $All_weight += $stu[$i]['weight'];
            $i++;
        }
        //算出本次要抽取的学生人数
        $stu_num = SC::find()->where('cid=:cid',[':cid'=>$this->cid])->count();
        $num = floor(($stu_num*SignController::WEIGHT)/SignController::SIGN_NUM)+1;

        //新建立对应关系模拟抽中(从总份数中抽取num，抽取到的份数属于哪个学生的就代表该学生被选中)
        for($j=0;$j<$num;$j++) {
            $lucky = rand(1,$All_weight);
            $now = 0;
            for($k=0;$k<$i && $now<=$lucky ;$k++) {
                $now = $now + $stu[$k]['weight'];
            }
            $k -= 1;//下标从0开始
            if($stu[$k]['flag']) {
                $num += 1;
            }else{
                $stu[$k]['flag'] = 1;
            }
        }

        //取出抽到的sid返回
        $j = 0;
        foreach ($stu as $v) {
            if($v['flag'] == 1) {
                $result[$j]['sid'] = $v['sid'];
            }
            $j++;
        }
        return $result;
    }

    //学生请求查看是否需要签到,返回具体的提示信息
    public function actionLucky() {
        $this->Before();
        if($this->return['code'] == 4007) {
            return json_encode($this->return);
        }
        if($this->type == 0) {
            $id = Sign::find()->where('cid=:cid', [':cid' => $this->cid])->max('id');

            if ($id == null) {
                $this->return = ['status' => true, 'code' => 4013];//暂无签到（或你已旷课）
            } else {
                $sign = Sign::find()->where('id=:id', [':id' => $id])->one();
                if ($sign->expire_time + SignController::LATE_TIME < time()) {
                    $this->return = ['status' => true, 'code' => 4013];
                } else {
                    $find = SS::find()->where('sign_id=:sign', [':sign' => $id])->andWhere('sid=:sid', [':sid' => $this->uid])->one();
                    if (empty($find)) {
                        $this->return = ['status' => true, 'code' => 4014];//本次没被抽到
                    } else {
                        if ($find->status == 0) {
                            $this->return = ['status' => true, 'code' => 4015,'id'=>$find->sign_id];//本次被抽到
                        } else {
                            $this->return = ['status' => true, 'code' => 4016];//已经签到过，无需再签到。
                        }
                    }
                }
            }
        }else {
            $this->return = ['status'=>false,'code'=>4003];
        }

        //返回学生的签到情况
        //签到已经过去15分钟
        $sign_model = Sign::find()->where('cid=:cid',['cid'=>$this->cid])->andWhere(['<','expire_time',(time()-SignController::LATE_TIME)])->all();
        //签到开启还没结束。即还在15分钟内
        $sign_now = Sign::find()->where('cid=:cid',['cid'=>$this->cid])->andWhere(['<','expire_time',(time()-SignController::TIME)])->andWhere(['>','expire_time',(time()-SignController::LATE_TIME)])->all();
        $this->return['count'] = 0;$this->return['absent'] = 0;$this->return['left'] = 0;$this->return['ok'] = 0;$this->return['askforleave'] = 0;
        foreach ($sign_model as $value) {
            $ss_md = SS::find()->where('sid=:sid',[':sid'=>$this->uid])->andWhere('sign_id=:sign_id',[':sign_id'=>$value['id']])->one();
            if(!empty($ss_md)) {
                $this->return['count']++;
                if($ss_md['status'] == 0) {
                    $this->return['absent']++;
                }elseif($ss_md['status'] == 1) {
                    $this->return['ok']++;
                }elseif ($ss_md['status'] == 2) {
                    $this->return['left']++;
                }
            }
        }
        foreach ($sign_now as $value2) {
            $model = SS::find()->where('sid=:sid',[':sid'=>$this->uid])->andWhere('sign_id=:sign_id',[':sign_id'=>$value2['id']])->one();
            if(!empty($model)) {
                $this->return['count']++;
                if($model['status'] == 1) {
                    $this->return['ok']++;
                }elseif ($model['status'] == 2) {
                    $this->return['late']++;
                }
            }
        }

        return json_encode($this->return);
    }

    //学生签到接口
    public function actionSign() {
        $this->Before();
        if($this->return['code'] == 4007) {
            return json_encode($this->return);
        }
        $get = json_decode($this->actionLucky(),true);
        $latitude = Yii::$app->request->post('latitude');
        $longitude = Yii::$app->request->post('longitude');

        if($get['code'] == 4015) {
            $sign = Sign::find()->where('id=:id',[':id'=>$get['id']])->one();
            $model = SS::find()->where('sign_id=:sign_id',[':sign_id'=>$get['id']])->andWhere('sid=:sid',[':sid'=>$this->uid])->one();
            if($this->getDistance($latitude,$longitude,$sign->latitude,$sign->longitude) <= SignController::DISTANCE) {
                if($sign->expire_time>=time()) {
                    $model->status = 1;
                    $this->return = ['status'=>true,'code'=>4017];//正常签到成功
                }else{
                    $model->status = 2;
                    $this->return = ['status'=>true,'code'=>4018];//迟到签到成功
                }
                if($model->save()) {
                    return json_encode($this->return);
                }
            }else{
                $this->return = ['status'=>false,'code'=>4019];//距离过远，签到失败
                return json_encode($this->return);
            }
        }else{
            return json_encode($get);
        }
    }

    //返回两个坐标点之间的距离
    private function getDistance($lat1, $lng1, $lat2, $lng2) {
        $earthRadius = 6367000; //approximate radius of earth in meters

        $lat1 = ($lat1 * pi() ) / 180;
        $lng1 = ($lng1 * pi() ) / 180;

        $lat2 = ($lat2 * pi() ) / 180;
        $lng2 = ($lng2 * pi() ) / 180;

        $calcLongitude = $lng2 - $lng1;
        $calcLatitude = $lat2 - $lat1;
        $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);
        $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
        $calculatedDistance = $earthRadius * $stepTwo;

        return round($calculatedDistance);
    }
    
}