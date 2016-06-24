<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use app\models\KpWork;
use app\models\KpUser;
use app\models\KpCompanyRegister;

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