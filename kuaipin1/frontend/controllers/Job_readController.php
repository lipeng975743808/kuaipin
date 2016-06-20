<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;

/**
 * Index controller
 */
class Job_readController extends Controller
{
	public $enableCsrfValidation=false;
	/* 去除Yii框架格式 */
	public $layout = false;

/**
 * 我收藏的职位
 */
	public function actionJob(){
		return $this->render('job.html');
	}
/**
* 我的订阅
*/
	public function actionRead(){
		return $this->render('read.html');
	}  
}