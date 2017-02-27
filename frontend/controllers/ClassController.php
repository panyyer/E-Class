<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use common\models\Teacher;
use common\models\Student;
use common\models\Token;
use common\models\Classes;
use common\models\SC;

class ClassController extends Controller{

    public $result = ['status' => false, 'code' => 4004]; //未知错误
    public $success = ['status' => true, 'code' => 4000]; //成功
    public $class_info = ['name', 'class_time', 'class_place', 'period', 'attendance_ratio', 'homework_ratio'];

    public function actionCreate()
    {
        // $type = Yii::$app->request->post('type');
        $token = Yii::$app->request->post('access_token');
        $res = Token::Auth($token);
        if($res['status']) {
            if($res['token']->type == 1) {
                $classes = new Classes();
                $classes->tid = $res['token']->uid;
                $classes->code = Classes::generateCode(6);
                $classes->create_time = date("Y-m-d H:i:s", time());
                $classes->setAttributes(Yii::$app->request->post());
                if($classes->save()) {
                    $this->result = $this->success;
                    $this->result['cid'] = $classes->id;
                    $this->result['class_code'] = $classes->code;
                } else {
                    $this->result = ['status' => false, 'code' => 4001];
                }
            } else {
                $this->result = ['status' => false, 'code' => 4003];
            } 
        } 
        return json_encode($this->result);
    }

    public function actionEdit()
    {
        // $type = Yii::$app->request->post('type');
        $token = Yii::$app->request->post('access_token');
        $id = Yii::$app->request->post('cid');
        $res = Token::Auth($token);
        if($res['status']) {
            if($res['token']->type == 1) {
                $class = Classes::find()->where('id=:id', [':id'=>$id])->one();
                $class->setAttributes(Yii::$app->request->post());
                if($class->save()) {
                    $this->result = $this->success;
                } else {
                    $this->result = ['status' => false, 'code' => 4001];
                }                
            } else {
               $this->result = ['status' => false, 'code' => 4003];
            } 
        }

        return json_encode($this->result);
    }

    public function actionJoin()
    {
        $token = Yii::$app->request->post('access_token');
        $code = Yii::$app->request->post('class_code');
        $res = Token::Auth($token);
        if($res['status']) {
            $code_model = Classes::find()->where('code=:code', [':code'=>$code])->one();
            if(!empty($code_model)) {
                if($res['token']->type == 0) {
                    $sc = new SC();
                    $sc->cid = $code_model->id;
                    $sc->sid = $res['token']->uid;
                    $sc->join_time = date("Y-m-d H:i:s",time());
                    if($sc->save()) {
                        $this->result = $this->success;
                        $this->result['cid'] = $sc->cid;
                        $this->result['name'] = $code_model->name;
                    } else {
                        $this->result = ['status' => false, 'code' => 4003];
                    }
                } else {
                    $this->result = ['status' => false, 'code' => 4003];
                }
            } else {
                $this->result = ['status' => false, 'code' => 4008];
            }
        }
        return json_encode($this->result);
    }

    public function actionRequestOne()
    {
        $token = Yii::$app->request->post('access_token');
        $cid = Yii::$app->request->post('cid'); 
        $res = Token::Auth($token);
        if($res['status'] == true) {
            $model = Classes::find()->where('id=:cid', [':cid'=>$cid])->one();
            if(!empty($model)) {
                //判断是否是该班级的成员
                if($res['token']->type == 0) {
                    $sc = SC::find()->where('cid=:cid', [':cid'=>$cid])->one();
                    if($sc->sid == $res['token']->uid) {
                        $this->result = $this->success;
                        foreach ($this->class_info as $key => $value) {
                            $this->result[$value] = $model->$value;
                        }                        
                    } else {
                        $this->result = ['status' => false, 'code' => 4003];
                    }
                } else if($res['token']->type == 1) {
                    //如果是该班的老师，返回的数据带邀请码
                    if($model->tid == $res['token']->uid) {
                        $this->result = $this->success;
                        foreach ($this->class_info as $key => $value) {
                            $this->result[$value] = $model->$value;
                        }
                        $this->result['class_code'] = $model->code;                    
                    } else {
                        $this->result = ['status' => false, 'code' => 4003];
                    }
                }
            } else {
                $this->result = ['status' => false, 'code' => 4008];
            }
        }
        return json_encode($this->result);
    }

    public function actionRequestAll()
    {
        $token = Yii::$app->request->post('access_token');
        $res = Token::Auth($token);
        $classes = array();
        if($res['status'] == true) {
            if($res['token']->type == 0) {
                $sql = "SELECT c.* FROM Eclass_classes c, Eclass_SC s WHERE c.id=s.cid AND s.sid=".$res['token']->uid;
                $model = Classes::findBySql($sql)->all(); 
                if(!empty($model)) {
                    $this->result = $this->success;
                    foreach ($model as $key => $value) {
                        foreach ($this->class_info as $k => $v) {
                            $classes[$key][$v] = $value->$v;
                        }
                        $classes[$key]['cid'] = $value->id;
                    }                    
                }
                $this->result['classes'] = $classes;
            } else if($res['token']->type == 1) {
                $model = Classes::find()->where('tid=:t',[':t'=>$res['token']->uid])->all();
                if(!empty($model)) {
                    $this->result = $this->success;
                    foreach ($model as $key => $value) {
                        foreach ($this->class_info as $k => $v) {
                            $classes[$key][$v] = $value->$v;
                        }
                        //班级邀请码只有老师才能看到
                        $classes[$key]['class_code'] = $value->code;  
                        $classes[$key]['cid'] = $value->id;  
                    }                    
                }
                $this->result['classes'] = $classes;
            }
        }
        return json_encode($this->result);
    }
}

?>