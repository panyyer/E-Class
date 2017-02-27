<?php
/**
 * Created by PhpStorm.
 * User: home
 * Date: 2016/11/26
 * Time: 16:19
 */

namespace backend\controllers;
use common\models\Notice;
use common\models\SC;
use Yii;
use yii\base\Exception;
use yii\web\Controller;
use yii\filters\AccessControl;
use common\models\Classes;
use common\models\Homework;
use common\models\SH;
use yii\web\UploadedFile;
use common\models\Student;
class IndexController extends Controller
{
    public $layout='index';
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only'  => ['main','index'],
                'rules' => [
                    [
                        'allow'   => true,
                        'actions' => ['main','index','manage'],
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
    // 课程列表
    public function actionMain(){
        $this->layout='main';
        $model=Classes::find()->where(['tid'=>Yii::$app->user->id])->all();
        $newClass=new Classes();
        if ($newClass->load(Yii::$app->request->post())) {
            $newClass->tid = Yii::$app->user->id;
            $newClass->create_time = date("Y-m-d H:i:s", time());
            $newClass->code = Classes::generateCode(6);
            if(!$newClass->save()) {
                echo "<script>alert('创建失败');</script>";
            }else{
                return $this->redirect(['student/show','id'=>$newClass->id]);
            }
        }
        return $this->render('main',['model'=>$model,'newClass'=>$newClass]);
    }
    //修改课程信息
    public function actionInfo($id){
        $model=Classes::find()->where(['id'=>$id,'tid'=>Yii::$app->user->id])->one();
        if(empty($model)) 
            return $this->redirect(['index/main']);
        if($model->load(Yii::$app->request->post())&&$model->save()){
            Yii::$app->getSession()->setFlash('succeedC','修改成功');
        }
        return $this->render('info',['model'=>$model]);
    }
//    删除课程
    public function actionDeleteClass($id){
        $class=Classes::find()->where(['id'=>$id,'tid'=>Yii::$app->user->id])->one();
        if(empty($class)) 
            return $this->redirect(['index/main']);
        $transaction=Yii::$app->db->beginTransaction();
        try{
            $class->delete();
            Homework::deleteAll(['cid'=>$id]);
            Notice::deleteAll(['cid'=>$id]);
            SC::deleteAll(['cid'=>$id]);
            SH::deleteAll(['cid'=>$id]);
            $transaction->commit();
            return $this->redirect(['index/main']);
        }catch (Exception $e){
            $transaction->rollBack();
            Yii::$app->getSession()->setFlash('failC','操作失败');
            echo "<script>history.go(-1)</script>";
        }

    }
    // 进入课程，发布作业
    public function actionIndex(){
        $cid = Yii::$app->request->get('id');
        $model = Classes::find()->where(['id'=>$cid,'tid'=>Yii::$app->user->id])->one();
        if(empty($model)){
            return $this->redirect(['index/main']);
        }
        $homework = new Homework();
        if ($homework->load(Yii::$app->request->post())) {
            $homework->fileName = UploadedFile::getInstance($homework, 'fileName');
            $homework->publish_time = date("Y-m-d H:i:s", time());
            $homework->cid = $cid;
            if($homework->upload() && $homework->save()) {
                Yii::$app->getSession()->setFlash('succeedH', "发布成功！");
            }
        } 
        return $this->render('index', ['homework' => $homework]);
    }
    //作业详情
    public function actionView($id,$hid){
        $class=Classes::find()->where(['id'=>$id,'tid'=>Yii::$app->user->id])->one();
        if(empty($class)){
            return $this->redirect(['index/main']);
        }
        $model = Homework::find()->where(['id'=>$hid,'cid'=>$id])->one();
        if(empty($model))
            return $this->redirect(['index/list','id'=>$id]);
        return $this->render('view',['model'=>$model]);
    }
    //修改作业
    public function actionModify($id,$hid){
        $class=Classes::find()->where(['id'=>$id,'tid'=>Yii::$app->user->id])->one();
        if(empty($class)){
            return $this->redirect(['index/main']);
        }
        $homework=Homework::find()->where(['id'=>$hid,'cid'=>$id])->one();
        if(empty($homework))return $this->redirect(['index/list','id'=>$id]);
        if($homework->load(Yii::$app->request->post())){
            $homework->fileName = UploadedFile::getInstance($homework, 'fileName');
            $homework->publish_time = date("Y-m-d H:i:s", time());
            $homework->cid = $id;
            if($homework->upload() && $homework->save()) {
                return $this->redirect(['index/view','id'=>$id,'hid'=>$hid]);
            }
        }
        return $this->render('modify',['homework'=>$homework]);
    }
//删除作业
public function actionDelete($id,$hid){
    $class=Classes::find()->where(['id'=>$id,'tid'=>Yii::$app->user->id])->one();
    if(empty($class)){
        return $this->redirect(['index/main']);
    }
    $homework=Homework::find()->where(['id'=>$hid,'cid'=>$id])->one();
    if(empty($homework))
        return $this->redirect(['index/list','id'=>$id]);
    $transaction=Yii::$app->db->beginTransaction();
    try{
        $homework->delete();
        SH::deleteAll(['hid'=>$hid]);
        $transaction->commit();
        return $this->redirect(['index/list','id'=>$id]);
    }catch (Exception $e){
        $transaction->rollBack();
        echo "<script>history.go(-1);alert('操作失败')</script>";
    }
}
//作业列表
    public function actionList() {
        $cid = Yii::$app->request->get('id');
        $class=Classes::find()->where(['id'=>$cid,'tid'=>Yii::$app->user->id])->one();
        if(empty($class)){
            return $this->redirect(['index/main']);
        }
        $model = Homework::find()->where('cid=:cid', [':cid'=>$cid])->orderBy(['id'=>SORT_DESC])->all();
        return $this->render('list', ['model' => $model]);
    }
    //批改作业
    public function actionCheck($id,$hid){
        $class=Classes::find()->where(['id'=>$id,'tid'=>Yii::$app->user->id])->one();
        if(empty($class)){
            return $this->redirect(['index/main']);
        }
        $homework=Homework::find()->where(['id'=>$hid,'cid'=>$id])->one();
        if(empty($homework))
            return $this->redirect(['index/list','id'=>$id]);
        $sql="select st.* from Eclass_student st,Eclass_SH sh where st.id=sh.sid and cid=".$id;
        $stu = Student::findBySql($sql)->all();
        $data = array();
        foreach ($stu as $key => $value) {
            if($value->name)$data[$value->id] = $value->name;
            else $data[$value->id] = $value->phone;
        }
        $model=SH::find()->where(['hid'=>$hid])->all();
        return $this->render('check',['model'=>$model,'data'=>$data]);
    }
    //批改一份作业
    public function actionCheckOne($id,$hid,$shid){

        // var_dump(Yii::$app->request->post('homework'));
        // exit;
        $class=Classes::find()->where(['id'=>$id,'tid'=>Yii::$app->user->id])->one();
        if(empty($class)){
            return $this->redirect(['index/main']);
        }
        $homework=Homework::find()->where(['id'=>$hid,'cid'=>$id])->one();
        if(empty($homework))
            return $this->redirect(['index/list','id'=>$id]);
        $one=SH::find()->where(['id'=>$shid,'hid'=>$hid])->one();
        if(empty($one)) {
            return $this->redirect(['index/check','id'=>$id,'hid'=>$hid]);
        }
        //如果为批改，则分数初始化为NULL
        $one->score = $one->score == -1 ? NULL : $one->score;
        if ($one->load(Yii::$app->request->post())) {
            if(!$one->save()) {
                echo "<script>alert('批改失败');</script>";
            }else{
                return $this->redirect(['index/check','id'=>$id,'hid'=>$hid]);
            }
        }
        $student=Student::find()->where(['id'=>$one->sid])->one();
        return $this->render('checkone',['one'=>$one,'student'=>$student]);
    }

    public function actionDownload() {
        $hid = Yii::$app->request->get('hid');
        $shid = Yii::$app->request->get('shid');
        if(!empty($hid)) {
            $model = Homework::find()->where('id=:id', [':id'=>$hid])->one();
        } else if(!empty($shid)) {
            $model = SH::find()->where('id=:id', [':id'=>$shid])->one();
        }
        if(!empty($model)) {
            $filePath = Yii::$app->params['homework'].'/'.$model->second_name;
            $fileName = $model->fileName;
            return Yii::$app->response->sendFile($filePath,$fileName);
        }
    } 
}