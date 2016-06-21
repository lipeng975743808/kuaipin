<?php

namespace frontend\controllers;
header('content-type:text/html;charset=utf-8');
use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;

class QiyeController extends \yii\web\Controller
{
		public $enableCsrfValidation=false;
	/* 去除Yii框架格式 */
	public $layout = false;
	public function actionIndex()
	{
		$sql="select * from qiye";
		$arr=Yii::$app->db->createcommand($sql)->queryAll();
		return $this->render('lists.php',['a'=>$arr]);
	}
	public function actionUpdates()
	{
		$id=$_GET['id'];
		$sql="select * from qiye where q_id=$id";
		$arr=Yii::$app->db->createcommand($sql)->queryAll();
		// print_R($arr);die;
		return $this->render('op.php',['a'=>$arr]);
	}
}
