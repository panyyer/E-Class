<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use common\models\Teacher;
use common\models\Student;
use common\models\Token;
use common\models\Classes;
use common\models\SC;
use common\models\Notice;

class NoticeController extends Controller {
    public $result = ['status' => false, 'code' => 4004]; //未知错误
    public $success = ['status' => true, 'code' => 4000]; //成功
    public $notice_info = ['content', 'create_time', 'notReceived'];

	public function actionPublish()
	{
        $token = Yii::$app->request->post('access_token');
        $cid = Yii::$app->request->post('cid');
        $content = Yii::$app->request->post('content');
        $res = Token::Auth($token);
        if($res['status']) {
            if($res['token']->type == 1) {
            	$class = Classes::find()->where('id=:id', [':id'=>$cid])->one();
            	if(!empty($class) && $class->tid == $res['token']->uid) {
                	$sql = "SELECT st.* FROM Eclass_student st, Eclass_SC sc WHERE st.id=sc.sid AND sc.cid=".$cid;
                	$stu = Student::findBySql($sql)->all();
                	$data = array();
                	foreach ($stu as $key => $value) {
                		$data[] = $value->id;
                	}
	                $notice = new Notice();
	                $notice->cid = $cid;
	                $notice->content = $content;
	                $notice->notReceived = json_encode($data);
	                $notice->create_time = date("Y-m-d H:i:s", time());   
	                if($notice->save()) {
	                    $this->result = $this->success;
	                } else {
	                    $this->result = ['status' => false, 'code' => 4001];
	                }	
            	} else {
                	$this->result = ['status' => false, 'code' => 4003];
            	}
            } else {
                $this->result = ['status' => false, 'code' => 4003];
            } 
        } 
        return json_encode($this->result);		
	}

	public function actionRequestOne()
	{
        $token = Yii::$app->request->post('access_token');
        $nid = Yii::$app->request->post('nid'); 
        $res = Token::Auth($token);
        if($res['status'] == true) {
            $model = Notice::find()->where('id=:nid', [':nid'=>$nid])->one();
            if(!empty($model)) {
                //判断是否该班学生
                if($res['token']->type == 0) {
                	$flag = false;
                    $sc = SC::find()->where('cid=:cid', [':cid'=>$model->cid])->all();
                    foreach ($sc as $key => $value) {
                    	if($value->sid == $res['token']->uid) {
                    		$flag = true;
                    		break;
                    	}
                    }
                    if($flag) {
                    	//将该学生从'未接收通知'字段删去
                    	$notReceived = json_decode($model->notReceived);
                    	$index = array_search($res['token']->uid, $notReceived);
                    	if($index !== false) {
                    		array_splice($notReceived, $index, 1);
                    		$model->notReceived = json_encode($notReceived);
                    		$model->update();
                    	}

                        $this->result = $this->success;
                        foreach ($this->notice_info as $key => $value) {
                            $this->result[$value] = $model->$value;
                        }  
                        //存进数据库的notReceived是json格式，解码成数组
                        $this->result['notReceived'] = json_decode($model->notReceived);
                    } else {
                        $this->result = ['status' => false, 'code' => 4003];
                    }
                } else if($res['token']->type == 1) {
            		$class = Classes::find()->where('id=:id', [':id'=>$model->cid])->one();
                    if($class->tid == $res['token']->uid) {
                        $this->result = $this->success;
                        foreach ($this->notice_info as $key => $value) {
                            $this->result[$value] = $model->$value;
                        }
                        $this->result['notReceived'] = json_decode($model->notReceived);
                    } else {
                        $this->result = ['status' => false, 'code' => 4003];
                    }
                }
            } else {
                $this->result = ['status' => false, 'code' => 4008];
            }
        }
        return json_encode($this->result);		
	}

	//请求某个班级的所有通知
	public function actionRequestAll()
	{
      	$token = Yii::$app->request->post('access_token');
      	$cid = Yii::$app->request->post('cid');
        $res = Token::Auth($token);
        $notices = array();
        if($res['status'] == true) {
            if($res['token']->type == 0) {
            	$flag = false;
                $sc = SC::find()->where('cid=:cid', [':cid'=>$cid])->all();
                foreach ($sc as $key => $value) {
                	if($value->sid == $res['token']->uid) {
                		$flag = true;
                		break;
                	}
                }                
                if($flag) {
            		$model = Notice::find()->where('cid=:cid', [':cid'=>$cid])->all();
                    $this->result = $this->success;
                    foreach ($model as $key => $value) {
                        foreach ($this->notice_info as $k => $v) {
                            $notices[$key][$v] = $value->$v;
                        }
                        $notices[$key]['nid'] = $value->id;
                        $notices[$key]['notReceived'] = json_decode($value->notReceived);
                        $notices[$key]['name'] = array();
                        foreach (json_decode($value->notReceived) as $k => $v) {
                            $stu =  Student::find()->where('id=:id',[':id'=>$v])->one();
                            // var_dump($stu->name);exit;
                            if(!empty($stu)) 
                            $notices[$key]['name'][$k] = $stu->name == NULL ? "未完善" : $stu->name;
                        }
                    }            
                }
                $this->result['notices'] = $notices;
            } else if($res['token']->type == 1) {
                $check = Classes::find()->where('id=:cid',[':cid'=>$cid])->one();
                if(!empty($check) && $check->tid == $res['token']->uid) {
                	$model = Notice::find()->where('cid=:cid', [':cid'=>$cid])->all();
                    $this->result = $this->success;
                    foreach ($model as $key => $value) {
                        foreach ($this->notice_info as $k => $v) {
                            $notices[$key][$v] = $value->$v;
                        }
                        $notices[$key]['nid'] = $value->id;
                        //存进数据库的notReceived是json格式，解码成数组
                        $notices[$key]['notReceived'] = json_decode($value->notReceived);
                        $notices[$key]['name'] = array();
                        foreach (json_decode($value->notReceived) as $k => $v) {
                            $stu =  Student::find()->where('id=:id',[':id'=>$v])->one();
                            // var_dump($stu->name);exit;
                            if(!empty($stu)) 
                            $notices[$key]['name'][$k] = $stu->name == NULL ? "未完善" : $stu->name;
                        }
                    }                    
                	$this->result['notices'] = $notices;
                } else {
                    $this->result = ['status' => false, 'code' => 4003];
                }
            }
        }
        return json_encode($this->result);		
	}

}
