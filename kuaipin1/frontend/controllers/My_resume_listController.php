<?php
namespace frontend\controllers;
header('content-type:text/html;charset=utf8');
use Yii;
use yii\web\Controller;

/**
 * Index controller
 */
class My_resume_listController extends Controller
{
	public $enableCsrfValidation=false;
	/* 去除Yii框架格式 */
	public $layout = false;

/**
 * 我的简历
 */
	public function actionIndex(){
		return $this->render('index.html');
	} 
/**
* 待定简历
*/
	public function actionDaiding(){
		//$sql = "select * from kp_resume_company as a join kp_company_info as b on a.ci_id=b.ci_id join kp_resume as c on a.re_id=c.re_id where status='1'";
		$sql = "select * from kp_resume_company as a join kp_resume as b on b.re_id=a.re_id join kp_user as c on b.u_id=c.u_id where status='1'";
		$list = Yii::$app->db->createCommand($sql)->queryAll();
		$count = count($list);
		return $this->render('daiding.html',['list'=>$list,'count'=>$count]);
	}
/**
* 不合适简历
*/
	public function actionNo(){
		$sql = "select * from kp_resume_company as a join kp_resume as b on b.re_id=a.re_id join kp_user as c on b.u_id=c.u_id where status='3'";
		$list = Yii::$app->db->createCommand($sql)->queryAll();
		$count = count($list);
		return $this->render('no.html',['list'=>$list,'count'=>$count]);
	}
/**
* 通知面试简历
*/
	public function actionTongzhi(){
		$sql = "select * from kp_resume_company as a join kp_resume as b on b.re_id=a.re_id join kp_user as c on b.u_id=c.u_id where status='2'";
		$list = Yii::$app->db->createCommand($sql)->queryAll();
		$count = count($list);
		return $this->render('tongzhi.html',['list'=>$list,'count'=>$count]);
	}
/**
 * 删除简历
 */
	public function actionDelete(){
		$id = $_POST['id'];
		$sql = "delete from kp_resume_company where id in ($id)";
		$info = Yii::$app->db->createCommand($sql)->execute();
		if($info){
			echo 1;
		}
	}
/**
* 修改简历
*/
	public function actionUpdate(){
		$times = $_POST['times'];
		$id = $_POST['id'];
		$sql1 = "update kp_resume_company set times='$times' where id='$id'";
		Yii::$app->db->createCommand($sql1)->execute();
		$sql = "update kp_resume_company set status='2' where id='$id'";
		$info = Yii::$app->db->createCommand($sql)->execute();
		if($info){
			echo 1;
		}
	}
/**
 * 改为不合适简历
 */
	public function actionUpdates(){
		$id = $_POST['id'];
		$sql = "update kp_resume_company set status='3' where id='$id'";
		$info = Yii::$app->db->createCommand($sql)->execute();
		if($info){
			echo 1;
		}
	}
/**
 * 批量改为不合适简历
 */
	public function actionUpdatess(){
		$id = $_POST['id'];
		$sql = "update kp_resume_company set status='3' where id in ($id)";
		$info = Yii::$app->db->createCommand($sql)->execute();
		if($info){
			echo 1;
		}
	}

}