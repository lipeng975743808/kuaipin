<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;

/**
 * Index controller
 */
class MycompanyController extends Controller
{
	public $enableCsrfValidation=false;
	/* 去除Yii框架格式 */
	public $layout = false;
	
/**
 * 主页面
 */
	public function actionIndex(){
		return $this->render('index01.html');
	} 
/**
 * 公司
 */
	public function actionIndex02(){
		return $this->render('index02.html');
	}
/**
 * 我的简历
 */
	public function actionPositions(){
		return $this->render('positions.html');
	}  
/**
 * 发布职位
 */
	public function actionCreate(){
		return $this->render('create.html');
	} 
}