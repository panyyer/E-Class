<?php
/**
 * Created by PhpStorm.
 * User: home
 * Date: 2016/11/26
 * Time: 16:19
 */

namespace backend\controllers;
use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;

use common\models\Classes;
use common\models\Student;
use common\models\Homework;
use common\models\SH;
use common\models\Sign;
use common\models\SS;
use common\models\SC;

use PHPExcel;
use PHPExcel_Writer_Excel2007;
use PHPExcel_Style_Border;
use PHPExcel_Style_Alignment;
use PHPExcel_IOFactory;
use PHPExcel_Style_Fill;

class ScoreController extends Controller
{
    public $layout='index';
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only'  => ['index','view'],
                'rules' => [
                    [
                        'allow'   => true,
                        'actions' => ['index','view'],
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

    public function actionIndex(){
        $cid = Yii::$app->request->get('id');
        $model = Classes::find()->where(['id'=>$cid,'tid'=>Yii::$app->user->id])->one();
        if(empty($model)) {
            return Yii::$app->getResponse()->redirect(["index/main"]);
        }
        $homework_ratio = $model->homework_ratio ;
        $attendance_ratio = $model->attendance_ratio;
        if(Yii::$app->request->post()) {
            if($_POST['homework'] + $_POST['attend'] != 100) {
                echo "<script>alert('两者比例和必须为100');</script>";
            } else {
                $model->homework_ratio = $_POST['homework'];
                $model->attendance_ratio = $_POST['attend'];
                $model->update();
                return $this->redirect(['score/index','id'=>$cid]);
            }
        }
        if($homework_ratio + $attendance_ratio == 0 ) {
            $homework_ratio = 50;$attendance_ratio = 50;//默认设置1:1
        }
        // $array='10,30,60,25,7';

        //计算平时分区间人数
        $score[] = array();
        $studen_num = 0;
        $stu = SC::find()->where('cid=:cid', [':cid'=>$cid])->all();
        //计算作业分
        $homework_num = Homework::find()->where('cid=:cid', [':cid'=>$cid])->count();
        if($homework_num != 0) {
            foreach ($stu as $key => $value) {
                $score[$key] = 0;
                $studen_num++;
                $sh = SH::find()->where('cid=:cid AND sid=:sid', [':cid'=>$cid,':sid'=>$value->sid])->all();
                if(empty($sh)) {
                    continue;
                }
                foreach ($sh as $k => $v) {
                    if($v->score >= 0) {
                        $score[$key] += $v->score; 
                    }
                }
                $score[$key] /= $homework_num; //作业平均分
                $score[$key] *= ($homework_ratio/100); //折算成平时分
            }
        }

        //计算考勤分
        $sign = Sign::find()->where('cid=:cid', [':cid'=>$cid])->all();
        $sign_num = Sign::find()->where('cid=:cid', [':cid'=>$cid])->count();
        if(!empty($sign)) {
            foreach ($stu as $key => $value) {
                if(empty($score[$key])) $score[$key] = 0;
                foreach ($sign as $k => $v) {
                    $ss = SS::find()->where('sign_id=:sign_id AND sid=:sid', [':sign_id'=>$v->id, ':sid'=>$value->sid])->one();
                    if(empty($ss) || $ss->status == 1) {
                        $score[$key] += ((double)(1/$sign_num))*$attendance_ratio;
                    } else if($ss->status == 2) {
                        //迟到给一半分
                        $score[$key] += ((double)(0.5/$sign_num))*$attendance_ratio;

                    }
                }
            }
        }
        //arr代表各个分数区间的人数，从高分递减
        $arr = [0,0,0,0,0];
        $maxm = 0;
        $minm = 999;
        $sum = 0;
        for($i=0;$i<count($score);$i++) {
            $sum += $score[$i];
            if($score[$i] > $maxm) $maxm = $score[$i];
            if($score[$i] < $minm) $minm = $score[$i];
            if($score[$i] < 60) {
                $arr[4]++;
            } else if($score[$i] > 59 && $score[$i] < 70){
                $arr[3]++;
            } else if($score[$i] > 69 && $score[$i] < 80){
                $arr[2]++;
            } else if($score[$i] > 79 && $score[$i] < 90){
                $arr[1]++;
            } else if($score[$i] > 89) {
                $arr[0]++;
            }
        }
        //arr拼接成字符串，格式需要
        $str = NULL;
        $cnt = array();
        for($i=0;$i<count($arr);$i++) {
            $cnt[$i] = $arr[$i];
            if($i!=count($arr)-1) {
                $str.=$arr[$i].',';
            } else {
                $str.=$arr[$i];
            }
            
        }
        // var_dump($str);exit;
        //求出班级最高分 平均分 最低分用于显示
        $cnt['maxm'] = $maxm;
        $cnt['minm'] = $minm;
        $cnt['aver'] = $studen_num > 0 ? $sum/$studen_num : 0;
        // $str='10,30,60,25,7';

        return $this->render('index',['homework'=>$homework_ratio,'attend'=>$attendance_ratio,'array'=>$str, 'cnt' => $cnt]);
    }
    public function actionView(){
        $cid = Yii::$app->request->get('id');
        $model = SC::find()->where('cid=:cid', [':cid'=>$cid])->all();
        if(empty($model)) {
            return Yii::$app->getResponse()->redirect(["index/main"]);
        }
        $cla = Classes::find()->where(['id'=>$cid,'tid'=>Yii::$app->user->id])->one();
        if(empty($cla)) {
            return Yii::$app->getResponse()->redirect(["index/main"]);
        }
        $homework_ratio = $cla->homework_ratio ;
        $attendance_ratio = $cla->attendance_ratio;      

        $data = array();
        $homework_num = Homework::find()->where('cid=:cid', [':cid'=>$cid])->count();       
        $sign = Sign::find()->where('cid=:cid', [':cid'=>$cid])->all();
        $sign_num = Sign::find()->where('cid=:cid', [':cid'=>$cid])->count();
        if($homework_num != 0) {
            foreach ($model as $key => $value) {
                //计算作业分
                $data[$key]['homework'] = 0;
                $sh = SH::find()->where('cid=:cid AND sid=:sid', [':cid'=>$cid,':sid'=>$value->sid])->all();
                if(!empty($sh)) {
                    foreach ($sh as $k => $v) {
                        if($v->score >= 0) {
                            $data[$key]['homework'] += $v->score; 
                        }
                    }                
                }
                $data[$key]['homework'] /= $homework_num; //作业平均分
       
                //计算考勤分
                $data[$key]['attend'] = 0;
                if(!empty($sign)) {
                    foreach ($sign as $k => $v) {
                        $ss = SS::find()->where('sign_id=:sign_id AND sid=:sid', [':sign_id'=>$v->id, ':sid'=>$value->sid])->one();
                        if(empty($ss) || $ss->status == 1) {
                            $data[$key]['attend'] += ((double)(1/$sign_num))*100;
                        } else if($ss->status == 2) {
                            //迟到给一半分
                            $data[$key]['attend'] += ((double)(0.5/$sign_num))*100;
                        }
                    }
                }

                //综合成绩
                $data[$key]['final'] = ($data[$key]['homework']*$homework_ratio + $data[$key]['attend']*$attendance_ratio)/100;

                //个人信息
                $stu = Student::find()->where('id=:id', [':id'=>$value->sid])->one();
                $data[$key]['name'] = $stu->name == NULL ? "未完善" : $stu->name; 
                $data[$key]['sex'] = $stu->sex == NULL ? "未完善" : $stu->sex; 
                $data[$key]['number'] = $stu->number == NULL ? "未完善" : $stu->number; 
                $data[$key]['major'] = $stu->major == NULL ? "未完善" : $stu->major; 
                $data[$key]['phone'] = $stu->phone;     
            }
        }
        return $this->render('view', ['data' => $data]);
    }

    public  function actionExport()
    {
        $objectPHPExcel = new PHPExcel();
        $objectPHPExcel->setActiveSheetIndex(0);
    
        $page_size = 50;
        $data = Yii::$app->request->get('data');
        $count = count($data);
        $page_count = (int)($count/$page_size) +1;
        $current_page = 0;
        $n = 0;
        foreach ( $data as $product )
        {
            if ( $n % $page_size === 0 )
            {
                $current_page = $current_page + 1;
    
                $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('A1','日期：'.date("Y年m月j日"));
                $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('H1','第'.$current_page.'/'.$page_count.'页');
                $objectPHPExcel->setActiveSheetIndex(0)->getStyle('H1')
                    ->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    
                //表格头的输出
                $objectPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
                $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('A2','姓名');
                $objectPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(6);
                $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('B2','性别');
                $objectPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
                $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('C2','学号');
                $objectPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
                $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('D2','手机');
                $objectPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
                $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('E2','专业');
                $objectPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12);
                $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('F2','作业成绩');
                $objectPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
                $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('G2','考勤成绩');
                $objectPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
                $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('H2','最终成绩');
                    
            }
            //明细的输出
            $objectPHPExcel->getActiveSheet()->setCellValue('A'.($n+3) ,$product['name']);
            $objectPHPExcel->getActiveSheet()->setCellValue('B'.($n+3) ,$product['sex']);
            $objectPHPExcel->getActiveSheet()->setCellValue('C'.($n+3) ,$product['number']);
            $objectPHPExcel->getActiveSheet()->setCellValue('D'.($n+3) ,$product['phone']);
            $objectPHPExcel->getActiveSheet()->setCellValue('E'.($n+3) ,$product['major']);
            $objectPHPExcel->getActiveSheet()->setCellValue('F'.($n+3),$product['homework']);
            $objectPHPExcel->getActiveSheet()->setCellValue('G'.($n+3) ,$product['attend']);
            $objectPHPExcel->getActiveSheet()->setCellValue('H'.($n+3) ,$product['final']);
            $objectPHPExcel->getActiveSheet()->getStyle('B'.($n+3).':h'.($n+3))
                           ->getAlignment()  ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $n = $n +1;    
        }
    
        //设置分页显示
        //$objectPHPExcel->getActiveSheet()->setBreak( 'I55' , PHPExcel_Worksheet::BREAK_ROW );
        //$objectPHPExcel->getActiveSheet()->setBreak( 'I10' , PHPExcel_Worksheet::BREAK_COLUMN );
        $objectPHPExcel->getActiveSheet()->getPageSetup()->setHorizontalCentered(true);
        $objectPHPExcel->getActiveSheet()->getPageSetup()->setVerticalCentered(false);
    
    
        ob_end_clean();
        ob_start();
    
        header('Content-Type : application/vnd.ms-excel');
        header('Content-Disposition:attachment;filename="'.'平时成绩表-'.date("Y年m月j日").'.xls"');
        $objWriter= PHPExcel_IOFactory::createWriter($objectPHPExcel,'Excel5');
        $objWriter->save('php://output');
    }
}