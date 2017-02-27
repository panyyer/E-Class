<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

use common\models\SC;
use common\models\Student;

class StudentController extends Controller
{
    public $layout = "index";

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only'  => ['login','logout'],
                'rules' => [
                    [
                        'allow'   => true,
                        'actions' => ['login'],
                        'roles'   => ['?'],
                    ],
                    [
                        'allow'   => true,
                        'actions' => ['logout'],
                        'roles'   => ['@']
                    ],
                ],
                'denyCallback' => function () {
                    if(Yii::$app->user->isGuest)
                    {
                        return Yii::$app->getResponse()->redirect(["admin/login"]);
                    }else {
                        return Yii::$app->getResponse()->redirect(["index/main"]);
                    }
                }
            ],
        ];
    }

    public function actionShow()
    {
        $data = array();
        $pages = NULL;
        if(Yii::$app->request->isPost){
            $name = Yii::$app->request->post('keywords');
            $model = Student::find()->where('name LIKE :name', [':name' => '%'.$name.'%'])->all();
            foreach ($model as $key => $value) {
                $data[$key]['name'] = $value->name == NULL ? "未完善" : $value->name;
                $data[$key]['phone'] = $value->phone == NULL ? "未完善" : $value->phone;
                $data[$key]['sex'] = $value->sex == NULL ? "未完善" : $value->sex;
                $data[$key]['number'] = $value->number == NULL ? "未完善" : $value->number;
                $data[$key]['major'] = $value->major == NULL ? "未完善" : $value->major;
                $data[$key]['department'] = $value->department==NULL ? "未完善" : $value->department;
                $data[$key]['academy'] = $value->academy == NULL ? "未完善" : $value->academy;
                $data[$key]['school'] = $value->school == NULL ? "未完善" : $value->school;
                $data[$key]['province'] = $value->province == NULL ? "未完善" : $value->province;
            }
        } else {
            $cid = Yii::$app->request->get('id');
            $sc = SC::find()->where('cid=:cid', [':cid' => $cid])->all();
            foreach ($sc as $key => $value) {
                $stu = Student::find()->where('id=:id', [':id' => $value->sid])->one();
                $data[$key]['name'] = $stu->name == NULL ? "未完善" : $stu->name;
                $data[$key]['phone'] = $stu->phone == NULL ? "未完善" : $stu->phone;
                $data[$key]['sex'] = $stu->sex == NULL ? "未完善" : $stu->sex;
                $data[$key]['number'] = $stu->number == NULL ? "未完善" : $stu->number;
                $data[$key]['major'] = $stu->major == NULL ? "未完善" : $stu->major;
                $data[$key]['department'] = $stu->department==NULL ? "未完善" : $stu->department;
                $data[$key]['academy'] = $stu->academy == NULL ? "未完善" : $stu->academy;
                $data[$key]['school'] = $stu->school == NULL ? "未完善" : $stu->school;
                $data[$key]['province'] = $stu->province == NULL ? "未完善" : $stu->province;
            }            
        }

        return $this->render('show', ['model' => $data]);
    }

}