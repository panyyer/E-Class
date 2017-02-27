<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use frontend\models\LoginForm;
use common\models\Teacher;
use common\models\Student;
use common\models\Token;

class UserController extends Controller{

    public $result = ['status' => false, 'code' => 4004]; //未知错误
    public $st_info = ['phone','name','sex','school','academy','department','major','number','province'];
    public $tc_info = ['phone','name','sex','school','province'];

    public function actionRegister()
    {    
        $type = Yii::$app->request->post('type');

        if($type == 0) {
            $st = new Student();
            $st->setAttributes(Yii::$app->request->post());
            $this->result = $st->isValid();
            if($this->result['status']) {
                $st->password = Yii::$app->security->generatePasswordHash($st->password);
                $st->create_time = date("Y-m-d H:i:s",time());
                $st->save();
            }
        } else if($type == 1) {
            $tc = new Teacher();
            $tc->setAttributes(Yii::$app->request->post());
            $this->result = $tc->isValid();
            if($this->result['status']) {
                $tc->password = Yii::$app->security->generatePasswordHash($tc->password);
                $tc->create_time = date("Y-m-d H:i:s",time());
                $tc->save();
            }
        }
        return json_encode($this->result);
    }

    public function actionLogin()
    {
        $phone = Yii::$app->request->post('phone');
        $pass = Yii::$app->request->post('password');
        // $type = Yii::$app->request->post('type');
        $model = NULL;
        $type = 0;
        $model = Student::find()->where('phone=:phone', [':phone'=>$phone])->one();
        if(empty($model)){
            $type = 1;
            $model = Teacher::find()->where('phone=:phone', [':phone'=>$phone])->one();
        }
        if(empty($model) || !Yii::$app->security->validatePassword($pass,$model->password)){
            $this->result = ['status' => false, 'code' => 4006];
        } else {
            $_token = Token::find()->where('uid=:id AND type=:type', [':id'=>$model->id, ':type'=>$type])->one();
            if(!empty($_token)) {
                $_token->delete();
            }
            $token_model = new Token();
            $token = Yii::$app->security->generateRandomString().time();
            $expire_time = time() + $token_model::TIME;
            $token_model->access_token = $token;
            $token_model->expire_time = $expire_time;
            $token_model->uid = $model->id;
            $token_model->type = $type;
            $token_model->save();               
            $this->result = ['status' => true, 'code' => 4000, 'access_token' => $token, 'type' => $type, 'uid' => $model->id];
        }            
        return json_encode($this->result);
    }

    public function actionEdit()
    {
        $token = Yii::$app->request->post('access_token');
        // $type = Yii::$app->request->post('type'); 
        
        $res = Token::Auth($token);
        if($res['status'] == true) {
            if($res['token']->type == 0) {
                $model = Student::find()->where('id=:id',[':id'=>$res['token']->uid])->one();
            } else if($res['token']->type == 1) {
                $model = Teacher::find()->where('id=:id',[':id'=>$res['token']->uid])->one();
            }
            $model->setAttributes(Yii::$app->request->post());
            if($model->save()) {
                $this->result = ['status' => true, 'code' => 4000];
            } else {
                $this->result = ['status' => false, 'code' => 4001];
            }
        }
        return json_encode($this->result);
    }

    public function actionInfo()
    {
        $token = Yii::$app->request->post('access_token');  
        $res = Token::Auth($token);
        if($res['status'] == true) {
            if($res['token']->type == 0) {
                $model = Student::find()->where('id=:id',[':id'=>$res['token']->uid])->one();
                if(!empty($model)) {
                    $this->result = ['status' => true, 'code' => 4000];
                    foreach ($this->st_info as $key => $value) {
                        $this->result[$value] = $model->$value;
                    }
                } else {
                    $this->result = ['status' => false, 'code' => 4008];
                }
            } else if($res['token']->type == 1) {
                $model = Teacher::find()->where('id=:id',[':id'=>$res['token']->uid])->one();
                if(!empty($model)) {
                    $this->result = ['status' => true, 'code' => 4000];
                    foreach ($this->tc_info as $key => $value) {
                        $this->result[$value] = $model->$value;
                    }
                } else {
                    $this->result = ['status' => false, 'code' => 4008];
                }
            }
        }
        return json_encode($this->result);     
    }

    public function actionInfos()
    {

    }
}
?>