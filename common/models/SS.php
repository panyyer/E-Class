<?php
namespace common\models;
use yii\db\ActiveRecord;

class SS extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%SS}}';
    }
}