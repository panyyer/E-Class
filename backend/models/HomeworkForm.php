<?php
namespace backend\models;
use Yii;
use common\models\Homework;
use yii\base\Model;

class HomeworkForm extends Model {
    public $title;
    public $content;
    public $deadline;
    public $filename;
    public function rules()
    {
        return [
            [['title','content','deadline'],'required'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'title'=>'标题',
            'content'=>'内容',
            'deadline'=>'截止时间',
            'filename'=>'上传附件'
        ];
    }
}