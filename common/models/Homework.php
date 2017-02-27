<?php
namespace common\models;

use yii\db\ActiveRecord;
use yii\web\UploadedFile;
use Yii;

class Homework extends ActiveRecord
{
	public $file;

	public function rules()
	{
		return [
			[['title'],'required','message'=>'标题不能为空'],
			[['content'],'safe'],
			['deadline', 'required', 'message' => '截止时间不能为空'],
			[['fileName'], 'file', 'skipOnEmpty' => true, 
			 'extensions' => 'docx, doc, txt, xlsx, xls, docm, dotx, dotm, xlsm, xltx, xltm, xlsb, xlam, pptx, pptm, ppsx, potx, potm, ppam, pdf, xps, jpg, png, gif, bmp, jnt, rar, zip']
		];
	}

	public function attributeLabels()
	{
		return [
			'title' => '标题',
			'content' => '内容',
			'deadline' => '截止时间',
			'fileName' => '文件名',
		];
	}


    public function upload()
    {
        if(!empty($this->fileName)){
            $fileName = time().mt_rand(0,999999).'.'.$this->fileName->extension;
            $saveUrl = Yii::$app->params['homework'].'/'.$fileName;
            if($this->fileName->saveAs($saveUrl)){
                $this->second_name = $fileName;
                $this->fileName = $this->fileName->name;
                return true;
            }else{ 
                return false;           
            }
        } else {
            return true;
        }
    }
}
