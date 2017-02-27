<?php
namespace common\models;

use yii\db\ActiveRecord;

class SH extends ActiveRecord
{
    public static function tableName()
    {
        return "{{%SH}}";
    }

    public function rules()
    {
        return [
            ['score','required', 'message' => '分数必填'],
            ['message','safe'],
        ];
    }

}
