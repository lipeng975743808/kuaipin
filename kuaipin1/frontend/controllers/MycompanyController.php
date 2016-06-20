<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use app\models\KpUser;
use app\models\KpCompanyRegister;

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

    function actionHead()
    {
        $session = Yii::$app->session;
        $arr['sess_uid'] = $session->get('user');
        $arr['sess_idc'] = $session->get('identity');

        if (!empty($arr['sess_uid']) && !empty($arr['sess_idc'])) {
            if ($arr['sess_idc'] == "company") {
                //查询出公司用户的邮箱
                $user_info = KpCompanyRegister::find()
                    ->where(['cr_id' => $arr['sess_uid']])
                    ->asArray()
                    ->one();
                $arr['uemail'] = $user_info['cr_email'];
            } else {
                //查询出个人用户的邮箱
                $user_info = KpUser::find()
                    ->where(['u_id' => $arr['sess_uid']])
                    ->asArray()
                    ->one();
                $arr['uemail'] = $user_info['user_email'];
            }
        }
        return $arr;
    }
}