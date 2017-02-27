<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use backend\models\LoginForm;
use backend\models\Admin;
use yii\filters\AccessControl;
class AdminController extends Controller
{
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
    public function actionLogin()
    {
        $this->layout = "login";
        $loginForm=new LoginForm();
        if($loginForm->load(Yii::$app->request->post())&&$loginForm->login()){
            $user=Admin::find()->where('id=:id',[':id'=>Yii::$app->user->id])->one();
            // $sql = "select * from yh_admin where id =".Yii::$app->user->id;
            if($user->name)
                Yii::$app->session['ad_phone']=$user->name;
            else
                Yii::$app->session['ad_phone']=$loginForm->phone;
            return $this->redirect(['index/main']);
        }
        return $this->render('login',['loginForm'=>$loginForm]);
    }
    public function actionLogout(){
        Yii::$app->user->logout();
        return $this->redirect(['admin/login']);
    }
}