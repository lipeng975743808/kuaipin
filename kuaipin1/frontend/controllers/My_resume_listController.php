<?php
namespace frontend\controllers;

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
		return $this->render('daiding.html');
	}
/**
* 不合适简历
*/
	public function actionNo(){
		return $this->render('no.html');
	}
}