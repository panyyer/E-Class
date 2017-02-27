<?php
namespace common\models;

use yii\db\ActiveRecord;

class SC extends ActiveRecord
{
    public static function tableName()
    {
        return "{{%SC}}";
    }

    public function rules()
    {
        return [
            ['sid','checkUnique'],
        ];
    }

    public function checkUnique($attribute, $param) {
        //找出该学生加入的所有课程
        $model = self::find()->where('sid=:sid',[':sid'=>$this->sid])->all();
        foreach ($model as $key => $value) {
            if($value->cid == $this->cid) {
                $this->addError($attribute, 'Invalid');
            }
        }
    }
}
