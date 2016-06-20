<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;

/**
 * Index controller
 */
class UserController extends Controller
{
	public $enableCsrfValidation=false;
	/* 去除Yii框架格式 */
	public $layout = false;

/**
* 账号设置
*/
	public function actionUpdate(){
		return $this->render('update.html');
	}
/**
* 密码设置
*/
	public function actionUpdatepwd(){
		return $this->render('updatepwd.html');
	}
}