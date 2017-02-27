<?php
namespace common\models;

use yii\db\ActiveRecord;

class Notice extends ActiveRecord
{

    public function rules()
    {
        return [
            ['content','required'],
        ];
    }

}
