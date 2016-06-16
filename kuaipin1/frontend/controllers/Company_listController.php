<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;

/**
 * Index controller
 */
class Company_listController extends Controller
{
	public $enableCsrfValidation=false;
	/* 去除Yii框架格式 */
	public $layout = false;

/**
 * 公司
 */
	public function actionIndex(){
		return $this->render('index.html');
	} 
}