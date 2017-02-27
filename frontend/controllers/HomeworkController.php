<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use common\models\Teacher;
use common\models\Student;
use common\models\Token;
use common\models\Classes;
use common\models\Homework;
use common\models\SC;
use common\models\SH;
use frontend\models\SHforSchedule;

class HomeworkController extends Controller
{
    public $result = ['status' => false, 'code' => 4004]; //未知错误
    public $success = ['status' => true, 'code' => 4000]; //成功
    public $homework_info = ['title', 'content', 'deadline', 'publish_time', 'fileName'];
    
	//提交作业
	public function actionSubmit()
	{
       	$token = Yii::$app->request->post('access_token');
        $cid = Yii::$app->request->post('cid');
        $hid = Yii::$app->request->post('hid');

 		$res = Token::Auth($token);
        if($res['status']) {
            if($res['token']->type == 0) {
            	$class = SC::find()->where('cid=:cid AND sid=:sid', [':cid'=>$cid,':sid'=>$res['token']->uid])->one();
            	$homework = Homework::find()->where('id=:id', [':id'=>$hid])->one();
            	if(!empty($class) && !empty($homework)){
            		if(!empty($homework->deadline)) {
            			if(time() > strtotime($homework->deadline)) {
            				return json_encode(['status' => false, 'code' => 4009]);
            			}
            		}
            		$temp = SH::find()->where('hid=:hid AND sid=:sid', [':hid'=>$hid, ':sid'=>$res['token']->uid])->one();
            		if(!empty($temp)) {
            			$sh = $temp;
            			$sh->score = -1;
            			$sh->message = NULL;
            			$sh->fileName = NULL;
            			$fileUrl = Yii::$app->params['homework'].'/'.$sh->second_name;
			            if(is_file($fileUrl)){
			                unlink($fileUrl);
			            } 
            		} else {
						$sh = new SH();
            		}
	            	$sh->hid = $hid;
	            	$sh->cid = $cid;
	            	$sh->sid = $res['token']->uid;
	            	$sh->submit_time = date("Y-m-d H:i:s", time());
		            if(isset($_FILES['file']['name'])) {
		                //获取文件后缀
		                $extension = explode('.',$_FILES['file']['name']);
		                //文件重命名
		                $second_name = mt_rand(0,999999).time().'.'.$extension[1];
		                //获取文件暂存路径
		                $tmpUrl = $_FILES['file']['tmp_name'];
		                move_uploaded_file($tmpUrl, Yii::$app->params['homework'].'/'.$second_name);
		                $sh->second_name = $second_name;
		                $sh->fileName = $_FILES['file']['name'];
		            } else {
		            	return json_encode(['status' => false, 'code' => 4010]);
		            }
		            if($sh->save(false)) {
		            	$this->result = $this->success;
		            	$this->result['shid'] = $sh->id;
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

	//查看指定作业
	// public function actionRequestOne()
	// {
 //       	$token = Yii::$app->request->post('access_token');
 //        $hid = Yii::$app->request->post('hid');
 //        $res = Token::Auth($token);

 //      	if($res['status']) {
 //        	if($res['token']->type == 1) {
 //    			$homework = Homework::find()->where('id=:id', [':id'=>$hid])->one();
 //    			if(!empty($homework)) {
 //    				$class = Classes::find()->where('id=:id',[':id'=>$homework->cid])->one();
 //    				if(!empty($class) && $class->tid == $res['token']->uid) {
	// 	     			$this->result = $this->success;
 //                        foreach ($this->homework_info as $key => $value) {
 //                            $this->result[$value] = $homework->$value;
 //                        }
 //    				} else {
 //                		$this->result = ['status' => false, 'code' => 4003];
 //    				}
 // 				} else {
 //                	$this->result = ['status' => false, 'code' => 4008];
 // 				}
	//         } else if($res['token']->type == 0) {
 //    			$homework = Homework::find()->where('id=:id', [':id'=>$hid])->one();
 //    			if(!empty($homework)) {
 //        			$sc = SC::find()->where('sid=:sid', [':sid'=>$res['token']->uid])->one();
 //        			if(!empty($sc)) {
 //        				$this->result = $this->success;
 //                        foreach ($this->homework_info as $key => $value) {
 //                            $this->result[$value] = $homework->$value;
 //                        }
 //        			} else {
 //                		$this->result = ['status' => false, 'code' => 4003];
 //        			}
 //    			} else {
 //                	$this->result = ['status' => false, 'code' => 4008];
 //    			}
 //        	}
 //        }
 //        return json_encode($this->result);		
	// }

	//教师查看课程的所有作业
	public function actionRequestAll()
	{
       	$token = Yii::$app->request->post('access_token');
        $cid = Yii::$app->request->post('cid');
        $res = Token::Auth($token);
        $data = NULL;
        if($res['status']) {
        	if($res['token']->type == 1) {
        		$class = Classes::find()->where('id=:id', [':id'=>$cid])->one();
            	if(!empty($class) && $class->tid == $res['token']->uid) {
        			$homework = Homework::find()->where('cid=:cid', [':cid'=>$cid])->all();
	        		if(!empty($homework)) {
		        		foreach ($homework as $key => $value) {
	                        foreach ($this->homework_info as $k => $v) {
	                            $data[$key][$v] = $value->$v;
	                        }
	                        //hid在requestOne方法没有，额外处理
	                        $data[$key]['hid'] = $value->id; 
		        		}        			
	        		}
        			$this->result = $this->success;
	        		$this->result['homework'] = $data;
        		} else {
                	$this->result = ['status' => false, 'code' => 4003];
        		}
        	} else if($res['token']->type == 0) {
        		$sc = SC::find()->where('cid=:cid AND sid=:sid', [':cid'=>$cid,':sid'=>$res['token']->uid])->one();
        		if(!empty($sc)) {
        			$homework = Homework::find()->where('cid=:cid', [':cid'=>$cid])->all();
        			if(!empty($homework)) {
        				foreach ($homework as $key => $value) {
	                        foreach ($this->homework_info as $k => $v) {
	                            $data[$key][$v] = $value->$v;
	                        }
	                        $data[$key]['hid'] = $value->id;
        				}
        			}
        			$this->result = $this->success;
        			$this->result['homework'] = $data;
        		} else {
                	$this->result = ['status' => false, 'code' => 4003];
        		}
        	}
        }
        return json_encode($this->result);		
	}

	//布置作业
	public function actionPublish()
	{
        $token = Yii::$app->request->post('access_token');
        $cid = Yii::$app->request->post('cid');
        $title = Yii::$app->request->post('title');
        $content = Yii::$app->request->post('content');
        $deadline = Yii::$app->request->post('deadline');

  		$res = Token::Auth($token);
        if($res['status']) {
            if($res['token']->type == 1) {
            	$class = Classes::find()->where('id=:id', [':id'=>$cid])->one();
            	if(!empty($class) && $class->tid == $res['token']->uid) {
					$homework = new Homework();
	            	$homework->deadline = $deadline;
	            	$homework->title = $title;
	            	$homework->content = $content;
	            	$homework->publish_time = date("Y-m-d H:i:s", time());
	            	$homework->cid = $cid;
		            if(isset($_FILES['file']['name'])) {
		                //获取文件后缀
		                $extension = explode('.',$_FILES['file']['name']);
		                //文件重命名
		                $second_name = time().mt_rand(0,999999).'.'.$extension[1];
		                //获取文件暂存路径
		                $tmpUrl = $_FILES['file']['tmp_name'];
		                move_uploaded_file($tmpUrl, Yii::$app->params['homework'].'/'.$second_name);
		                $homework->second_name = $second_name;
		                $homework->fileName = $_FILES['file']['name'];
		            }
		            if($homework->save()) {
		            	$this->result = $this->success;
		            	$this->result['hid'] = $homework->id;
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

	public function actionDownload() {
        $token = Yii::$app->request->post('access_token');
        $hid = Yii::$app->request->post('hid');
        $shid = Yii::$app->request->post('shid');
 		$res = Token::Auth($token);
        if($res['status']) {
	        if(!empty($hid)) {
	        	$model = Homework::find()->where('id=:id', [':id'=>$hid])->one();
	        } else if(!empty($shid)) {
	        	$model = SH::find()->where('id=:id', [':id'=>$shid])->one();
	        }
	        if(!empty($model)) {
		    	$filePath = Yii::$app->params['homework'].'/'.$model->second_name;
		    	$fileName = $model->fileName;
		    	return Yii::$app->response->sendFile($filePath,$fileName);
	        } else {
	        	$this->result = ['status' => false, 'code' => 4011];
	        }
    	}
        return json_encode($this->result);
	} 

	//学生请求某课程的所有作业
	public function actionAllSubmit() {
        $token = Yii::$app->request->post('access_token');
        $cid = Yii::$app->request->post('cid');
 		$res = Token::Auth($token);
 		$data = NULL;
        if($res['status']) {
            if($res['token']->type == 0) {
        		$sc = SC::find()->where('cid=:cid AND sid=:sid', [':cid'=>$cid,':sid'=>$res['token']->uid])->one();
        		if(!empty($sc)) {
        			$homework = Homework::find()->where('cid=:cid',[':cid'=>$cid])->all();
					if(!empty($homework)) {
						$this->result = $this->success;
						foreach ($homework as $key => $value) {
							$sh = SH::find()->where('hid=:hid AND sid=:sid', [':hid'=>$value->id, ':sid' => $res['token']->uid])->one();
							$data[$key]['shid'] = 0;
							$data[$key]['score'] = -2;
							$data[$key]['message'] = NULL;
							$data[$key]['fileName2'] = NULL;
							$data[$key]['submit_time'] = NULL;
							if(!empty($sh)) {
								$data[$key]['shid'] = $sh->id;
								$data[$key]['score'] = $sh->score;
								$data[$key]['message'] = $sh->message;
								$data[$key]['fileName2'] = $sh->fileName;
								$data[$key]['submit_time'] = $sh->submit_time;
							}
    						$data[$key]['hid'] = $value->id;
    						foreach ($this->homework_info as $k => $v) {
    							$data[$key][$v] = $value->$v;
    						}
						}	
					}
					$this->result['homework'] = $data;
            	} else {
                	$this->result = ['status' => false, 'code' => 4003];
            	}
            } else {
                $this->result = ['status' => false, 'code' => 4003];
            } 
        }
        return json_encode($this->result);
	}

	//学生请求某课程提交过的单个作业
	// public function actionSingleSubmit() {
 //        $token = Yii::$app->request->post('access_token');
 //        $shid = Yii::$app->request->post('shid');
 // 		$res = Token::Auth($token);
 // 		$data = NULL;
 //        if($res['status']) {
 //            if($res['token']->type == 0) {
	// 			$sh = SH::find()->where('id=:id', [':id'=>$shid])->one();
	// 			if(!empty($sh)) {
	// 				$this->result = $this->success;
	// 				$this->result['score'] = $sh->score;
	// 				$this->result['message'] = $sh->message;
	// 				$this->result['fileName'] = $sh->fileName;
	// 				$this->result['submit_time'] = $sh->submit_time;
	// 			}
	// 			$this->result['submit'] = $data;
 //            } else {
 //                $this->result = ['status' => false, 'code' => 4003];
 //            } 
 //        }
 //        return json_encode($this->result);
	// }	

	// private function updateSchedule($hid, $uid) {
	// 	$model = SHforSchedule::find()->where('hid=:hid AND uid=:uid',[':hid'=>$hid, ':uid' => $uid])->one();
	// 	if(!empty($model)) {
	// 		$model->status = 1;
	// 	} else {
	// 		$model = new SHforSchedule();
	// 	}
 // 	}
}