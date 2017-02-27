<?php
namespace common\models;
use yii\db\ActiveRecord;

class Sign extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%Sign}}';
    }
}