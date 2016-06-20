<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;

/**
 * Index controller
 */
class JobController extends Controller
{
	public $enableCsrfValidation=false;
	/* 去除Yii框架格式 */
	public $layout = false;

/**
 *  发布职位
 */
	public function actionJob_make(){
		return $this->render('index.html');
	} 
/**
*  有效职位
*/
	public function actionPositions(){
		return $this->render('positions.html');
	} 
/**
*  已下线职位
*/
	public function actionNopositions(){
		return $this->render('positions.html');
	} 
}