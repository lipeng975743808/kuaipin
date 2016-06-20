<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;

/**
 * Index controller
 */
class ResumeController extends Controller
{
	public $enableCsrfValidation=false;
	/* 去除Yii框架格式 */
	public $layout = false;

/**
 * 简历列表
 */
	public function actionList(){
		return $this->render('index.html');
	} 
/**
* 我要招人
*/
	public function actionAdd_people(){
		return $this->render('index.html');
	}
}