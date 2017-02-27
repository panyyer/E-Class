<?php

namespace frontend\controllers;
use common\models\Classes;
use common\models\Homework;
use common\models\SC;
use common\models\SH;
use frontend\models\Schedule;
use frontend\models\SHforSchedule;
use yii\web\Controller;
use Yii;
use common\models\Token;

/*
 *  日程接口
 *  @author Kehang Fang
 */
class ScheduleController extends Controller {

    //请求某天日程接口
    public function actionRequestDay() {
        $token = Yii::$app->request->post('access_token');
        $date = Yii::$app->request->post('date');
        $schedule = null;
        $return['status'] = false;

        $res = Token::Auth($token);
        if($res['status']) {
            if ($res['token']->type == 0) {
                /**------    以下是返回homework的日程   --------**/
                $i = 0;//用于记录返回的数量
                $info = SC::find()->where('sid=:sid',[':sid'=>$res['token']->uid])->all();

                foreach ($info as $value) {
                    $search = Homework::find()->where('cid=:cid',['cid'=>$value['cid']])->all();
                    if($search!=null) {
                        foreach ($search as $v) {

                            //检查所有符合条件的日程在这一天的紧急情况
                            $color_return = $this->Color(substr($v['deadline'],0,10),$date);
                            if($color_return == null) {
                                continue;
                            }else {
                                $schedule[$i]['countTime'] = $this->Timesub(substr($v['deadline'],0,10),$date);
                                $schedule[$i]['color'] = $color_return;
                            }

                            $classes = Classes::find()->where('id=:id',[':id'=>$v['cid']])->one();
                            $schedule[$i]['title'] = $classes->name.'作业：'.$v['title'];
                            $schedule[$i]['content'] = $v['content'];
                            $schedule[$i]['deadline'] = $v['deadline'];
                            $schedule[$i]['flag'] = 'h-'.$v['id'];

                            //从SHforSchedule表中找到具体日程的完成情况。
                            $find_status = SHforSchedule::find()->where('uid=:uid',[':uid'=>$res['token']->uid])->andWhere('hid=:hid',[':hid'=>$v['id']])->one();
                            $schedule[$i]['status'] = 0;
                            if(!empty($find_status)) {
                                $schedule[$i]['status'] =  $find_status->status;
                            }

                            //从SH表中找到该作业是否提交，并把日程的完成情况变成完成
                            $find_status2 = SH::find()->where('sid=:sid',[':sid'=>$res['token']->uid])->andWhere('hid=:hid',[':hid'=>$v['id']])->one();
                            if(!empty($find_status2)) {
                                $schedule[$i]['status'] = 1;
                            }

                            $i++;
                        }
                    }
                }
                /**--------    End Homework   --------**/

                /**--------    以下是返回用户自定义的日程   --------**/
                $info2 = Schedule::find()->where('uid=:uid',[':uid'=>$res['token']->uid])->all();
                foreach ($info2 as $v2) {

                    $color_return2 = $this->Color(substr($v2['deadline'],0,10),$date);
                    if($color_return2 == null) {
                        continue;
                    }else {
                        $schedule[$i]['countTime'] = $this->Timesub(substr($v2['deadline'],0,10),$date);
                        $schedule[$i]['color'] = $color_return2;
                    }

                    $schedule[$i]['title'] = $v2['title'];
                    $schedule[$i]['content'] = $v2['content'];
                    $schedule[$i]['deadline'] = $v2['deadline'];
                    $schedule[$i]['flag'] = 's-'.$v2['id'];
                    $schedule[$i]['status'] = $v2['status'];
                    $i++;
                }
                $return['status'] = true;
                $return['code'] = 4000;
            }else {
                $return['code'] = 4003;
            }
        }else{
            $return['code'] = 4007;
        }
        //status 为真时，返回按照截止日期排序的schedule，
        if($return['status']) {
            $filed = 'countTime';
            $this->sortArrByField($schedule,$filed);
            $return['schedule'] = $schedule;
        }
        return json_encode($return);
    }

    /**
     *  该方法用来设置日程的紧急颜色，
     *  距离deadline 【6，5，4天时间 返回颜色3】，【3，2天 返回颜色2】，【1，0天返回颜色1】
     */
    private function Color($dateBig,$dateSmall) {
        $sub_result = $this->Timesub($dateBig,$dateSmall);
        if($sub_result <= 6 && $sub_result > 3) {
            return 3;
        }else if($sub_result <= 3 && $sub_result >= 2){
            return 2;
        }else if($sub_result == 1 || $sub_result == 0) {
            return 1;
        }else {
            return null;
        }
    }

    private function Timesub($dateBig,$dateSmall) {
        return round((strtotime($dateBig)-strtotime($dateSmall))/3600/24);
    }

    //添加自定义日程接口
    public function actionSubmit() {
        $return = ['code'=>4004,'status'=>false];

        $token = Yii::$app->request->post('access_token');
        $title = Yii::$app->request->post('title');
        $content = Yii::$app->request->post('content');
        $deadline = Yii::$app->request->post('deadline');

        if($title == null || $deadline == null || strtotime($deadline)<strtotime(date("Y-m-d"))) {
            $return = ['code'=>4001,'status'=>true];
        }else{
            $res = Token::Auth($token);
            if($res['status']) {
                if($res['token']->type == 0) {
                    $info = new Schedule();
                    $info->title = $title;
                    $info->content = $content;
                    $info->deadline = $deadline;
                    $info->submit_time = date("Y-m-d H:i:s", time());
                    $info->uid = $res['token']->uid;
                    if($info->save()) {
                        $return = ['code'=>4000,'status'=>true];
                    }
                }
            }else{
                $return = ['code'=>4007,'status'=>true];
            }
        }
        return json_encode($return);
    }

    //删除自定义日程接口
    public function actionDelete() {
        $token = Yii::$app->request->post('access_token');
        $flag = Yii::$app->request->post('flag');

        $return = ['code'=>4004,'status'=>false];

        $type = substr($flag,0,1);
        $id = (int)substr($flag,2);

        $res = Token::Auth($token);
        if($res['status']) {
            //只能是自定义日程的才能允许删除
            if($type == 's') {
                $search = Schedule::find()->where('id=:id', [':id' => $id])->one();
                $count = Schedule::find()->where('id=:id', [':id' => $id])->count();
                if($count != 0) {
                    if($search->delete()) {
                        $return = ['code'=>4000,'status'=>true];
                    }
                }
            }
        }else{
            $return = ['code'=>4007,'status'=>false];
        }
        return json_encode($return);
    }

    //日程完成
    public function actionFinish() {
        $token = Yii::$app->request->post('access_token');
        $flag = Yii::$app->request->post('flag');
        $return = ['code'=>4004,'status'=>false];
        $type = substr($flag,0,1);
        $id = (int)substr($flag,2);

        $res = Token::Auth($token);
        if($res['status']) {
            //当该日程来自于homework表
            if ($type == 'h') {
                $count = SHforSchedule::find()->where('uid=:uid',[':uid'=>$res['token']->uid])->andWhere('hid=:hid',[':hid'=>$id])->count();
                if($count != 0) {
                    $model = SHforSchedule::find()->where('uid=:uid',[':uid'=>$res['token']->uid])->andWhere('hid=:hid',[':hid'=>$id])->one();
                }else{
                    $model = new SHforSchedule();
                }
                $model->uid = $res['token']->uid;
                $model->hid = $id;
                $model->status = 1;
                if($model->save()) {
                    $return = ['code'=>4000,'status'=>true];
                }
            }else{
                $model = Schedule::find()->where('id=:id',[':id'=>$id])->one();
                if(!empty($model)) {
                    $model->status = 1;
                    if($model->save()) {
                        $return = ['code'=>4000,'status'=>true];
                    }
                }
            }
        }else{
            $return = ['code'=>4007,'status'=>false];
        }
        return json_encode($return);
    }

    //根据多维数组某个字段进行排序，在返回某天日程接口中调用。
    function sortArrByField(&$array, $field, $desc = false){
        $fieldArr = array();
        foreach ($array as $k => $v) {
            $fieldArr[$k] = $v[$field];
        }
        $sort = $desc == false ? SORT_ASC : SORT_DESC;
        array_multisort($fieldArr, $sort, $array);
    }
}