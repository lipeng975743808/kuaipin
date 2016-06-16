<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;

/**
 * Index controller
 */
class My_resumeController extends Controller
{
	public $enableCsrfValidation=false;
	/* 去除Yii框架格式 */
	public $layout = false;

/**
 * 制作简历
 */
	public function actionMake(){
		return $this->render('index.html');
	} 
/**
* 预览简历
*/
	public function actionPreview(){
		return $this->render('preview.html');
	}
}