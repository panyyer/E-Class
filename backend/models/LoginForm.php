<?php
namespace  backend\models;

use yii\base\Model;
use backend\models\Admin;
use Yii;
/**
 * Created by PhpStorm.
 * User: home
 * Date: 2016/11/26
 * Time: 19:55
 */
class LoginForm extends Model
{
    public $phone;
    public $password;
    public $verifyCode;
    private $_user;
    public function rules(){
        return [
            [['phone','password'],'required'],
            ['verifyCode','captcha'],
            ['password','validatePwd'],
        ];
    }
    public function attributeLabels(){
        return[
            'phone'=>'手机号码',
            'password'=>'密码'
        ];
    }
    public function login(){
        if($this->validate()){
            return Yii::$app->user->login($this->getUser());
        }
        return false;
    }
    public function validatePwd($attribute,$params){
        if(!$this->hasErrors()){
            $user=$this->getUser();
            if(!$user||!$user->validatePassword($this->password)){
                $this->addError($attribute,'手机号码或密码错误');
            }
        }
    }
    public function getUser(){
        if($this->_user==null){
            $this->_user=Admin::findByUsername($this->phone);
        }
        return $this->_user;
    }
}