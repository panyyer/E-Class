<?php
namespace common\models;

use yii\db\ActiveRecord;
use common\models\Teacher;

class Classes extends ActiveRecord
{

	public function rules()
    {
        return [
            [['name','period','class_time','class_place','code'], 'required'],
            [['code'], 'newUnique'],
            [['attendance_ratio','homework_ratio','period'], 'number'],
            [['attendance_ratio','homework_ratio'], 'less100'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => '课程名称',
            'class_time' => '上课时间',
            'class_place' => '上课地点',
            'period' => '课时',
            'code'=>'邀请码'
        ];
    }

    public function less100($attribute, $param) {
    	if($this->attendance_ratio + $this->homework_ratio > 100) {
    		$this->addError($attribute, 'Invalid');
    	}
    }

    public function newUnique($attribute, $param){
        $check = self::find()->where(['code' => $this->code])->one();
        while(!empty($check)) {
            $this->code = self::generateCode(6);
            $check = self::find()->where(['code' => $this->code])->one();
        }
    }


    //生成邀请码
    public static function generateCode($len){
        $rand = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $temp = strlen($rand);
        $code = NULL;
        for($i=0;$i<6;$i++) {
            $code.=$rand[rand(0,$temp-1)];
        }
        return $code;
    }
}
