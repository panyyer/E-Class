<?php
namespace frontend\models;
use yii\db\ActiveRecord;

class SHforSchedule extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%SHforSchedule}}';
    }
}