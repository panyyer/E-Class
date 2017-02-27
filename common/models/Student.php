<?php
namespace common\models;

use yii\db\ActiveRecord;
use common\models\Teacher;

class Student extends ActiveRecord
{
	public $confirm_password;



    public function rules()
    {
        return [
            ['number', 'number'],
            [['phone','password','confirm_password','number','name','sex','school','academy','department','major','province','create_time'], 'safe'],
        ];
    }

	public function isValid(){
		$result = ['status' => true, 'code' => 4000];
		if(!preg_match('/^1[0-9]{10}$/', $this->phone)) {
			return ['status' => false, 'code' => 4001];
		} 
		if(empty($this->password) || empty($this->confirm_password)) {
			return ['status' => false, 'code' => 4001];
		}
		if($this->password != $this->confirm_password) {
			return ['status' => false, 'code' => 4005];
		}
		$model = self::find()->where('phone=:phone', [':phone' => $this->phone])->one();
		$model2 = Teacher::find()->where('phone=:phone', [':phone' => $this->phone])->one();
		if(!(empty($model) && empty($model2))){
			return ['status' => false, 'code' => 4002];
		}
		return $result;
	}
}
